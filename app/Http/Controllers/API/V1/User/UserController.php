<?php

namespace App\Http\Controllers\API\V1\User;

use App\DTOs\User\UserDTO;
use App\Http\Controllers\API\V1\APIV1Controller;
use App\Http\Requests\API\V1\User\UserCreateRequest;
use App\Http\Requests\API\V1\User\UserUpdateRequest;
use App\Http\Resources\API\V1\User\UserResource;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * @group User
 *
 * API endpoints for managing users
 */
class UserController extends APIV1Controller
{
    /**
     * @param UserService $userService
     */
    public function __construct(protected UserService $userService)
    {
    }

    /**
     * Get all users
     *
     * Retrieves a list of all users in the system with their roles.
     *
     * @authenticated
     *
     * @response 200 [
     *   {
     *     "uuid": "550e8400-e29b-41d4-a716-446655440000",
     *     "name": "John",
     *     "last_name": "Doe",
     *     "email": "john.doe@example.com",
     *     "roles": [
     *       {
     *         "name": "administrator"
     *       }
     *     ]
     *   },
     *   {
     *     "uuid": "550e8400-e29b-41d4-a716-446655440001",
     *     "name": "Jane",
     *     "last_name": "Smith",
     *     "email": "jane.smith@example.com",
     *     "roles": [
     *       {
     *         "name": "user"
     *       }
     *     ]
     *   }
     * ]
     *
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $users = $this->userService->getUsers();

        return UserResource::collection($users);
    }

    /**
     * Create a new user
     *
     * Creates a new user in the system and assigns specified roles.
     *
     * @authenticated
     *
     * @bodyParam name string required The user's first name. Example: John
     * @bodyParam last_name string required The user's last name. Example: Doe
     * @bodyParam email string required The user's email address. Must be unique. Example: john.doe@example.com
     * @bodyParam password string required The user's password. Must be at least 8 characters. Example: password123
     * @bodyParam password_confirmation string required Password confirmation. Must match password. Example: password123
     * @bodyParam roles string[] optional Array of role names to assign to the user. Example: ["administrator", "user"]
     *
     * @response 201 {
     *   "data": {
     *     "uuid": "550e8400-e29b-41d4-a716-446655440002",
     *     "name": "John",
     *     "last_name": "Doe",
     *     "email": "john.doe@example.com",
     *     "roles": [
     *       {
     *         "name": "administrator"
     *       }
     *     ]
     *   },
     *   "message": "User created successfully"
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "email": ["The email has already been taken."],
     *     "password": ["The password must be at least 8 characters."],
     *     "roles": ["The selected roles is invalid."]
     *   }
     * }
     *
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * @param UserCreateRequest $request
     * @return JsonResponse
     */
    public function store(UserCreateRequest $request): JsonResponse
    {
        $data = (object)$request->validated();

        $userDTO = new UserDTO(
            name     : $data->name,
            last_name: $data->last_name,
            email    : $data->email,
            password : $data->password,
        );

        $user = $this->userService->updateOrCreateUser($userDTO, collect($data->roles));

        return UserResource::make($user)
            ->additional(['message' => 'User created successfully'])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Get user by UUID
     *
     * Retrieves a specific user by UUID with their assigned roles.
     *
     * @authenticated
     *
     * @urlParam uuid string required The UUID of the user. Example: 550e8400-e29b-41d4-a716-446655440000
     *
     * @response 200 {
     *   "uuid": "550e8400-e29b-41d4-a716-446655440000",
     *   "name": "John",
     *   "last_name": "Doe",
     *   "email": "john.doe@example.com",
     *   "roles": [
     *     {
     *       "name": "administrator"
     *     },
     *     {
     *       "name": "user"
     *     }
     *   ]
     * }
     *
     * @response 404 {
     *   "message": "User not found"
     * }
     *
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     *
     * @param string $uuid
     * @return UserResource|JsonResponse
     */
    public function show(string $uuid): UserResource|JsonResponse
    {
        $user = $this->userService->getUserWithRoles($uuid);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return UserResource::make($user);
    }

    /**
     * Update user (PUT)
     *
     * Completely updates a user's information and roles. All fields are required.
     *
     * @authenticated
     *
     * @urlParam uuid string required The UUID of the user to update. Example: 550e8400-e29b-41d4-a716-446655440000
     * @bodyParam name string required The user's first name. Example: John
     * @bodyParam last_name string required The user's last name. Example: Doe
     * @bodyParam email string required The user's email address. Must be unique. Example: john.doe@example.com
     * @bodyParam password string required The user's password. Must be at least 8 characters. Example: password123
     * @bodyParam password_confirmation string required Password confirmation. Must match password. Example: password123
     * @bodyParam roles string[] optional Array of role names to assign to the user. Example: ["administrator"]
     *
     * @response 200 {
     *   "uuid": "550e8400-e29b-41d4-a716-446655440000",
     *   "name": "John",
     *   "last_name": "Doe",
     *   "email": "john.doe@example.com",
     *   "roles": [
     *     {
     *       "name": "administrator"
     *     }
     *   ]
     * }
     *
     * @response 404 {
     *   "message": "User not found"
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "email": ["The email has already been taken."],
     *     "password": ["The password must be at least 8 characters."],
     *     "roles": ["The selected roles is invalid."]
     *   }
     * }
     *
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     *
     * @param UserUpdateRequest $request
     * @param string $uuid
     * @return UserResource|JsonResponse
     */
    public function updatePut(UserUpdateRequest $request, string $uuid): UserResource|JsonResponse
    {
        $data = (object)$request->validated();

        $user = $this->userService->getByUuid($uuid);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $userDTO = new UserDTO(
            uuid     : $uuid,
            name     : $data->name,
            last_name: $data->last_name,
            email    : $data->email,
            password : $data->password,
        );

        $user = $this->userService->updateOrCreateUser($userDTO, collect($data->roles));

        return UserResource::make($user);
    }

    /**
     * Update user (PATCH)
     *
     * Partially updates a user's information and/or roles. Only provided fields will be updated.
     *
     * @authenticated
     *
     * @urlParam uuid string required The UUID of the user to update. Example: 550e8400-e29b-41d4-a716-446655440000
     * @bodyParam name string optional The user's first name. Example: John
     * @bodyParam last_name string optional The user's last name. Example: Doe
     * @bodyParam email string optional The user's email address. Must be unique. Example: john.doe@example.com
     * @bodyParam password string optional The user's password. Must be at least 8 characters. Example: password123
     * @bodyParam password_confirmation string optional Password confirmation. Must match password if password is provided. Example: password123
     * @bodyParam roles string[] optional Array of role names to assign to the user. Example: ["user"]
     *
     * @response 200 {
     *   "uuid": "550e8400-e29b-41d4-a716-446655440000",
     *   "name": "John",
     *   "last_name": "Doe",
     *   "email": "john.doe@example.com",
     *   "roles": [
     *     {
     *       "name": "user"
     *     }
     *   ]
     * }
     *
     * @response 404 {
     *   "message": "User not found"
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "email": ["The email has already been taken."],
     *     "password": ["The password must be at least 8 characters."],
     *     "roles": ["The selected roles is invalid."]
     *   }
     * }
     *
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     *
     * @param UserUpdateRequest $request
     * @param string $uuid
     * @return UserResource|JsonResponse
     */
    public function updatePatch(UserUpdateRequest $request, string $uuid): UserResource|JsonResponse
    {
        $data = (object)$request->validated();
        /**
         * @var User $user
         */
        $user = $this->userService->getUserWithRoles($uuid);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $roles = isset($data->roles) ? $data->roles : $user->getRoleNames();

        $userDTO = UserDTO::from($user);
        // подстановка только тех полей, которые пришли в реквест
        foreach ($data as $key => $value) {
            if ($key === 'roles') {
                continue;
            }
            if (property_exists($userDTO, $key)) {
                $userDTO->$key = $value;
            }
        }

        $user = $this->userService->updateOrCreateUser($userDTO, collect($roles));

        return UserResource::make($user);
    }


    /**
     * Delete user
     *
     * Deletes a user from the system.
     *
     * @authenticated
     *
     * @urlParam uuid string required The UUID of the user to delete. Example: 550e8400-e29b-41d4-a716-446655440000
     *
     * @response 204 scenario="User deleted successfully"
     *
     * @response 404 {
     *   "message": "User not found"
     * }
     *
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     *
     * @param string $uuid
     * @return Response|JsonResponse
     */
    public function destroy(string $uuid): Response|JsonResponse
    {
        $user = $this->userService->getByUuid($uuid);

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $user->delete();

        return response()->noContent();
    }
}
