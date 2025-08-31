<?php

use App\Services\User\UserService;
use App\Services\User\Repository\UserRepositoryInterface;
use App\DTOs\User\UserDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Mockery;

describe('UserService', function () {
    beforeEach(function () {
        $this->mockRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->userService    = new UserService($this->mockRepository);

        config(['cache_entity.user.mode' => false]);
    });

    test('register creates user with default roles', function () {
        $userDTO = new UserDTO(
            name     : 'John',
            last_name: 'Doe',
            email    : 'john@example.com',
            password : 'password123'
        );

        $mockUser = Mockery::mock(User::class)->makePartial();
        $mockUser->shouldReceive('syncRoles')->once()->with([config('default_roles.human')]);
        $mockUser->shouldReceive('loadMissing')->once()->with('roles')->andReturn($mockUser);

        $this->mockRepository->shouldReceive('updateOrCreate')
            ->once()
            ->andReturn($mockUser);

        $result = $this->userService->register($userDTO);

        expect($result)->toBe($mockUser);
    });

    test('updateOrCreateUser hashes password if not already hashed', function () {
        $userDTO = new UserDTO(
            name     : 'John',
            last_name: 'Doe',
            email    : 'john@example.com',
            password : 'password123'
        );

        $mockUser = Mockery::mock(User::class)->makePartial();
        $mockUser->shouldReceive('syncRoles')->once();
        $mockUser->shouldReceive('loadMissing')->once()->andReturn($mockUser);

        $this->mockRepository->shouldReceive('updateOrCreate')
            ->once()
            ->with(
                ['uuid' => $userDTO->uuid],
                Mockery::on(function ($data) {
                    return Hash::check('password123', $data['password']);
                })
            )
            ->andReturn($mockUser);

        $this->userService->updateOrCreateUser($userDTO, collect());
    });

    test('updateOrCreateUser does not hash already hashed password', function () {
        $hashedPassword = Hash::make('password123');
        $userDTO        = new UserDTO(
            name     : 'John',
            last_name: 'Doe',
            email    : 'john@example.com',
            password : $hashedPassword
        );

        $mockUser = Mockery::mock(User::class)->makePartial();
        $mockUser->shouldReceive('syncRoles')->once();
        $mockUser->shouldReceive('loadMissing')->once()->andReturn($mockUser);

        $this->mockRepository->shouldReceive('updateOrCreate')
            ->once()
            ->with(
                ['uuid' => $userDTO->uuid],
                Mockery::on(function ($data) use ($hashedPassword) {
                    return $data['password'] === $hashedPassword;
                })
            )
            ->andReturn($mockUser);

        $this->userService->updateOrCreateUser($userDTO, collect());
    });

    test('getUserWithRoles returns user with roles', function () {
        $uuid     = 'test-uuid-123';
        $mockUser = Mockery::mock(User::class)->makePartial();

        $mockQuery = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);
        $mockQuery->shouldReceive('with')->once()->with(['roles'])->andReturn($mockQuery);
        $mockQuery->shouldReceive('where')->once()->with('uuid', $uuid)->andReturn($mockQuery);
        $mockQuery->shouldReceive('first')->once()->andReturn($mockUser);

        $this->mockRepository->shouldReceive('index')->once()->andReturn($mockQuery);

        $result = $this->userService->getUserWithRoles($uuid);

        expect($result)->toBe($mockUser);
    });

    test('getUsers returns collection of users', function () {
        $users = collect([new User(), new User()]);

        $mockQuery = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);
        $mockQuery->shouldReceive('with')->once()->with(['roles'])->andReturn($mockQuery);
        $mockQuery->shouldReceive('get')->once()->andReturn($users);

        $this->mockRepository->shouldReceive('index')
            ->once()
            ->with(['id', 'uuid', 'name', 'last_name', 'email'])
            ->andReturn($mockQuery);

        $result = $this->userService->getUsers();

        expect($result)->toBe($users);
    });

    afterEach(function () {
        Mockery::close();
    });
});
