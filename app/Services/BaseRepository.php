<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var Model
     */
    public Model $model;

    /**
     *
     * @param Model $model
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     *
     * @param array|null $attr
     * @return Builder
     */
    public function index(array $attr = null): Builder
    {
        return $attr ? $this->model->query()->select($attr) : $this->model->query();
    }

    /**
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        $model = $this->index()->create($data);
        return $model->refresh();
    }

    /**
     *
     * @param string $uuid
     * @param array $data
     * @return Model|null
     */
    public function updateByUuid(string $uuid, array $data): ?Model
    {
        $model = $this->index()->where('uuid', $uuid)->first();

        if (!$model) {
            return null;
        }

        $model->fill($data)->save();

        return $model->refresh();
    }

    /**
     *
     * @param array $attr
     * @param array $data
     * @return Model
     */
    public function updateOrCreate(array $attr, array $data): Model
    {
        return $this->model->query()->updateOrCreate($attr, $data);
    }

    /**
     * @param string $uuid
     * @return null|Model
     */
    public function getByUuid(string $uuid): ?Model
    {
        return $this->index()->where('uuid', $uuid)->first();
    }

    /**
     * @param string $uuid
     * @return bool
     */
    public function deleteByUuid(string $uuid): bool
    {
        $model = $this->index()->where('uuid', $uuid)->first();

        if (!$model) {
            return false;
        }

        return $model->delete();
    }
}
