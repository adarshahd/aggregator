<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleAuthor;
use App\Models\ArticleSource;
use App\Models\Preference;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        ArticleSource::factory(20)->create();

        ArticleAuthor::factory(20)->create();

        Preference::factory(5)->create();

        Article::factory(200)->create();
    }
}
