<?php

use App\Services\Role\RoleService;
use App\Services\Role\Repository\RoleRepositoryInterface;
use App\Enums\YesNoEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Mockery;

describe('RoleService', function () {
    beforeEach(function () {
        $this->mockRepository = Mockery::mock(RoleRepositoryInterface::class);
        $this->roleService    = new RoleService($this->mockRepository);

        config(['cache_entity.role.mode' => false]);
    });

    test('getRoles returns collection of roles', function () {
        $roles = new Collection([
            (object)['name' => 'admin', 'uuid' => 'uuid-1'],
            (object)['name' => 'user', 'uuid' => 'uuid-2']
        ]);

        $mockQuery = Mockery::mock();
        $mockQuery->shouldReceive('get')->once()->andReturn($roles);

        $this->mockRepository->shouldReceive('index')
            ->once()
            ->with(['name', 'uuid'])
            ->andReturn($mockQuery);

        $result = $this->roleService->getRoles();

        expect($result)->toBe($roles);
    });

    test('getByUuid returns role when found', function () {
        $uuid     = 'test-uuid-123';
        $mockRole = Mockery::mock(Model::class);

        $this->mockRepository->shouldReceive('getByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($mockRole);

        $result = $this->roleService->getByUuid($uuid);

        expect($result)->toBe($mockRole);
    });

    test('deleteRole returns not found when role does not exist', function () {
        $uuid = 'non-existent-uuid';

        $this->mockRepository->shouldReceive('getByUuid')
            ->once()
            ->with($uuid)
            ->andReturn(null);

        $result = $this->roleService->deleteRole($uuid);

        expect($result->status)->toBeFalse();
        expect($result->message)->toBe('Role Not found');
        expect($result->code)->toBe(Response::HTTP_NOT_FOUND);
    });

    test('deleteRole prevents deletion of default role', function () {
        $uuid                   = 'default-role-uuid';
        $mockRole               = Mockery::mock(Model::class);
        $mockRole->default_role = YesNoEnum::Yes;

        $this->mockRepository->shouldReceive('getByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($mockRole);

        $result = $this->roleService->deleteRole($uuid);

        expect($result->status)->toBeFalse();
        expect($result->message)->toBe('Cannot delete default role');
        expect($result->code)->toBe(Response::HTTP_CONFLICT);
    });

    test('deleteRole prevents deletion of role assigned to users', function () {
        $uuid                   = 'assigned-role-uuid';
        $mockRole               = Mockery::mock(Model::class);
        $mockRole->default_role = YesNoEnum::No;

        $mockUsers = Mockery::mock();
        $mockUsers->shouldReceive('exists')->once()->andReturn(true);
        $mockRole->shouldReceive('users')->once()->andReturn($mockUsers);

        $this->mockRepository->shouldReceive('getByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($mockRole);

        $result = $this->roleService->deleteRole($uuid);

        expect($result->status)->toBeFalse();
        expect($result->message)->toBe('Cannot delete role that is assigned to users');
        expect($result->code)->toBe(Response::HTTP_CONFLICT);
    });

    test('deleteRole successfully deletes eligible role', function () {
        $uuid                   = 'deletable-role-uuid';
        $mockRole               = Mockery::mock(Model::class);
        $mockRole->default_role = YesNoEnum::No;

        $mockUsers = Mockery::mock();
        $mockUsers->shouldReceive('exists')->once()->andReturn(false);
        $mockRole->shouldReceive('users')->once()->andReturn($mockUsers);

        $this->mockRepository->shouldReceive('getByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($mockRole);

        $this->mockRepository->shouldReceive('deleteByUuid')
            ->once()
            ->with($uuid)
            ->andReturn(true);

        $result = $this->roleService->deleteRole($uuid);

        expect($result->status)->toBeTrue();
    });

    test('deleteRole returns false when deletion fails', function () {
        $uuid                   = 'fail-delete-uuid';
        $mockRole               = Mockery::mock(Model::class);
        $mockRole->default_role = YesNoEnum::No;

        $mockUsers = Mockery::mock();
        $mockUsers->shouldReceive('exists')->once()->andReturn(false);
        $mockRole->shouldReceive('users')->once()->andReturn($mockUsers);

        $this->mockRepository->shouldReceive('getByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($mockRole);

        $this->mockRepository->shouldReceive('deleteByUuid')
            ->once()
            ->with($uuid)
            ->andReturn(false);

        $result = $this->roleService->deleteRole($uuid);

        expect($result->status)->toBeFalse();
    });

    afterEach(function () {
        Mockery::close();
    });
});
