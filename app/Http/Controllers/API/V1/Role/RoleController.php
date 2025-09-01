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
 * @group Role
 *
 * API endpoints for managing user roles
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
     * Get all roles
     *
     * Retrieves a list of all available roles in the system.
     *
     * @authenticated
     *
     * @response 200 [
     *   {
     *     "uuid": "550e8400-e29b-41d4-a716-446655440000",
     *     "name": "administrator",
     *   },
     *   {
     *     "uuid": "550e8400-e29b-41d4-a716-446655440001",
     *     "name": "user",
     *   }
     * ]
     *
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $roles = $this->roleService->getRoles();

        return RoleResource::collection($roles);
    }

    /**
     * Create a new role
     *
     * Creates a new role in the system.
     *
     * @authenticated
     *
     * @bodyParam name string required The role name. Must be unique. Example: manager
     *
     * @response 201 {
     *   "data": {
     *     "uuid": "550e8400-e29b-41d4-a716-446655440002",
     *     "name": "manager",
     *   },
     *   "message": "Role created successfully"
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "name": ["The name field is required.", "The name has already been taken."]
     *   }
     * }
     *
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
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
     * Get role by UUID
     *
     * Retrieves a specific role by its UUID.
     *
     * @authenticated
     *
     * @urlParam uuid string required The UUID of the role. Example: 550e8400-e29b-41d4-a716-446655440000
     *
     * @response 200 {
     *   "uuid": "550e8400-e29b-41d4-a716-446655440000",
     *   "name": "administrator",
     * }
     *
     * @response 404 {
     *   "message": "Role not found"
     * }
     *
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
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
     * Update role
     *
     * Updates an existing role by UUID. Default roles cannot be updated.
     *
     * @authenticated
     *
     * @urlParam uuid string required The UUID of the role to update. Example: 550e8400-e29b-41d4-a716-446655440000
     * @bodyParam name string required The new role name. Must be unique. Example: senior_manager
     *
     * @response 200 {
     *   "data": {
     *     "uuid": "550e8400-e29b-41d4-a716-446655440000",
     *     "name": "senior_manager",
     *   },
     *   "message": "Role updated successfully"
     * }
     *
     * @response 404 {
     *   "message": "Role not found"
     * }
     *
     * @response 409 {
     *   "message": "Cannot update default role"
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "name": ["The name field is required.", "The name has already been taken."]
     *   }
     * }
     *
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
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
     * Delete role
     *
     * Deletes a role by UUID. Default roles and roles assigned to users cannot be deleted.
     *
     * @authenticated
     *
     * @urlParam uuid string required The UUID of the role to delete. Example: 550e8400-e29b-41d4-a716-446655440000
     *
     * @response 204 scenario="Role deleted successfully"
     *
     * @response 404 {
     *   "message": "Role not found"
     * }
     *
     * @response 409 {
     *   "message": "Cannot delete default role"
     * }
     *
     * @response 422 {
     *   "message": "Cannot delete role that is assigned to users"
     * }
     *
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     *
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
