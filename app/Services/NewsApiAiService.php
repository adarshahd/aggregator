<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsApiAiService implements NewsBaseService
{
    const BASE_URL = "https://eventregistry.org/api/v1";
    const CONNECT_TIMEOUT = 10; // Number of seconds to wait for connection before timeout
    const RETRY_COUNT = 2; // Number of tries to fetch articles
    const CATEGORIES = [
        "dmoz/Business",
        "dmoz/Arts/Entertainment",
        "dmoz/Health",
        "dmoz/Science",
        "dmoz/Sports",
        "news/Technology",
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
                ->post(self::BASE_URL . "/article/getArticles", [
                    "query" => $query,
                    "apiKey" => $this->apiKey,
                ])
                ->json();

            $result = $this->buildArticleCollection($response);
            $articleCollection = $articleCollection->concat($result);
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
        try {
            $response = Http::connectTimeout(self::CONNECT_TIMEOUT)
                ->retry(self::RETRY_COUNT)
                ->post(self::BASE_URL . "/minuteStreamArticles", [
                    "categoryUri" => self::CATEGORIES,
                    "apiKey" => $this->apiKey,
                ])
                ->json();

            $result = $this->buildArticleCollection($response);
            $articleCollection = $articleCollection->concat($result);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
        return $articleCollection;
    }

    private function buildArticleCollection(array $articleResponse): Collection
    {
        $articleCollection = collect();

        foreach (
            $articleResponse["recentActivityArticles"]["activity"]
            as $articleItem
        ) {
            $article = [
                "title" => $articleItem["title"],
                "description" => $articleItem["body"],
                "url" => $articleItem["url"],
                "lang" => $articleItem["lang"],
                "thumbnail" => $articleItem["image"],
                "time" => Carbon::parse($articleItem["dateTime"]),
                "article_source_name" => $articleItem["source"]["title"],
                "article_author_name" =>
                    count($articleItem["authors"]) > 0
                        ? $articleItem["authors"][0]["name"]
                        : null,
            ];

            $articleCollection->push($article);
        }

        return $articleCollection;
    }
}
