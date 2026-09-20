<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default teacher first if none exist
        $teacher = User::where('role', 'teacher')->where('approval_status', 'approved')->first();

        if (! $teacher) {
            $teacher = User::create([
                'name' => 'Default Teacher',
                'email' => 'teacher@example.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'approval_status' => 'approved',
            ]);
        }

        // Create default sections
        $sectionNames = [
            'Section 1',
            'Section 2',
            'Section 3',
        ];

        foreach ($sectionNames as $name) {
            Section::firstOrCreate(
                ['name' => $name],
                ['teacher_id' => $teacher->id]
            );
        }
    }
}
