<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Article;
use App\Models\ArticleAuthor;
use App\Models\ArticleSource;
use Illuminate\Support\Facades\Log;

class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $randomAuthorId = random_int(1, 20);
        $randomSourceId = random_int(1, 20);

        return [
            "title" => fake()->sentence(4),
            "description" => fake()->text(),
            "url" => fake()->url(),
            "lang" => fake()->languageCode(),
            "thumbnail" => fake()->imageUrl(),
            "time" => fake()->dateTime(),
            "article_source_id" => ArticleSource::query()
                ->where("id", $randomSourceId)
                ->first()->id,
            "article_author_id" => ArticleAuthor::query()
                ->where("id", $randomAuthorId)
                ->first()->id,
        ];
    }
}
