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
 *
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
