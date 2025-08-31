<?php

namespace App\Services\Role;

use App\Models\User;
use App\Services\BaseService;
use App\Services\Role\Repository\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

/**
 *
 */
class RoleService extends BaseService
{
    /**
     * @param RoleRepositoryInterface $roleRepository
     */
    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->baseRepository = $roleRepository;
    }

    /**
     * @return Collection
     */
    public function getRoles(): Collection
    {
        // todo кеш
        return $this->baseRepository->index()->get();
    }

    /**
     * @param string $uuid
     * @return \StdClass
     */
    public function deleteRole(string $uuid): \StdClass
    {
        $response = literal(status: false, message: null);

        $role = Role::where('uuid', $uuid)
            ->first();

        if ($role && $role->users()->exists()) {
            $response->status  = false;
            $response->message = 'Cannot delete role that is assigned to users';
            return $response;
        }

        $isDeleted        = $this->baseRepository->deleteByUuid($uuid);
        $response->status = $isDeleted;

        return $response;
    }
}
