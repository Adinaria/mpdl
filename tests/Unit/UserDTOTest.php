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

        expect($userDTO->id)->toBe(1);
        expect($userDTO->uuid)->toBe('test-uuid-123');
        expect($userDTO->name)->toBe('John');
        expect($userDTO->last_name)->toBe('Doe');
        expect($userDTO->email)->toBe('john@example.com');
        expect($userDTO->password)->toBe('hashed_password');
    });

    test('creates UserDTO with minimal required properties', function () {
        $userDTO = new UserDTO(
            name     : 'John',
            last_name: 'Doe',
            email    : 'john@example.com',
            password : 'password123'
        );

        expect($userDTO->name)->toBe('John');
        expect($userDTO->last_name)->toBe('Doe');
        expect($userDTO->email)->toBe('john@example.com');
        expect($userDTO->password)->toBe('password123');
        expect($userDTO->id)->toBeNull();
        expect($userDTO->uuid)->toBeNull();
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
        ]);

        expect($array['id'])->toBe(1);
        expect($array['name'])->toBe('John');
        expect($array['email'])->toBe('john@example.com');
        expect($array['email_verified_at'])->toBeNull();
        expect($array['updated_at'])->toBeNull();
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
        ]);

        expect($array)->not->toHaveKeys(['id', 'uuid', 'updated_at', 'created_at']);

        expect($array['name'])->toBe('John');
        expect($array['email'])->toBe('john@example.com');
        expect($array['password'])->toBe('password123');
    });

    test('toArrayForCreate excludes system fields', function () {
        $userDTO = new UserDTO(
            name     : 'John',
            last_name: 'Doe',
            email    : 'john@example.com',
            password : 'password123'
        );

        $array = $userDTO->toArrayForCreate();

        expect($array)->not->toHaveKey('id');
        expect($array)->not->toHaveKey('uuid');
        expect($array)->not->toHaveKey('created_at');
        expect($array)->not->toHaveKey('updated_at');
    });
});
