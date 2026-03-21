<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\JobOpening;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        Review::create([
            'name' => 'John Doe',
            'title' => 'CEO, Tech Corp',
            'description' => 'Excellent service and quality work. Very professional team.',
            'rating' => 5,
        ]);

        JobOpening::create([
            'title' => 'Senior Site Engineer',
            'location' => 'Mumbai',
            'experience' => '5+ Years',
            'description' => '<p>We are looking for an experienced Site Engineer to lead our PT projects.</p><ul><li>Supervise sites</li><li>Coordinate with clients</li></ul>',
        ]);
    }
}
