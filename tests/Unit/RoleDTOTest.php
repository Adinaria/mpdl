<?php

use App\DTOs\Role\RoleDTO;
use App\Enums\YesNoEnum;

describe('RoleDTO', function () {
    test('creates RoleDTO with all properties', function () {
        $roleDTO = new RoleDTO(
            id          : 1,
            uuid        : 'test-uuid-123',
            name        : 'administrator',
            default_role: YesNoEnum::Yes,
            guard_name  : 'web',
            created_at  : '2023-01-01 00:00:00',
            updated_at  : '2023-01-01 00:00:00'
        );

        expect($roleDTO->id)->toBe(1)
            ->and($roleDTO->uuid)->toBe('test-uuid-123')
            ->and($roleDTO->name)->toBe('administrator')
            ->and($roleDTO->default_role)->toBe(YesNoEnum::Yes)
            ->and($roleDTO->guard_name)->toBe('web')
            ->and($roleDTO->created_at)->toBe('2023-01-01 00:00:00')
            ->and($roleDTO->updated_at)->toBe('2023-01-01 00:00:00');
    });

    test('creates RoleDTO with minimal required properties', function () {
        $roleDTO = new RoleDTO(
            name      : 'user',
            guard_name: 'web'
        );

        expect($roleDTO->name)->toBe('user')
            ->and($roleDTO->guard_name)->toBe('web')
            ->and($roleDTO->id)->toBeNull()
            ->and($roleDTO->uuid)->toBeNull()
            ->and($roleDTO->default_role)->toBeNull();
    });

    test('creates RoleDTO with default_role as string', function () {
        $roleDTO = new RoleDTO(
            name        : 'moderator',
            default_role: 'No',
            guard_name  : 'web'
        );

        expect($roleDTO->name)->toBe('moderator')
            ->and($roleDTO->default_role)->toBe('No')
            ->and($roleDTO->guard_name)->toBe('web');
    });

    test('creates RoleDTO with default_role as YesNoEnum', function () {
        $roleDTO = new RoleDTO(
            name        : 'admin',
            default_role: YesNoEnum::No,
            guard_name  : 'web'
        );

        expect($roleDTO->name)->toBe('admin')
            ->and($roleDTO->default_role)->toBe(YesNoEnum::No)
            ->and($roleDTO->guard_name)->toBe('web');
    });

    test('toArray returns all properties including nulls and timestamps', function () {
        $roleDTO = new RoleDTO(
            id          : 1,
            uuid        : 'test-uuid-123',
            name        : 'administrator',
            default_role: YesNoEnum::Yes,
            guard_name  : 'web'
        );

        $array = $roleDTO->toArray();

        expect($array)->toHaveKeys([
            'id',
            'uuid',
            'name',
            'guard_name',
            'created_at',
            'default_role',
            'updated_at'
        ])
            ->and($array['id'])->toBe(1)
            ->and($array['uuid'])->toBe('test-uuid-123')
            ->and($array['name'])->toBe('administrator')
            ->and($array['default_role'])->toBe(YesNoEnum::Yes)
            ->and($array['guard_name'])->toBe('web')
            ->and($array['created_at'])->toBeNull()
            ->and($array['updated_at'])->toBeNull();
    });

    test('toArrayForCreate returns only creation-relevant fields', function () {
        $roleDTO = new RoleDTO(
            id          : 1,
            uuid        : 'test-uuid-123',
            name        : 'administrator',
            default_role: YesNoEnum::No,
            guard_name  : 'web',
            created_at  : '2023-01-01 00:00:00',
            updated_at  : '2023-01-01 00:00:00'
        );

        $array = $roleDTO->toArrayForCreate();

        expect($array)->toHaveKeys(['name', 'guard_name', 'default_role'])
            ->and($array)->not->toHaveKeys(['id', 'uuid', 'created_at', 'updated_at'])
            ->and($array['name'])->toBe('administrator')
            ->and($array['guard_name'])->toBe('web')
            ->and($array['default_role'])->toBe(YesNoEnum::No);
    });

    test('toArrayForCreate excludes system fields', function () {
        $roleDTO = new RoleDTO(
            name        : 'user',
            default_role: YesNoEnum::Yes,
            guard_name  : 'web'
        );

        $array = $roleDTO->toArrayForCreate();

        expect($array)->not->toHaveKey('id')
            ->and($array)->not->toHaveKey('uuid')
            ->and($array)->not->toHaveKey('created_at')
            ->and($array)->not->toHaveKey('updated_at')
            ->and($array['name'])->toBe('user')
            ->and($array['guard_name'])->toBe('web')
            ->and($array['default_role'])->toBe(YesNoEnum::Yes);
    });

    test('handles null default_role properly', function () {
        $roleDTO = new RoleDTO(
            name        : 'guest',
            default_role: null,
            guard_name  : 'web'
        );

        $createArray = $roleDTO->toArrayForCreate();
        $fullArray   = $roleDTO->toArray();

        expect($createArray['default_role'])->toBeNull()
            ->and($fullArray['default_role'])->toBeNull();
    });

    test('preserves enum type in arrays when set', function () {
        $roleDTO = new RoleDTO(
            name        : 'admin',
            default_role: YesNoEnum::Yes,
            guard_name  : 'web'
        );

        $createArray = $roleDTO->toArrayForCreate();
        $fullArray   = $roleDTO->toArray();

        expect($createArray['default_role'])->toBe(YesNoEnum::Yes)
            ->and($fullArray['default_role'])->toBe(YesNoEnum::Yes)
            ->and($createArray['default_role'])->toBeInstanceOf(YesNoEnum::class);
    });
});
