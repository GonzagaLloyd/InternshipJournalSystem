<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\JournalEntry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JournalEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        JournalEntry::create([
            'user_id' => $user->id,
            'title' => 'Day 1: Internship Orientation and Tech Stack Integration',
            'content' => 'Weather Condition: Rainy (Typhoon Basyang). Morning Activities: Upon arriving at iTech Media Logic, the morning began with our administrative onboarding. The staff assisted us in setting up our biometrics to officially track our attendance (Time In/Out and Break sessions). This was followed by an orientation and introduction session. The company staff introduced themselves, and we reciprocated by introducing ourselves individually to the team. Afterward, we were transitioned to the second floor, which will serve as our dedicated workstation for the duration of the internship. Technical Tasking: Our Senior Developer, Mr. Beboy Eyana, assigned our first primary task: familiarizing ourselves with the company’s official tech stack. The stack consists of: Backend: Laravel, Frontend: Inertia.js with Vue.js, Database: MongoDB. Accomplishments: I spent the remainder of the day studying the official documentation for these technologies. To apply what I learned, I began developing a simple CRUD (Create, Read, Update, Delete) project to get comfortable with the syntax and the integration of Inertia with Vue.',
            'image' => 'image1.jpg',
            'video' => 'video1.mp4',
            'audio' => 'audio1.mp3',
            'file' => 'file1.pdf',
            'entry_date' => '2026-02-05',
        ]);            
    }
}
