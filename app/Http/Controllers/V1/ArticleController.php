<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Preference;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    public const CACHE_TIMEOUT = 15;
    public const PER_PAGE_COUNT = 20;

    /**
    Fetch Articles

    This method returns articles with paginated result. Each page contains 20 articles

    @queryParam page int The required page number, defaults to first page Example: 1
    @queryParam no_cache bool Instruct API to clear cache and provide fresh data Example: false
    */
    public function index(Request $request): JsonResponse
    {
        // Query parameters
        $validator = Validator::make($request->all(), [
            "page" => "int",
            "no_cache" => Rule::in(["true", "false"]),
        ]);

        if ($validator->fails()) {
            return response()->json(
                ["error" => "Invalid request"],
                Response::HTTP_BAD_REQUEST
            );
        }

        $page = intval($request->input("page") ?? 1);
        $noCache = $request->input("no_cache") == "true" ? true : false;

        $user = Auth::user();
        $preference = Preference::query()->where("user_id", $user->id)->first();
        $articles = collect();
        $articleTotal = 0;
        $articlesTotalPage = 0;
        $cacheKey = "";
        $cacheKeyArticleCount = "";
        $cacheKeyArticleTotalPage = "";
        $cache = false;

        /**
        Caching Strategy

        Articles are cached based on user preferences and current page being requested.

        Pagination is customized to make sure we can store and retrieve the articles from cache
        easily
        */

        if ($preference != null) {
            $preferenceTimeStamp = $preference->updated_at->timestamp;
            $cacheKey = "articles-$user->id-$preferenceTimeStamp-$page";
            $cacheKeyArticleCount = "articles-$user->id-$preferenceTimeStamp-$page-count";
            $cacheKeyArticleTotalPage = "articles-$user->id-$preferenceTimeStamp-$page-total-page";
        } else {
            $cacheKey = "articles-$user->id-$page";
            $cacheKeyArticleCount = "articles-$user->id-$page-count";
            $cacheKeyArticleTotalPage = "articles-$user->id-$page-total-page";
        }

        // If no_cache is set or cache not found for given key, fetch articles from DB
        if ($noCache || !Cache::has($cacheKey)) {
            Log::info("Fetching articles from database");
            $articlePages = collect();
            if ($preference != null) {
                Cache::delete($cacheKey);
                $articlePages = Article::query()
                    ->whereIn(
                        "article_source_id",
                        explode(",", $preference->sources)
                    )
                    ->orWhereIn(
                        "article_author_id",
                        explode(",", $preference->authors)
                    )
                    ->orderBy("time", "desc")
                    ->paginate(self::PER_PAGE_COUNT, page: $page);
            } else {
                Cache::delete($cacheKey);
                $articlePages = Article::query()
                    ->orderBy("time", "desc")
                    ->paginate(self::PER_PAGE_COUNT, page: $page);
            }
            $articles = $articlePages->items();
            $articleTotal = $articlePages->total();
            $articlesTotalPage = $articlePages->lastPage();

            Cache::put(
                $cacheKey,
                json_encode($articles),
                now()->addMinutes(self::CACHE_TIMEOUT)
            );
            Cache::put(
                $cacheKeyArticleCount,
                $articleTotal,
                now()->addMinutes(self::CACHE_TIMEOUT)
            );
            Cache::put(
                $cacheKeyArticleTotalPage,
                $articlesTotalPage,
                now()->addMinutes(self::CACHE_TIMEOUT)
            );
        } else {
            // Use cached data for response
            Log::info("Cache hit");
            $cachedArticleArray = json_decode(Cache::get($cacheKey), true);
            $articleTotal = Cache::get($cacheKeyArticleCount);
            $articlesTotalPage = Cache::get($cacheKeyArticleTotalPage);
            $articles = Article::hydrate($cachedArticleArray);
            $cache = true;
        }

        return response()->json([
            "total" => $articleTotal,
            "current_page" => $page,
            "total_page" => $articlesTotalPage,
            "cache" => $cache,
            "articles" => $articles,
        ]);
    }
}
