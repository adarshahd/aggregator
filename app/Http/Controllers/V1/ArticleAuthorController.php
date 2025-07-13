<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleAuthorStoreRequest;
use App\Http\Requests\ArticleAuthorUpdateRequest;
use App\Http\Resources\ArticleAuthorCollection;
use App\Http\Resources\ArticleAuthorResource;
use App\Models\ArticleAuthor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ArticleAuthorController extends Controller
{
    public const PER_PAGE_COUNT = 20;

    /**
    Fetch article authors

    This method returns paginated article authors

    @queryParam page int Page number
    */
    public function index(
        Request $request
    ): ArticleAuthorCollection|JsonResponse {
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

        $articleAuthors = ArticleAuthor::query()->simplePaginate(
            self::PER_PAGE_COUNT,
            page: $page
        );

        return new ArticleAuthorCollection($articleAuthors);
    }
}
