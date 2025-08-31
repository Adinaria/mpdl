<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    /**
     * @param array|null $attr
     * @return Builder
     */
    public function index(array $attr = null): Builder;

    /**
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;


    /**
     * @param string $uuid
     * @param array $data
     * @return Model|null
     */
    public function updateByUuid(string $uuid, array $data): ?Model;

    /**
     * @param array $attr
     * @param array $data
     * @return Model
     */
    public function updateOrCreate(array $attr, array $data): Model;

    /**
     * @param string $uuid
     * @return null|Model
     */
    public function getByUuid(string $uuid): ?Model;

    /**
     * @param string $uuid
     * @return bool
     */
    public function deleteByUuid(string $uuid): bool;

}
