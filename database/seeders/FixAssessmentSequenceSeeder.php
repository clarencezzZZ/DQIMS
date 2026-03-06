<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\AssessmentSequence;

class FixAssessmentSequenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the highest assessment number from existing assessments
        $highestAssessment = DB::table('assessments')
            ->whereRaw("assessment_number LIKE '2026-03-%'")
            ->orderByRaw('CAST(SUBSTRING_INDEX(assessment_number, \'-\', -1) AS UNSIGNED) DESC')
            ->first();

        if ($highestAssessment) {
            // Extract the sequence number from the assessment number
            $parts = explode('-', $highestAssessment->assessment_number);
            $highestNumber = isset($parts[2]) ? intval($parts[2]) : 0;

            // Update or create the sequence record for year 2026
            AssessmentSequence::updateOrCreate(
                ['year_month' => '2026'],
                ['current_value' => $highestNumber]
            );

            echo "Fixed sequence: Set current_value to {$highestNumber} based on highest assessment number: {$highestAssessment->assessment_number}\n";
        } else {
            echo "No assessments found for 2026-03\n";
        }

        // Delete the old format record if it exists (2026-03)
        AssessmentSequence::where('year_month', '2026-03')->delete();
        echo "Deleted old sequence record for 2026-03\n";
    }
}
