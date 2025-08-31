<?php

namespace App\Http\Controllers\API\V1\Role;

use App\DTOs\Role\RoleDTO;
use App\Enums\YesNoEnum;
use App\Http\Controllers\API\V1\APIV1Controller;
use App\Http\Requests\API\V1\Role\RoleCreateRequest;
use App\Http\Requests\API\V1\Role\RoleUpdateRequest;
use App\Http\Resources\API\V1\Role\RoleResource;
use App\Models\Role\Role;
use App\Services\Role\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 *
 */
class RoleController extends APIV1Controller
{
    /**
     * @param RoleService $roleService
     */
    public function __construct(protected RoleService $roleService)
    {
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $roles = $this->roleService->getRoles();

        return RoleResource::collection($roles);
    }

    /**
     * @param RoleCreateRequest $request
     * @return JsonResponse
     */
    public function store(RoleCreateRequest $request): JsonResponse
    {
        $roleDTO             = RoleDTO::from($request->validated());
        $roleDTO->guard_name = 'web';

        $role = $this->roleService->create($roleDTO->toArrayForCreate());

        return RoleResource::make($role)
            ->additional(['message' => 'Role created successfully'])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @param string $uuid
     * @return RoleResource|JsonResponse
     */
    public function show(string $uuid): RoleResource|JsonResponse
    {
        $role = $this->roleService->getByUuid($uuid);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }

        return RoleResource::make($role);
    }

    /**
     * @param RoleUpdateRequest $request
     * @param string $uuid
     * @return RoleResource|JsonResponse
     */
    public function update(RoleUpdateRequest $request, string $uuid): RoleResource|JsonResponse
    {
        $data = (object)$request->validated();

        /**
         * @var Role $role
         */
        $role = $this->roleService->getByUuid($uuid);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }

        if ($role->default_role == YesNoEnum::Yes) {
            return response()->json([
                'message' => 'Cannot update default role'
            ], Response::HTTP_CONFLICT);
        }

        $roleDTO       = RoleDTO::from($this->roleService->getByUuid($uuid));
        $roleDTO->name = $data->name;
        $updatedRole   = $this->roleService->updateByUuid($uuid, $roleDTO->toArrayForCreate());

        return RoleResource::make($updatedRole)
            ->additional(['message' => 'Role updated successfully']);
    }

    /**
     * @param string $uuid
     * @return JsonResponse|Response
     */
    public function destroy(string $uuid): JsonResponse|Response
    {
        $response = $this->roleService->deleteRole($uuid);

        if (!$response->status) {
            return response()->json(['message' => $response->message], $response->code);
        }
        return response()->noContent();
    }
}
