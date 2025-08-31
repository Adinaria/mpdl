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
     * @return UserResource
     */
    public function show(string $uuid): UserResource
    {
        $user = $this->userService->getUserWithRoles($uuid);

        return UserResource::make($user);
    }

    /**
     * @param UserUpdateRequest $request
     * @param string $uuid
     * @return UserResource
     */
    public function updatePut(UserUpdateRequest $request, string $uuid): UserResource
    {
        $data = (object)$request->validated();

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
     * @return UserResource
     */
    public function updatePatch(UserUpdateRequest $request, string $uuid): UserResource
    {
        $data = (object)$request->validated();
        /**
         * @var User $user
         */
        $user  = $this->userService->getUserWithRoles($uuid);

        $roles = isset($data->roles) ? $data->roles : $user->getRoleNames();

        $userDTO = UserDTO::from($user);
        // подстановка только тех полей, которые пришли в реквест
        foreach ($data as $key => $value) {
            if ($key === 'roles') continue;
            if (property_exists($userDTO, $key)) {
                $userDTO->$key = $value;
            }
        }

        $user = $this->userService->updateOrCreateUser($userDTO, collect($roles));

        return UserResource::make($user);
    }


    /**
     * @param string $uuid
     * @return Response
     */
    public function destroy(string $uuid): Response
    {
        $this->userService->deleteByUuid($uuid);

        return response()->noContent();
    }
}
