<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 *
 */
abstract class BaseService
{
    /**
     * @var BaseRepository
     */
    public BaseRepositoryInterface $baseRepository;

    /**
     * @param array|null $attr
     * @return Builder
     */
    public function index(array $attr = null): Builder
    {
        return $this->baseRepository->index($attr);
    }

    /**
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->baseRepository->create($data);
    }

    /**
     * @param string $uuid
     * @param array $input
     * @return Model|null
     */
    public function updateByUuid(string $uuid, array $input): ?Model
    {
        return $this->baseRepository->updateByUuid($uuid, $input);
    }

    /**
     * @param array $attr
     * @param array $data
     * @return Model
     */
    public function updateOrCreate(array $attr, array $data): Model
    {
        return $this->baseRepository->updateOrCreate($attr, $data);
    }


    /**
     * @param string $uuid
     * @return Model|null
     */
    public function getByUuid(string $uuid): ?Model
    {
        return $this->baseRepository->getByUuid($uuid);
    }

    /**
     * @param string $uuid
     * @return bool
     */
    public function deleteByUuid(string $uuid): bool
    {
        return $this->baseRepository->deleteByUuid($uuid);
    }
}
