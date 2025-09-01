<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\DTOs\User\UserDTO;
use App\Http\Controllers\API\V1\APIV1Controller;
use App\Http\Requests\API\V1\Auth\UserLoginRequest;
use App\Http\Requests\API\V1\Auth\UserRegisterRequest;
use App\Http\Resources\API\V1\User\UserResource;
use App\Models\User;
use App\Services\User\UserService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Authentication
 *
 * API endpoints for user authentication and registration
 */
class AuthController extends APIV1Controller
{

    /**
     * @param UserService $userService
     */
    public function __construct(protected UserService $userService)
    {
    }

    /**
     * Register a new user
     *
     * Creates a new user account and returns an authentication token.
     *
     * @unauthenticated
     *
     * @bodyParam name string required The user's first name. Example: John
     * @bodyParam last_name string required The user's last name. Example: Doe
     * @bodyParam email string required The user's email address. Example: john.doe@example.com
     * @bodyParam password string required The user's password. Must be at least 8 characters. Example: password123
     * @bodyParam password_confirmation string required Password confirmation. Must match password. Example: password123
     *
     * @response 201 {
     *   "token": "1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz",
     *   "user": [
     *     {
     *       "uuid": "550e8400-e29b-41d4-a716-446655440000",
     *       "name": "John",
     *       "last_name": "Doe",
     *       "email": "john.doe@example.com",
     *     }
     *   ]
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "email": ["The email has already been taken."],
     *     "password": ["The password must be at least 8 characters."]
     *   }
     * }
     *
     * @param UserRegisterRequest $request
     * @return JsonResponse
     */
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = (object)$request->validated();

        // userDTO::from можно сделать
        $userDTO = new UserDTO(
            name     : $data->name,
            last_name: $data->last_name,
            email    : $data->email,
            password : $data->password,
        );

        /**
         * @var User $user
         */
        $user = $this->userService->register($userDTO);

        $token = $user->createToken(
            name     : $user->email,
            expiresAt: Carbon::now()->addDay()
        )->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                UserResource::make($user),
            ]
        ], 201);
    }

    /**
     * Login user
     *
     * Authenticates a user with email and password, returns an authentication token.
     *
     * @unauthenticated
     *
     * @bodyParam email string required The user's email address. Example: john.doe@example.com
     * @bodyParam password string required The user's password. Example: password123
     *
     * @response 200 {
     *   "token": "1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz",
     *   "user": [
     *     {
     *       "uuid": "550e8400-e29b-41d4-a716-446655440000",
     *       "name": "John",
     *       "last_name": "Doe",
     *       "email": "john.doe@example.com",
     *     }
     *   ]
     * }
     *
     * @response 401 {
     *   "errors": {
     *     "email": "Invalid email or password"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "email": ["The email field is required."],
     *     "password": ["The password field is required."]
     *   }
     * }
     *
     * @param UserLoginRequest $request
     * @return JsonResponse
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        $data = (object)$request->validated();

        if (Auth::attempt(['email' => $data->email, 'password' => $data->password])) {
            $user = Auth::user();

            $token = $user->createToken(
                name     : $user->email,
                expiresAt: Carbon::now()->addDay()
            )->plainTextToken;

            return response()->json([
                'token' => $token,
                'user'  => [
                    UserResource::make($user),
                ]
            ]);
        }

        return response()->json([
            'errors' => ['email' => 'Invalid email or password']
        ], 401);
    }

    /**
     * Logout user
     *
     * Revokes the current user's authentication token.
     *
     * @authenticated
     *
     * @response 200 {
     *   "message": "Logged out successfully."
     * }
     *
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.'
        ]);
    }
}
