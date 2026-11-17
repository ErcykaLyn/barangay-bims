@php
    // Logo: i-embed sa mismong email (CID) para lumabas kahit nasa localhost ang APP_URL.
    // Kapag walang $message (hal. view preview), gagamit ng asset() bilang fallback.
    $logoPath = public_path('images/brgy_logo.png.jpg');
    $logoSrc  = (isset($message) && is_file($logoPath))
        ? $message->embed($logoPath)
        : asset('images/brgy_logo.png.jpg');

    $resetUrl   = route('password.reset.form', $resetRequest->id);
    $approvedAt = now()->timezone('Asia/Manila')->format('F j, Y · g:i A');
@endphp
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>Your password reset is approved</title>
    <!--[if mso]>
    <noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
    <style>
        table, td, div, h1, h2, h3, p, a, span { font-family: Arial, Helvetica, sans-serif !important; }
        table { border-collapse: collapse; }
    </style>
    <![endif]-->
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        a { text-decoration: none; }
        a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; }

        .btn:hover { background-color: #047857 !important; }
        .link:hover { text-decoration: underline !important; }

        @media only screen and (max-width: 620px) {
            .container { width: 100% !important; max-width: 100% !important; }
            .outer-pad { padding: 16px 10px !important; }
            .px { padding-left: 22px !important; padding-right: 22px !important; }
            .hero-title { font-size: 26px !important; line-height: 32px !important; }
            .stack { display: block !important; width: 100% !important; box-sizing: border-box; padding-left: 0 !important; padding-right: 0 !important; }
            .stack-gap { padding-bottom: 10px !important; }
            .btn-full { display: block !important; width: 100% !important; box-sizing: border-box; }
            .hide-sm { display: none !important; max-height: 0 !important; overflow: hidden !important; }
            .detail-label { display: block !important; width: 100% !important; padding-bottom: 2px !important; }
            .detail-value { display: block !important; width: 100% !important; text-align: left !important; }
        }

        @media (prefers-color-scheme: dark) {
            .bg-page   { background-color: #0b1210 !important; }
            .bg-card   { background-color: #111b17 !important; }
            .bg-soft   { background-color: #16241f !important; }
            .bg-alert  { background-color: #0f2a21 !important; border-color: #10b981 !important; }
            .t-ink     { color: #f1f5f9 !important; }
            .t-body    { color: #cbd5e1 !important; }
            .t-muted   { color: #94a3b8 !important; }
            .t-accent  { color: #34d399 !important; }
            .bd        { border-color: #1f3a30 !important; }
        }
    </style>
</head>
<body class="bg-page" style="margin:0; padding:0; background-color:#eef2f1; font-family:'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica Neue', Arial, sans-serif; -webkit-font-smoothing:antialiased;">

    <!-- Preheader (hidden preview text) -->
    <div style="display:none; font-size:1px; line-height:1px; max-height:0; max-width:0; opacity:0; overflow:hidden; mso-hide:all;">
        Hi {{ $resident->first_name }}, your password reset request is approved. Set a new password within 24 hours.
        &#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;
    </div>

    <table role="presentation" class="bg-page" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#eef2f1;">
        <tr>
            <td align="center" class="outer-pad" style="padding:32px 16px;">

                <!--[if mso]><table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="620"><tr><td><![endif]-->
                <table role="presentation" class="container" border="0" cellpadding="0" cellspacing="0" width="620" style="width:620px; max-width:620px;">

                    <!-- Top label -->
                    <tr>
                        <td align="center" style="padding:0 0 14px 0;">
                            <span class="t-muted" style="font-size:11px; letter-spacing:0.18em; text-transform:uppercase; color:#64748b; font-weight:700;">Barangay Information Management System</span>
                        </td>
                    </tr>

                    <!-- Card -->
                    <tr>
                        <td class="bg-card" style="background-color:#ffffff; border-radius:20px; overflow:hidden; box-shadow:0 24px 48px -24px rgba(6,78,59,0.35);">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">

                                <!-- ============ HERO ============ -->
<tr><td align="center" bgcolor="#047857" class="px" style="background-color:#047857;background-image:linear-gradient(135deg,#065f46 0%,#047857 45%,#0d9488 100%);padding:40px 32px 56px;">
<table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center"><tr><td align="center" width="72" height="72" bgcolor="#ffffff" style="width:72px;height:72px;background-color:#ffffff;border-radius:50%;box-shadow:0 10px 24px rgba(0,0,0,0.22);">
<img src="{{ $logoSrc }}" width="52" height="52" alt="Barangay Bactad East" style="display:block;width:52px;height:52px;margin:10px auto;border-radius:50%;object-fit:contain;"></td></tr></table>
<p style="margin:16px 0 0 0;font-size:12px;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;color:#a7f3d0;">Barangay Bactad East</p>
<p style="margin:4px 0 0 0;font-size:13px;color:#d1fae5;">Urdaneta City, Pangasinan</p></td></tr>
<tr><td align="center" style="padding:0;"><table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center" style="margin-top:-28px;"><tr><td align="center" width="56" height="56" bgcolor="#10b981" style="width:56px;height:56px;background-color:#10b981;border-radius:50%;border:5px solid #ffffff;color:#ffffff;font-size:24px;line-height:46px;">&#128273;</td></tr></table></td></tr>
<!-- ============ TITLE ============ -->
<tr><td align="center" class="px" style="padding:20px 40px 4px;">
<span style="display:inline-block;padding:5px 14px;border-radius:999px;background-color:#d1fae5;color:#065f46;font-size:11px;font-weight:800;letter-spacing:0.14em;text-transform:uppercase;">Request Approved</span>
<h1 class="hero-title t-ink" style="margin:16px 0 0 0;font-size:30px;line-height:36px;font-weight:800;letter-spacing:-0.02em;color:#0f172a;">Hi {{ $resident->first_name }}, you can reset your password</h1>
<p class="t-body" style="margin:14px 0 0 0;font-size:16px;line-height:26px;color:#475569;">The Barangay Admin has <strong style="color:#059669;">approved your password reset request</strong>. Use the button below to choose a new password for your BIMS account.</p></td></tr>
<!-- ============ CTA ============ -->
<tr><td align="center" class="px" style="padding:28px 40px 8px;">
<!--[if mso]>
<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $resetUrl }}" style="height:54px;v-text-anchor:middle;width:260px;" arcsize="22%" stroke="f" fillcolor="#059669"><w:anchorlock/><center style="color:#ffffff;font-family:Arial,sans-serif;font-size:16px;font-weight:bold;">Reset my password &rarr;</center></v:roundrect>
<![endif]-->
<!--[if !mso]><!-- -->
<a href="{{ $resetUrl }}" class="btn btn-full" target="_blank" style="display:inline-block;background-color:#059669;background-image:linear-gradient(135deg,#10b981 0%,#059669 55%,#047857 100%);color:#ffffff;font-size:16px;font-weight:700;line-height:54px;height:54px;padding:0 38px;border-radius:12px;text-align:center;box-shadow:0 14px 24px -10px rgba(5,150,105,0.7);">Reset my password &rarr;</a>
<!--<![endif]-->
<p class="t-muted" style="margin:14px 0 0 0;font-size:13px;color:#94a3b8;">Approved on {{ $approvedAt }}</p></td></tr>
<!-- ============ EXPIRY ============ -->
<tr><td class="px" style="padding:26px 40px 0;"><table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#fffbeb;border:1px solid #fde68a;border-left:4px solid #f59e0b;border-radius:12px;"><tr>
<td width="44" valign="top" style="padding:16px 0 16px 18px;font-size:22px;line-height:24px;">&#9203;</td>
<td valign="top" style="padding:16px 18px 16px 8px;"><p style="margin:0 0 2px 0;font-size:14px;font-weight:800;color:#92400e;">This link expires in 24 hours</p><p style="margin:0;font-size:13px;line-height:21px;color:#b45309;">For your security, please complete the reset within 24 hours. After that, you will need to submit a new request.</p></td></tr></table></td></tr>
<!-- ============ STEPS ============ -->
<tr><td class="px" style="padding:32px 40px 0;">
<h2 class="t-ink" style="margin:0 0 16px 0;font-size:19px;line-height:26px;font-weight:800;color:#0f172a;">How it works</h2>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr><td valign="top" width="44" style="padding-bottom:14px;"><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td align="center" width="32" height="32" bgcolor="#d1fae5" style="width:32px;height:32px;border-radius:50%;background-color:#d1fae5;color:#065f46;font-size:14px;font-weight:800;">1</td></tr></table></td>
<td valign="top" style="padding-bottom:14px;"><p class="t-ink" style="margin:0 0 2px 0;font-size:15px;font-weight:800;color:#0f172a;">Click &ldquo;Reset my password&rdquo;</p><p class="t-body" style="margin:0;font-size:13px;line-height:21px;color:#64748b;">Open the secure page using the button above.</p></td></tr>
<tr><td valign="top" width="44" style="padding-bottom:14px;"><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td align="center" width="32" height="32" bgcolor="#d1fae5" style="width:32px;height:32px;border-radius:50%;background-color:#d1fae5;color:#065f46;font-size:14px;font-weight:800;">2</td></tr></table></td>
<td valign="top" style="padding-bottom:14px;"><p class="t-ink" style="margin:0 0 2px 0;font-size:15px;font-weight:800;color:#0f172a;">Enter and confirm a new password</p><p class="t-body" style="margin:0;font-size:13px;line-height:21px;color:#64748b;">Choose a strong one: at least 8 characters, mixed with numbers or symbols.</p></td></tr>
<tr><td valign="top" width="44" style=""><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td align="center" width="32" height="32" bgcolor="#d1fae5" style="width:32px;height:32px;border-radius:50%;background-color:#d1fae5;color:#065f46;font-size:14px;font-weight:800;">3</td></tr></table></td>
<td valign="top" style=""><p class="t-ink" style="margin:0 0 2px 0;font-size:15px;font-weight:800;color:#0f172a;">Log in again</p><p class="t-body" style="margin:0;font-size:13px;line-height:21px;color:#64748b;">Return to the login page and sign in with your email and new password.</p></td></tr>
</table></td></tr>
<!-- ============ FALLBACK LINK ============ -->
<tr><td class="px" style="padding:26px 40px 0;"><table role="presentation" class="bg-soft bd" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;"><tr><td style="padding:14px 18px;">
<p class="t-muted" style="margin:0 0 4px 0;font-size:12px;color:#64748b;">Button not working? Copy and paste this link into your browser:</p>
<a href="{{ $resetUrl }}" class="link t-accent" style="font-size:12px;line-height:18px;color:#059669;word-break:break-all;">{{ $resetUrl }}</a></td></tr></table></td></tr>
<!-- ============ SECURITY ============ -->
<tr><td class="px" style="padding:26px 40px 0;"><table role="presentation" class="bg-alert" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f0fdf4;border:1px solid #bbf7d0;border-left:4px solid #10b981;border-radius:12px;"><tr>
<td width="44" valign="top" style="padding:16px 0 16px 18px;font-size:22px;line-height:24px;">&#128274;</td>
<td valign="top" style="padding:16px 18px 16px 8px;"><p class="t-accent" style="margin:0 0 2px 0;font-size:14px;font-weight:800;color:#065f46;">Didn&rsquo;t request this?</p><p class="t-body" style="margin:0;font-size:13px;line-height:21px;color:#047857;">Ignore this email and contact the Barangay Hall right away. Never share this link or your password with anyone.</p></td></tr></table></td></tr>
<!-- ============ HELP ============ -->
<tr><td class="px" style="padding:30px 40px 36px;"><table role="presentation" class="bd" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top:1px solid #e2e8f0;"><tr><td style="padding-top:24px;">
<p class="t-ink" style="margin:0 0 10px 0;font-size:15px;font-weight:800;color:#0f172a;">Need help?</p>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"><tr>
<td class="stack stack-gap t-body" valign="top" style="font-size:13px;line-height:22px;color:#475569;padding-right:12px;">&#128205; Barangay Hall, Bactad East,<br>Urdaneta City, Pangasinan 2428</td>
<td class="stack t-body" valign="top" style="font-size:13px;line-height:22px;color:#475569;">&#9993; <a href="mailto:bactadeast@urdaneta.gov.ph" class="link t-accent" style="color:#059669;font-weight:700;">bactadeast@urdaneta.gov.ph</a><br>&#128337; Mon&ndash;Fri, 8:00 AM &ndash; 5:00 PM</td></tr></table></td></tr></table></td></tr>
<!-- ============ FOOTER BAND ============ -->
<tr><td class="px" align="center" style="background-color:#0f172a;padding:26px 32px;"><p style="margin:0;font-size:13px;line-height:20px;color:#cbd5e1;">If you didn&rsquo;t request a password reset, please contact the Barangay Hall immediately.</p></td></tr>
</table>
</td>
</tr>

<!-- Sub-footer -->
                    <tr>
                        <td align="center" style="padding:22px 16px 8px;">
                            <p class="t-muted" style="margin:0; font-size:12px; line-height:19px; color:#94a3b8;">
                                This is an automated message. Please do not reply directly to this email.
                            </p>
                            <p class="t-muted" style="margin:6px 0 0 0; font-size:12px; line-height:19px; color:#94a3b8;">
                                &copy; {{ date('Y') }} Barangay Bactad East, Urdaneta City, Pangasinan. All rights reserved.
                            </p>
                            <p style="margin:6px 0 0 0; font-size:11px; color:#a8b3c2;" class="t-muted">
                                Powered by <strong style="color:#64748b;">PSU-Asingan BSIT</strong>
                            </p>
                        </td>
                    </tr>
                </table>
                <!--[if mso]></td></tr></table><![endif]-->

            </td>
        </tr>
    </table>
</body>
</html>