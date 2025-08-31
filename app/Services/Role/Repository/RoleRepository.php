<?php

namespace App\Services\Role\Repository;

use App\Models\Role\Role;
use App\Services\BaseRepository;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    /**
     * @param Role $model
     */
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }
}

