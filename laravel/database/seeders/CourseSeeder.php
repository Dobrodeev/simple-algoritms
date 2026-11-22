<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/** Loads database/data/courses.json into the courses table. */
class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('data/courses.json')), true, 512, JSON_THROW_ON_ERROR);
        $now = now();

        $rows = [];
        foreach ($data as $symbol => $price) {
            $rows[] = ['symbol' => $symbol, 'price' => $price, 'created_at' => $now, 'updated_at' => $now];
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('courses')->upsert($chunk, ['symbol'], ['price', 'updated_at']);
        }
    }
}
