<?php

use App\DTOs\User\UserDTO;

describe('UserDTO', function () {
    test('creates UserDTO with all properties', function () {
        $userDTO = new UserDTO(
            id               : 1,
            uuid             : 'test-uuid-123',
            name             : 'John',
            last_name        : 'Doe',
            email            : 'john@example.com',
            email_verified_at: '2023-01-01 00:00:00',
            password         : 'hashed_password',
            remember_token   : 'remember_token',
            updated_at       : '2023-01-01 00:00:00',
            created_at       : '2023-01-01 00:00:00'
        );

        expect($userDTO->id)->toBe(1)
            ->and($userDTO->uuid)->toBe('test-uuid-123')
            ->and($userDTO->name)->toBe('John')
            ->and($userDTO->last_name)->toBe('Doe')
            ->and($userDTO->email)->toBe('john@example.com')
            ->and($userDTO->password)->toBe('hashed_password');
    });

    test('creates UserDTO with minimal required properties', function () {
        $userDTO = new UserDTO(
            name     : 'John',
            last_name: 'Doe',
            email    : 'john@example.com',
            password : 'password123'
        );

        expect($userDTO->name)->toBe('John')
            ->and($userDTO->last_name)->toBe('Doe')
            ->and($userDTO->email)->toBe('john@example.com')
            ->and($userDTO->password)->toBe('password123')
            ->and($userDTO->id)->toBeNull()
            ->and($userDTO->uuid)->toBeNull();
    });

    test('toArray returns all properties including nulls and timestamps', function () {
        $userDTO = new UserDTO(
            id       : 1,
            uuid     : 'test-uuid-123',
            name     : 'John',
            last_name: 'Doe',
            email    : 'john@example.com',
            password : 'password123'
        );

        $array = $userDTO->toArray();

        expect($array)->toHaveKeys([
            'id',
            'uuid',
            'name',
            'last_name',
            'email',
            'email_verified_at',
            'password',
            'remember_token',
            'updated_at',
            'created_at'
        ])
            ->and($array['id'])->toBe(1)
            ->and($array['name'])->toBe('John')
            ->and($array['email'])->toBe('john@example.com')
            ->and($array['email_verified_at'])->toBeNull()
            ->and($array['updated_at'])->toBeNull();
    });

    test('toArrayForCreate returns only creation-relevant fields', function () {
        $userDTO = new UserDTO(
            id               : 1,
            uuid             : 'test-uuid-123',
            name             : 'John',
            last_name        : 'Doe',
            email            : 'john@example.com',
            email_verified_at: '2023-01-01 00:00:00',
            password         : 'password123',
            remember_token   : 'token',
            updated_at       : '2023-01-01 00:00:00',
            created_at       : '2023-01-01 00:00:00'
        );

        $array = $userDTO->toArrayForCreate();

        expect($array)->toHaveKeys([
            'name',
            'last_name',
            'email',
            'email_verified_at',
            'password',
            'remember_token'
        ])
            ->and($array)->not->toHaveKeys(['id', 'uuid', 'updated_at', 'created_at'])
            ->and($array['name'])->toBe('John')
            ->and($array['email'])->toBe('john@example.com')
            ->and($array['password'])->toBe('password123');
    });

    test('toArrayForCreate excludes system fields', function () {
        $userDTO = new UserDTO(
            name     : 'John',
            last_name: 'Doe',
            email    : 'john@example.com',
            password : 'password123'
        );

        $array = $userDTO->toArrayForCreate();

        expect($array)->not->toHaveKey('id')
            ->and($array)->not->toHaveKey('uuid')
            ->and($array)->not->toHaveKey('created_at')
            ->and($array)->not->toHaveKey('updated_at');
    });
});
