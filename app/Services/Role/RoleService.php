<?php

namespace App\Services\Role;

use App\Services\BaseService;
use App\Services\Role\Repository\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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
        return Cache::remember(config('cache_entity.role.list'), now()->addDay(), function () {
            return $this->baseRepository->index()->get();
        });
    }

    public function getByUuid(string $uuid): ?Model
    {
        return Cache::remember(config('cache_entity.role.entity') . $uuid, now()->addDay(), function ($uuid) {
            return parent::getByUuid($uuid);
        });
    }

    /**
     * @param string $uuid
     * @return \StdClass
     */
    public function deleteRole(string $uuid): \StdClass
    {
        $response = literal(status: false, message: null);

        $role = $this->getByUuid($uuid);

        if (!is_null($role) && $role->users()->exists()) {
            $response->status  = false;
            $response->message = 'Cannot delete role that is assigned to users';
            return $response;
        }

        $isDeleted        = $this->baseRepository->deleteByUuid($uuid);
        $response->status = $isDeleted;

        return $response;
    }
}
