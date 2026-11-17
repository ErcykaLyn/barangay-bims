<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FamilyMember;
use App\Models\Resident;

class SyncDeceasedStatus extends Command
{
    // Ito yung command na itatype mo sa terminal
    protected $signature = 'app:sync-deceased';

    // Ito yung description na lalabas kapag nag `php artisan list` ka
    protected $description = 'I-sync ang is_deceased status mula sa residents table papuntang family_members table';

    public function handle()
    {
        $this->info('Sinisimulan ang pag-sync ng deceased status...');

        // Kunin lahat ng family members na buhay pa (is_deceased = false)
        $livingMembers = FamilyMember::where('is_deceased', false)->get();
        
        $updatedCount = 0;

        foreach ($livingMembers as $member) {
            // Hanapin yung katumbas na resident base sa pangalan at birthday
            $resident = Resident::where('first_name', $member->first_name)
                ->where('last_name', $member->last_name)
                ->when($member->date_of_birth, function($q) use ($member) {
                    return $q->where('date_of_birth', $member->date_of_birth);
                })
                ->where('is_deceased', true) // Kung deceased na sa residents table
                ->first();

            if ($resident) {
                $member->update([
                    'is_deceased' => true,
                    'date_of_death' => $resident->date_of_death,
                ]);
                $this->line("Na-update: {$member->first_name} {$member->last_name}");
                $updatedCount++;
            }
        }

        $this->info("Tapos na! {$updatedCount} family members ang na-sync as deceased.");
    }
}