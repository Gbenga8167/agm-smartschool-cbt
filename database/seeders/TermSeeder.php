<?php

namespace Database\Seeders;

use App\Models\Term;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
 public function run(): void
    {
        $terms = [
            'First Term',
            'Second Term',   
            'Third Term',
        ];

        foreach ($terms as $term) {
            Term::updateOrCreate(
                ['name' => $term],
                ['is_current' => 0] // all off by default
            );
        }
    }
}
