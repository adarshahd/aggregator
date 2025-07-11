<?php

namespace Database\Factories;

use App\Models\ArticleAuthor;
use App\Models\ArticleSource;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Preference;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PreferenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Preference::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $randomUserId = random_int(1, 10);
        $randomNumberArray = range(1, 20);

        shuffle($randomNumberArray);
        $randomAuthorIds = ArticleAuthor::query()
            ->whereIn("id", array_slice($randomNumberArray, 1, 5))
            ->get()
            ->map(function ($item) {
                return $item->id;
            });

        shuffle($randomNumberArray);
        $randomSourceIds = ArticleSource::query()
            ->whereIn("id", array_slice($randomNumberArray, 1, 5))
            ->get()
            ->map(function ($item) {
                return $item->id;
            });

        return [
            "user_id" => User::query()->where("id", $randomUserId)->first()->id,
            "authors" => $randomAuthorIds,
            "sources" => $randomSourceIds,
        ];
    }
}
