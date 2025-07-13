<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuardianApiService implements NewsBaseService
{
    const BASE_URL = "https://content.guardianapis.com/";
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
                ->get(self::BASE_URL . "/search", [
                    "q" => $query,
                    "page-size" => self::PAGE_SIZE,
                    "api-key" => $this->apiKey,
                    "show-tags" => "contributor",
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
                    ->get(self::BASE_URL . "/search", [
                        "q" => $category,
                        "page-size" => self::PAGE_SIZE,
                        "api-key" => $this->apiKey,
                        "show-tags" => "contributor",
                    ])
                    ->json();

                if ($response["response"]["status"] === "ok") {
                    $result = $this->buildArticleCollection(
                        $response["response"]
                    );
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

        foreach ($articleResponse["results"] as $articleItem) {
            $article = [
                "title" => $articleItem["webTitle"],
                "description" => $articleItem["webTitle"],
                "url" => $articleItem["webUrl"],
                "lang" => "",
                "thumbnail" => "",
                "time" => Carbon::parse($articleItem["webPublicationDate"]),
                "article_source_name" => "The Guardian",
                "article_author_name" =>
                    count($articleItem["tags"]) > 0
                        ? $articleItem["tags"][0]["webTitle"]
                        : "The Guardian Team",
            ];

            $articleCollection->push($article);
        }

        return $articleCollection;
    }
}
