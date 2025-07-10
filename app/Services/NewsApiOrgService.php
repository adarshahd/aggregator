<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsApiOrgService implements NewsBaseService
{
    const BASE_URL = "https://newsapi.org/v2";
    const CONNECT_TIMEOUT = 10; // Number of seconds to wait for connection before timeout
    const RETRY_COUNT = 2; // Number of tries to fetch articles
    const PAGE_SIZE = 100;
    const CATEGORIES = [
        "business",
        "entertainment",
        "general",
        "health",
        "science",
        "sports",
        "technology",
    ];

    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getArticles(string $query): Collection
    {
        $articleCollection = collect();
        try {
            $response = Http::connectTimeout(self::CONNECT_TIMEOUT)
                ->retry(self::RETRY_COUNT)
                ->withHeader("Authorization", $this->apiKey)
                ->get(self::BASE_URL . "/everything", [
                    "q" => $query,
                    "pageSize" => self::PAGE_SIZE,
                ])
                ->json();

            if ($response["status"] === "ok") {
                $result = $this->buildArticleCollection($response);
                $articleCollection = $articleCollection->concat($result);
            }
        } catch (Exception $e) {
            Log::error(
                "Could not complete request. Error fetching data from: " .
                    $e->getMessage()
            );
        }
        return $articleCollection;
    }

    public function getTopArticles(): Collection
    {
        $articleCollection = collect();
        foreach (self::CATEGORIES as $category) {
            try {
                $response = Http::connectTimeout(self::CONNECT_TIMEOUT)
                    ->retry(self::RETRY_COUNT)
                    ->withHeader("Authorization", $this->apiKey)
                    ->get(self::BASE_URL . "/top-headlines", [
                        "category" => $category,
                        "pageSize" => self::PAGE_SIZE,
                    ])
                    ->json();

                if ($response["status"] === "ok") {
                    $result = $this->buildArticleCollection($response);
                    $articleCollection = $articleCollection->concat($result);
                }
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
        return $articleCollection;
    }

    private function buildArticleCollection(array $articleResponse): Collection
    {
        $articleCollection = collect();

        foreach ($articleResponse["articles"] as $articleItem) {
            $article = [
                "title" => $articleItem["title"],
                "description" => $articleItem["description"],
                "url" => $articleItem["url"],
                "lang" => null,
                "thumbnail" => $articleItem["urlToImage"],
                "time" => Carbon::parse($articleItem["publishedAt"]),
                "article_source_name" => $articleItem["source"]["name"],
                "article_author_name" => $articleItem["author"],
            ];

            $articleCollection->push($article);
        }

        return $articleCollection;
    }
}
