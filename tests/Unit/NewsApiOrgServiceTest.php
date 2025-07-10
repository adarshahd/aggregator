<?php

namespace Tests\Unit;

use App\Services\NewsApiOrgService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\assertTrue;

class NewsApiOrgServiceTest extends TestCase
{
    protected $service;

    const ARTICLE_ITEM = [
        "title" => "Sample article",
        "description" => "sample article description",
        "url" => "https://article-url.name",
        "lang" => null,
        "urlToImage" => "https://article-url.image",
        "publishedAt" => null,
        "source" => ["name" => "NewsAPI.org"],
        "author" => "NewsAPI",
    ];

    protected function setUp(): void
    {
        $apiKey = "";
        $this->service = new NewsApiOrgService($apiKey);
    }

    public function test_news_api_builds_article_collection(): void
    {
        $class = new ReflectionClass("App\Services\NewsApiOrgService");
        $method = $class->getMethod("buildArticleCollection");

        $articleResponse = [];
        $articleResponse["articles"] = [self::ARTICLE_ITEM];

        $collection = $method->invoke($this->service, $articleResponse);

        assertEquals(1, $collection->count());
    }
}
