<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleSourceStoreRequest;
use App\Http\Requests\ArticleSourceUpdateRequest;
use App\Http\Resources\ArticleSourceCollection;
use App\Http\Resources\ArticleSourceResource;
use App\Models\ArticleSource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ArticleSourceController extends Controller
{
    public const PER_PAGE_COUNT = 20;

    /**
    Fetch article authors

    This method returns paginated article sources

    @queryParam page int Page number

    */
    public function index(
        Request $request
    ): ArticleSourceCollection|JsonResponse {
        // Query parameters
        $validator = Validator::make($request->all(), [
            "page" => "int",
        ]);

        if ($validator->fails()) {
            return response()->json(
                ["error" => "Invalid request"],
                Response::HTTP_BAD_REQUEST
            );
        }

        $page = intval($request->input("page") ?? 1);
        $articleSources = ArticleSource::query()->simplePaginate(
            self::PER_PAGE_COUNT,
            page: $page
        );

        return new ArticleSourceCollection($articleSources);
    }
}
