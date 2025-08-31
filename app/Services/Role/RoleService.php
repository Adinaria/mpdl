<?php

namespace App\Services\Role;

use App\Enums\YesNoEnum;
use App\Services\BaseService;
use App\Services\Role\Repository\RoleRepositoryInterface;
use App\Traits\EntityCacheable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

/**
 *
 */
class RoleService extends BaseService
{
    use EntityCacheable;

    protected bool $canEntityCache = false;

    /**
     * @param RoleRepositoryInterface $roleRepository
     */
    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->baseRepository = $roleRepository;
        $this->canEntityCache = config('cache_entity.role.mode');
    }

    /**
     * @return Collection
     */
    public function getRoles(): Collection
    {
        return $this->caching(
            $this->canEntityCache,
            config('cache_entity.role.cache_keys.list'),
            function () {
                return $this->baseRepository->index(['name', 'uuid'])->get();
            }
        );
    }

    public function getByUuid(string $uuid): ?Model
    {
        return $this->caching(
            $this->canEntityCache,
            config('cache_entity.role.cache_keys.entity') . $uuid,
            function () use ($uuid) {
                return parent::getByUuid($uuid);
            }
        );
    }

    /**
     * @param string $uuid
     * @return \StdClass
     */
    public function deleteRole(string $uuid): \StdClass
    {
        $response = literal(status: false, message: null, code: null);

        $role = $this->getByUuid($uuid);;
        if (is_null($role)) {
            $response->status  = false;
            $response->message = "Role Not found";
            $response->code    = Response::HTTP_NOT_FOUND;
            return $response;
        } elseif ($role->default_role == YesNoEnum::Yes) {
            $response->status  = false;
            $response->message = 'Cannot delete default role';
            $response->code    = Response::HTTP_CONFLICT;
            return $response;
        } elseif ($role->users()->exists()) {
            $response->status  = false;
            $response->message = 'Cannot delete role that is assigned to users';
            $response->code    = Response::HTTP_CONFLICT;
            return $response;
        }

        $isDeleted        = $this->baseRepository->deleteByUuid($uuid);
        $response->status = $isDeleted;

        return $response;
    }
}
