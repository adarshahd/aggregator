<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PreferenceStoreRequest;
use App\Http\Requests\PreferenceUpdateRequest;
use App\Http\Resources\PreferenceCollection;
use App\Http\Resources\PreferenceResource;
use App\Models\Preference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PreferenceController extends Controller
{
    /**
    User preferences

    This end point returns all the user preferences
    */
    public function index(Request $request): PreferenceCollection
    {
        $user = Auth::user();
        $preferences = Preference::query()->where("user_id", $user->id)->get();

        return new PreferenceCollection($preferences);
    }

    /**
    Store user preference

    This end point can be used to store user preference such as article authors
    and article sources

    @bodyParam authors string[] Comma separated article author ids Example: "1, 2, 3"
    @bodyParam sources string[] Comma separated article source ids Example: "1, 2"
    */
    public function store(
        PreferenceStoreRequest $request
    ): PreferenceResource|JsonResponse {
        // If preference for a user exists, update the same with new values

        $user = Auth::user();
        $preferenceInput = $request->validated();

        $preference = Preference::query()->where("user_id", $user->id)->first();
        if ($preference == null) {
            $preference = Preference::create([
                "user_id" => $user->id,
                "authors" => $preferenceInput["authors"],
                "sources" => $preferenceInput["sources"],
            ]);
        } else {
            $preference->update([
                "user_id" => $user->id,
                "authors" => $preferenceInput["authors"],
                "sources" => $preferenceInput["sources"],
            ]);
            $preference = $preference->refresh();
        }

        return new PreferenceResource($preference);
    }

    /**
    Preference Item

    Show information about specific preference item
    */
    public function show(
        Request $request,
        Preference $preference
    ): PreferenceResource|JsonResponse {
        $user = Auth::user();

        if ($user->id != $preference->user_id) {
            return response()->json(
                ["error" => "Unauthorized"],
                Response::HTTP_FORBIDDEN
            );
        }
        return new PreferenceResource($preference);
    }

    /**
    Update Preference

    Update preferences about article authors and sources

    @bodyParam authors string[] Comma separated article author ids Example: "1, 2, 3"
    @bodyParam sources string[] Comma separated article source ids Example: "1, 2"
    */
    public function update(
        PreferenceUpdateRequest $request,
        Preference $preference
    ): PreferenceResource|JsonResponse {
        $user = Auth::user();
        $request->merge(["user_id" => $user->id]);

        if ($user->id != $preference->user_id) {
            return response()->json(
                ["error" => "Unauthorized"],
                Response::HTTP_FORBIDDEN
            );
        }

        $preference->update($request->validated());

        return new PreferenceResource($preference);
    }

    /**
    Delete user preference

    This end point can be used to delete a specific preference
    */
    public function destroy(
        Request $request,
        Preference $preference
    ): Response|JsonResponse {
        $user = Auth::user();
        if ($user->id != $preference->user_id) {
            return response()->json(
                ["error" => "Unauthorized"],
                Response::HTTP_FORBIDDEN
            );
        }

        $preference->delete();

        return response()->noContent();
    }
}
