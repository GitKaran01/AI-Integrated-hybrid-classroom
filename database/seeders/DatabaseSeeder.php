<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Topic;
use App\Models\TopicRating;
use App\Models\Attendance;
use App\Models\TeacherStatus;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 🔹 Teachers
        Teacher::factory(5)->create();

        // 🔹 Classrooms
        Classroom::factory(3)->create();

        // 🔹 Students
        Student::factory(50)->create();

        // 🔹 Topics
        Topic::factory(20)->create();

        // 🔥 Topic Ratings
        for ($i = 0; $i < 100; $i++) {
            TopicRating::create([
                'teacher_id' => Teacher::inRandomOrder()->first()->id,
                'classroom_id' => Classroom::inRandomOrder()->first()->id,
                'topic_id' => Topic::inRandomOrder()->first()->id,
                'rating' => rand(1, 5),
                'label' => ['Poor', 'Average', 'Good'][rand(0,2)]
            ]);
        }

        // 🔥 Attendance
        for ($i = 0; $i < 200; $i++) {
            Attendance::create([
                'student_id' => Student::inRandomOrder()->first()->id,
                'classroom_id' => Classroom::inRandomOrder()->first()->id,
                'date' => now()->subDays(rand(0,10)),
                'status' => ['present', 'absent'][rand(0,1)],
                'confidence' => rand(80,100)/100
            ]);
        }

        // 🔥 Teacher Status
        for ($i = 0; $i < 20; $i++) {
            TeacherStatus::create([
                'teacher_id' => Teacher::inRandomOrder()->first()->id,
                'status' => ['present', 'absent'][rand(0,1)],
                'date' => now()->subDays(rand(0,5))
            ]);
        }
    }
}