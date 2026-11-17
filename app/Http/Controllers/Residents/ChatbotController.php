<?php

namespace App\Http\Controllers\Residents;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ChatbotController extends Controller
{
    public function index()
    {
        return view('resident.chatbot');
    }

    /**
     * Ginagamit ng dalawang route:
     *  - resident.chatbot.send  (naka-login na residente)
     *  - public.chatbot.ask     (landing page)
     */
    public function ask(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'question'       => 'required|string|max:2000',
            'history'        => 'nullable|array|max:12',
            'history.*.role' => 'required_with:history|in:user,bot',
            'history.*.text' => 'required_with:history|string|max:8000',
        ]);

        if ($v->fails()) {
            return response()->json([
                'answer' => 'Pakisulat nang mas maikli ang tanong (hanggang 2,000 characters).',
            ], 422);
        }

        // env() ay fallback lang kapag walang config/services.php entry
        $apiKey = config('services.gemini.key') ?: config('gemini.api_key') ?: env('GEMINI_API_KEY');
        $model  = config('services.gemini.model') ?: env('GEMINI_MODEL') ?: 'gemini-2.5-flash';

        if (!$apiKey) {
            Log::error('Gemini: walang API key. Ilagay ang GEMINI_API_KEY sa .env at i-run ang php artisan config:clear.');
            return response()->json([
                'answer' => 'Hindi pa available ang AI assistant ngayon. Pakisubukan mamaya.',
            ], 503);
        }

        // Buuin ang usapan (walang memory ang Gemini kada request)
        $contents = [];
        foreach (array_slice($request->input('history', []), -8) as $h) {
            $role = $h['role'] === 'user' ? 'user' : 'model';
            if (empty($contents) && $role === 'model') {
                continue; // dapat user ang unang mensahe
            }
            if (!empty($contents) && end($contents)['role'] === $role) {
                continue; // dapat salitan ang user/model
            }
            $contents[] = ['role' => $role, 'parts' => [['text' => mb_substr($h['text'], 0, 3000)]]];
        }
        // Dapat user ang huling nasa history bago ang bagong tanong
        if (!empty($contents) && end($contents)['role'] === 'user') {
            array_pop($contents);
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $request->input('question')]]];

        $payload = [
            'system_instruction' => ['parts' => [['text' => $this->systemPrompt()]]],
            'contents'           => $contents,
            'generationConfig'   => ['temperature' => 0.7, 'maxOutputTokens' => 8192],
        ];

        // Google Search para masagot din ang bagong balita, panahon, presyo, atbp.
        // Ang Search ay may SARILING quota; kapag ito ang bumagsak, magpapatuloy ang chat nang wala nito.
        $useSearch = !Cache::get('gemini_search_disabled', false);
        if ($useSearch) {
            $payload['tools'] = [['google_search' => new \stdClass()]];
        }

        // Subukan ang model sa .env muna; kapag quota (429), wala (404) o ayaw (403),
        // lumipat sa susunod na model para hindi tumigil ang chatbot.
        $models = array_values(array_unique(array_filter([
            $model,
            'gemini-3.8-flash',
            'gemini-3.5-flash-lite',
        ])));

        $response = null;
        $tried    = [];                        // sinubukang model at error nila (para sa debug)
        $deadline = microtime(true) + 50;      // hindi lalampas ng ~50s ang buong request
        set_time_limit(90);

        foreach ($models as $candidate) {
            if (microtime(true) > $deadline) {
                $tried[] = 'time limit reached';
                break;
            }

            $model   = $candidate;
            $attempt = $payload;
            $response = $this->attempt($apiKey, $model, $attempt, $tried);

            // Bumagsak (kahit 429 o timeout) habang naka-Search: ulitin nang wala nito
            if ((!$response || $response->failed()) && isset($attempt['tools'])) {
                unset($attempt['tools']);
                $response = $this->attempt($apiKey, $model, $attempt, $tried);

                if ($response && $response->successful()) {
                    Cache::put('gemini_search_disabled', true, now()->addMinutes(15));
                    unset($payload['tools']);
                }
            }

            if ($response && $response->successful()) {
                break;
            }

            // Ang 400 (maling key/payload) ay hindi maaayos ng ibang model
            if ($response && !in_array($response->status(), [429, 404, 403, 500, 503], true)) {
                break;
            }
        }

        if (!$response || $response->failed()) {
            $detail = 'key …' . substr($apiKey, -4) . "\n" . implode("\n", $tried);
            $status = $response ? $response->status() : 504;

            Log::error('Gemini failed', ['tried' => $tried]);

            return $status === 429
                ? response()->json(['answer' => $this->withDebug('Maraming gumagamit ngayon. Subukan ulit pagkalipas ng ilang segundo.', $detail)], 429)
                : response()->json(['answer' => $this->withDebug('Hindi makasagot ang AI ngayon. Pakisubukan ulit mamaya.', $detail)], 502);
        }

        $text = collect($response->json('candidates.0.content.parts', []))
            ->reject(fn ($p) => !empty($p['thought']))
            ->pluck('text')
            ->filter()
            ->implode('');

        if (trim($text) === '') {
            Log::warning('Gemini: walang laman ang sagot', [
                'model'        => $model,
                'blockReason'  => $response->json('promptFeedback.blockReason'),
                'finishReason' => $response->json('candidates.0.finishReason'),
            ]);

            return response()->json([
                'answer' => 'Pasensya na, hindi ko masagot iyan. Maaari mo bang ulitin sa ibang paraan?',
            ]);
        }

        return response()->json(['answer' => trim($text)]);
    }

    /** Isang tawag sa Gemini. Ibabalik ang Response, o null kapag timeout/walang koneksyon. */
    private function attempt(string $apiKey, string $model, array $payload, array &$tried): ?Response
    {
        $withSearch = isset($payload['tools']) ? ' +search' : '';

        try {
            $r = $this->callGemini($apiKey, $model, $payload);
        } catch (\Throwable $e) {
            $tried[] = $model . $withSearch . ' [timeout/network] ' . mb_substr($e->getMessage(), 0, 120);
            return null;
        }

        if ($r->failed()) {
            $tried[] = $model . $withSearch . ' [' . $r->status() . '] ' . mb_substr((string) $r->json('error.message'), 0, 140);
            Log::warning('Gemini attempt failed', ['model' => $model, 'search' => $withSearch !== '', 'status' => $r->status(), 'body' => mb_substr($r->body(), 0, 500)]);
        }

        return $r;
    }

    private function callGemini(string $apiKey, string $model, array $payload): Response
    {
        return Http::withHeaders(['x-goog-api-key' => $apiKey])
            ->acceptJson()
            ->connectTimeout(8)
            ->timeout(25)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", $payload);
    }

    /** Ipakita ang totoong error sa chat kapag APP_DEBUG=true lang (para madaling ayusin). */
    private function withDebug(string $message, ?string $detail): string
    {
        if (config('app.debug') && $detail) {
            return $message . "\n\n[debug] " . mb_substr($detail, 0, 900);
        }
        return $message;
    }

    private function systemPrompt(): string
    {
        // Kunin ang totoong petsa at oras sa Pilipinas
        $currentTime = now()->setTimezone('Asia/Manila')->format('l, F j, Y - h:i A');

        return <<<PROMPT
Ikaw si "BIMS AI Assistant", isang matalino at versatile na AI assistant na binuo para sa Barangay Bactad East, Urdaneta City, Pangasinan. Gumagana ka tulad ng ChatGPT: sumasagot ka sa KAHIT ANONG tanong (Math, Science, History, Coding, Cooking, Health tips, Trivia, Translation, Pagsusulat, Homework, payo, at iba pa), at mayroon ka ring espesyal na kaalaman tungkol sa barangay.

ESTILO:
- Sagutin sa parehong wika ng user (Tagalog, English, o Taglish). Magalang, mainit, at natural ang tono.
- Direkta sa punto. Maikli para sa simpleng tanong, detalyado at hakbang-hakbang para sa komplikado.
- Gumamit ng simpleng Markdown kapag nakakatulong: **bold**, listahan na may "-" o "1.", at ``` para sa code. Huwag gumamit ng tables o malalaking heading.
- Alalahanin ang naunang usapan at sagutin ang follow-up nang may konteksto.

REAL-TIME INFO:
- Ang kasalukuyang petsa at oras (Asia/Manila) ay: {$currentTime}
- Para sa balita, panahon, presyo, o anumang bagong impormasyon, gamitin ang Google Search kung available.

BARANGAY KNOWLEDGE (gamitin ito para sa mga tanong tungkol sa Bactad East):
- Itinatag noong 1964. Si Protacio Suyat ang unang Barangay Captain. Dating bahagi ng Bactad Proper (ngayon ay Barangay P.T. Orata).
- Lokasyon: Silangang bahagi ng Urdaneta City, mga pitong (7) kilometro mula sa City Proper.
- Barangay Hall: Bactad East, Urdaneta City, Pangasinan 2428.
- Email: bactadeast@urdaneta.gov.ph
- Oras ng Opisina: Lunes hanggang Biyernes, 8:00 AM - 5:00 PM.
- Serbisyo: Online request ng Barangay Clearance, Certificate of Residency, Certificate of Indigency, at paghiram ng equipment (upuan, tent, sound system) — lahat sa pamamagitan ng BIMS pagkatapos mag-login.
- Emergency Hotlines: Barangay (075) 123 4567, Hospital (075) 568 9012, BFP (075) 522 2345, PNP 0998 123 4567, National 911.
- Mga Opisyal: Punong Barangay Erwin E. Aduca; Kagawad: John R. Desamito, Herbert F. Fang, Reydendo B. Latonio, Orly A. Baybayan, Alvin A. Cuchapin, Henry L. Latonio, Francis Fernandez; SK Chairperson Thenmarck Dulos; Barangay Secretary Angelito Navarro.

PAANO SUMAGOT SA "PAANO MAG-REQUEST NG DOKUMENTO":
Kapag ang tanong ay tungkol sa pag-request ng dokumento (Barangay Clearance, Certificate of Residency, Certificate of Indigency, o katulad), sumagot gamit ang eksaktong steps na ito — walang bayad, walang exact amount, walang extra note:

1. Mag-login gamit ang iyong account sa BIMS.
2. Pumunta sa **Document Issuance** section (o "Requesting Documents").
3. Piliin ang dokumentong nais i-request (hal. Barangay Clearance, Certificate of Residency, Certificate of Indigency).
4. Punan ang online form at isumite ang request.
5. Hintayin ang proseso ng barangay at kunin ang dokumento sa Barangay Hall kapag ready na.

Gamitin ang parehong format para sa lahat ng uri ng dokumento. Huwag mag-imbento ng presyo, bayad, o ibang detalye na wala sa listahan.

MGA PATAKARAN:
- Para sa mga tanong tungkol sa barangay (lokasyon, opisyal, kasaysayan, serbisyo, steps sa pag-request), sundin ang BARANGAY KNOWLEDGE at ang steps sa itaas. Huwag mag-imbento ng fees, numero, pangalan, o patakaran.
- Para sa lahat ng iba pang tanong, sumagot nang buo at tapat gamit ang iyong kaalaman. Huwag tumanggi sa tanong na hindi tungkol sa barangay.
- Kung hindi ka sigurado, aminin ito sa halip na manghula.
- Sa medikal, legal, o pinansyal na tanong, magbigay ng malinaw na pangkalahatang impormasyon at magmungkahi ng propesyonal kung kinakailangan.
- Huwag humingi ng sensitibong personal na impormasyon (ID numbers, password, OTP, bank details).
- Tumanggi nang magalang sa mapanganib o ilegal na hiling.
PROMPT;
    }
}