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

    // php artisan queue:work --tries=1 --timeout=200
    public function run(): void
    {
        $user = User::first();
        
        if (!$user) {
            return;
        }

        $entries = [
            [
                'user_id' => $user->id,
                'title' => 'Day 1: Internship Orientation and Tech Stack Integration',
                'entry_date' => '2026-02-05 08:00:00',
                'content' => "Weather Condition: Rainy (Typhoon Basyang).\n\nMorning Activities: My internship journey at iTech Media Logic officially commenced today. The morning started with administrative onboarding, where the staff assisted in setting up biometrics for attendance tracking. This was followed by a comprehensive orientation where I was introduced to the development team and assigned to the second-floor workstation.\n\nTechnical Tasking: Senior Developer Mr. Beboy Eyana briefed us on the company's official tech stack, which includes Laravel for the backend, Inertia.js as the bridge, Vue.js for the frontend, and MongoDB as the primary database. I spent the afternoon configuring my local development environment and studying the official documentation for Inertia-Laravel integration, focusing on how it eliminates the need for a client-side API while maintaining a SPA-like experience.",
            ],
            [
                'user_id' => $user->id,
                'title' => 'Day 2: Advanced CRUD Prototypes and Framework Synergy',
                'entry_date' => '2026-02-09 08:00:00',
                'content' => "Weather Condition: Clear (Typhoon Basyang has exited the PAR).\n\nActivities: Today was dedicated to mastering the communication between Vue.js and Laravel through Inertia.js. I developed a more advanced CRUD prototype to understand how Laravel's Eloquent models interact with MongoDB collections.\n\nTechnical Learning: I explored the concept of 'Persistent Layouts' in Inertia.js to ensure state is maintained across page navigations. I also practiced using Laravel's Resource classes to transform database results into streamlined JSON objects for the frontend. This exercise was crucial in understanding the lifecycle of a request in a modern monolith-SPA hybrid architecture.",
            ],
            [
                'user_id' => $user->id,
                'title' => 'Day 3: System Architecture Design for Journal Management',
                'entry_date' => '2026-02-10 08:00:00',
                'content' => "Weather Condition: Good.\n\nActivities: I conceptualized and initiated the development of the 'Journal Management System' (JMS). This system is designed to streamline the logging of daily OJT activities and automate report generation.\n\nTechnical Implementation: I designed the database schema to handle NoSQL relationships. I established a One-to-Many relationship between the 'User' and 'JournalEntry' models, and started drafting the 'Task' entity which will link specific daily accomplishments to journal logs. I implemented the backend controllers for the 'Home' and 'Entries' views and began building the frontend components using Vue's Composition API. By the end of the day, I successfully pushed the initial commit containing the base layouts and routing logic.",
            ],
            [
                'user_id' => $user->id,
                'title' => 'Day 4: Backend Logic and Data Persistence Strategies',
                'entry_date' => '2026-02-11 08:00:00',
                'content' => "Weather Condition: Sunny.\n\nActivities: Continued the development of the JMS with a focus on robust backend logic and data validation.\n\nTechnical Implementation: I implemented Laravel Form Requests to handle server-side validation for journal entries, ensuring that every log includes a title, date, and comprehensive content. I also integrated the 'jenssegers/mongodb' library patterns to handle the unique ObjectId requirements of MongoDB. On the frontend, I utilized Inertia's useForm hook to manage form state and handle asynchronous submission feedback, including error messages and success toasts. This ensures a seamless user experience without full-page reloads.",
            ],
            [
                'user_id' => $user->id,
                'title' => 'Day 5: UI/UX Refinement and Custom Component Architecture',
                'entry_date' => '2026-02-12 08:00:00',
                'content' => "Weather Condition: Partly Cloudy.\n\nActivities: Shifted focus toward the user interface and overall user experience (UX) of the JMS, aiming for a professional yet thematic design.\n\nTechnical Implementation: I developed reusable Vue.js components for the dashboard, including a 'JournalCard' with custom CSS animations and a 'TaskWidget' for real-time task tracking. I applied advanced CSS techniques to achieve the 'Ancient Parchment' aesthetic requested for the project, using linear gradients and custom box shadows. I also implemented a 'Seal Decree' save mechanism that provides a satisfying visual confirmation upon data persistence. The goal was to blend functionality with a premium, engaging aesthetic.",
            ],
            [
                'user_id' => $user->id,
                'title' => 'Day 6: Reporting Engine and Integration Testing',
                'entry_date' => '2026-02-13 08:00:00',
                'content' => "Weather Condition: Good.\n\nActivities: Focused on the core objective of the system: the Weekly Progress Report generation module. This is critical for administrative compliance and performance tracking.\n\nTechnical Implementation: I engineered a reporting engine that aggregates data from both the JournalEntries and Tasks collections based on the selected work week. I implemented logic to filter records by 'entry_date' and format the content into a professional document structure. I also spent time debugging the session persistence issue where users were being logged out across different tabs, which involved adjusting the 'SESSION_DRIVER' and 'SESSION_DOMAIN' configurations in the environment settings. The day concluded with a full integration test of the CRUD operations.",
            ],
        ];

        foreach ($entries as $entry) {
            JournalEntry::create($entry);
        }
    }
}
