<?php

namespace App\Services\User;

use App\DTOs\User\UserDTO;
use App\Models\User;
use App\Services\BaseService;
use App\Services\User\Repository\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

/**
 *
 */
class UserService extends BaseService
{
    /**
     * @param UserRepositoryInterface $baseRepository
     */
    public function __construct(UserRepositoryInterface $baseRepository)
    {
        $this->baseRepository = $baseRepository;
    }

    /**
     * @param UserDTO $userDTO
     * @return Model
     */
    public function register(UserDTO $userDTO): Model
    {
        /**
         * @var User $user
         */
        return $this->updateOrCreateUser($userDTO, collect(config('default_roles.human')));
    }

    /**
     *
     * @param UserDTO $userDTO
     * @param Collection|null $roles - если ничего не передали, то роли не трогаем, если пустая коллекция - то роли очищаем
     * @return Model
     */
    public function updateOrCreateUser(UserDTO $userDTO, ?Collection $roles = null): Model
    {
        $userDTO->password = !Hash::isHashed($userDTO->password) ? Hash::make($userDTO->password) : $userDTO->password;

        /**
         * @var User $user
         */
        $user = $this->baseRepository->updateOrCreate(
            [
                'uuid' => $userDTO->uuid
            ],
            $userDTO->toArrayForCreate()
        );

        $this->syncRoles($user, $roles);
        $user->loadMissing('roles');
        return $user;
    }


    /**
     * @return Collection
     */
    public function getUsers(): Collection
    {
        // todo кеш
        return $this->baseRepository->index()->with(['roles'])->get();
    }

    /**
     * @param string $uuid
     * @return Model|null
     */
    public function getUserWithRoles(string $uuid): ?Model
    {
        return $this->baseRepository->index()
            ->with(['roles'])
            ->where('uuid', $uuid)
            ->first();
    }

    private function syncRoles(User $user, ?Collection $roles): void
    {
        if (is_null($roles)) {
            return;
        }

        $user->syncRoles($roles->toArray());
    }


}
