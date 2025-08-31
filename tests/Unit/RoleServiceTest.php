<?php

use App\Services\Role\RoleService;
use App\Services\Role\Repository\RoleRepositoryInterface;
use App\Enums\YesNoEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Response;

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

        $builder = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);
        $builder->shouldReceive('get')->once()->andReturn($roles);

        $this->mockRepository->shouldReceive('index')
            ->once()
            ->with(['name', 'uuid'])
            ->andReturn($builder);

        $result = $this->roleService->getRoles();

        expect($result)->toBe($roles);
    });

    test('getByUuid returns role when found', function () {
        $uuid     = 'test-uuid-123';
        $mockRole = Mockery::mock(\App\Models\Role\Role::class);

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

        $this->mockRepository->shouldReceive('deleteByUuid')
            ->never();

        $result = $this->roleService->deleteRole($uuid);

        expect($result->status)->toBeFalse()
            ->and($result->message)->toBe('Role Not found')
            ->and($result->code)->toBe(Response::HTTP_NOT_FOUND);
    });

    test('deleteRole prevents deletion of default role', function () {
        $uuid                   = 'default-role-uuid';
        $mockRole               = Mockery::mock(\App\Models\Role\Role::class)->makePartial();
        $mockRole->default_role = YesNoEnum::Yes;

        $this->mockRepository->shouldReceive('getByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($mockRole);

        $this->mockRepository->shouldReceive('deleteRole')
            ->with($uuid)
            ->never();

        $result = $this->roleService->deleteRole($uuid);

        expect($result->status)->toBeFalse()
            ->and($result->message)->toBe('Cannot delete default role')
            ->and($result->code)->toBe(Response::HTTP_CONFLICT);
    });

    test('deleteRole prevents deletion of role assigned to users', function () {
        $uuid                   = 'assigned-role-uuid';
        $mockRole               = Mockery::mock(\App\Models\Role\Role::class)->makePartial();
        $mockRole->default_role = YesNoEnum::No;

        $mockUsers = Mockery::mock(BelongsToMany::class)->makePartial();
        $mockUsers->shouldReceive('exists')->once()->andReturn(true);
        $mockRole->shouldReceive('users')->once()->andReturn($mockUsers);

        $this->mockRepository->shouldReceive('getByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($mockRole);

        $this->mockRepository->shouldReceive('deleteRole')->with($uuid)->never();
        $result = $this->roleService->deleteRole($uuid);

        expect($result->status)->toBeFalse()
            ->and($result->message)->toBe('Cannot delete role that is assigned to users')
            ->and($result->code)->toBe(Response::HTTP_CONFLICT);
    });

    test('deleteRole successfully deletes eligible role', function () {
        $uuid                   = 'deletable-role-uuid';
        $mockRole               = Mockery::mock(\App\Models\Role\Role::class)->makePartial();
        $mockRole->default_role = YesNoEnum::No;

        $mockUsers = Mockery::mock(BelongsToMany::class)->makePartial();
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

        expect($result->status)->toBeTrue()->and($result->message)->toBeNull()->and($result->code)->toBeNull();
    });

    test('deleteRole returns false when deletion fails', function () {
        $uuid                   = 'fail-delete-uuid';
        $mockRole               = Mockery::mock(\App\Models\Role\Role::class)->makePartial();
        $mockRole->default_role = YesNoEnum::No;

        $mockUsers = Mockery::mock(BelongsToMany::class);
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

        expect($result->status)->toBeFalse()->and($result->message)->toBeNull()->and($result->code)->toBeNull();
    });

    afterEach(function () {
        Mockery::close();
    });
});
