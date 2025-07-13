<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthenticationRequest;
use App\Http\Requests\UserRegistrationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class LoginController extends Controller
{
    /**
     * User Registration
     *
     * This end point can be used to register a user
     *
     * @bodyParam full_name string User full name Example: John Doe
     * @bodyParam email email User email id Example: john@doe.com
     * @bodyParam password string,min:8 User password Example: NvYgxwmi/#iw/kX
     */
    public function register(UserRegistrationRequest $request)
    {
        $validatedRequest = $request->validated();
        $user = User::query()->create([
            "name" => $validatedRequest["full_name"],
            "email" => $validatedRequest["email"],
            "password" => $validatedRequest["password"],
        ]);

        return response()->json($user, Response::HTTP_CREATED);
    }

    /**
     * Token Generation
     *
     * This end point is used for token generation
     */
    public function getToken(AuthenticationRequest $request)
    {
        $validatedRequest = $request->validated();
        $user = User::where("email", $validatedRequest["email"])->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                "email" => ["The provided credentials are incorrect."],
            ]);
        }

        $token = $user->createToken(Uuid::uuid7()->toString())->plainTextToken;
        return response()->json($token);
    }
}
