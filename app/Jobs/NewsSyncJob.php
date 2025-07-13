<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\ArticleAuthor;
use App\Models\ArticleSource;
use App\Services\NewsBaseService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NewsSyncJob implements ShouldQueue
{
    use Queueable;
    protected NewsBaseService $newsService;

    /**
     * Create a new job instance.
     */
    public function __construct(NewsBaseService $service)
    {
        $this->newsService = $service;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $articleResult = $this->newsService->getTopArticles();
        foreach ($articleResult as $articleItem) {
            $articleSource = ArticleSource::query()->firstOrCreate([
                "name" => $articleItem["article_source_name"],
            ]);

            $articleAuthor = ArticleAuthor::query()->firstOrCreate([
                "name" => $articleItem["article_author_name"] ?? "Unknown",
            ]);

            $article = Article::query()
                ->where([
                    "title" => $articleItem["title"],
                    "article_source_id" => $articleSource->id,
                    "article_author_id" => $articleAuthor->id,
                ])
                ->firstOrCreate(
                    [
                        "title" => $articleItem["title"],
                        "article_source_id" => $articleSource->id,
                        "article_author_id" => $articleAuthor->id,
                    ],
                    [
                        "description" => $articleItem["description"] ?? "",
                        "url" => $articleItem["url"],
                        "lang" => $articleItem["lang"],
                        "thumbnail" => $articleItem["thumbnail"] ?? "",
                        "time" => $articleItem["time"],
                    ]
                );
        }
    }
}
