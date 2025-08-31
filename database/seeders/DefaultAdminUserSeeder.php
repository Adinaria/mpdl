<?php

namespace Database\Seeders;

use App\DTOs\User\UserDTO;
use App\Services\User\UserService;
use Illuminate\Database\Seeder;

class DefaultAdminUserSeeder extends Seeder
{
    public function __construct(protected UserService $userService)
    {
    }

    public function run(): void
    {
        // можно вынести в конфиг, который будет брать данные с env
        $userDTO = new UserDTO(
            name     : 'Admin',
            last_name: 'Admin',
            email    : 'admin@gmail.com',
            password : 'admin@gmail.com',
        );

        $this->userService->updateOrCreateUser($userDTO, collect(config('default_roles.administrator')));
    }
}
