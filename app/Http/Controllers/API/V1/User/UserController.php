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
 *
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
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $users = $this->userService->getUsers();

        return UserResource::collection($users);
    }

    /**
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
