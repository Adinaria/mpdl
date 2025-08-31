<?php

namespace App\DTOs\User;


use App\DTOs\Role\RoleDTO;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

final class UserDTO extends Data
{
    public function __construct(
        public ?int $id = null,
        public ?string $uuid = null,
        public ?string $name = null,
        public ?string $last_name = null,
        public ?string $email = null,
        public ?string $email_verified_at = null,
        public ?string $password = null,
        public ?string $remember_token = null,
        public ?string $updated_at = null,
        public ?string $created_at = null,
        #[DataCollectionOf(RoleDTO::class)]
        public ?RoleDTO $roles = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id'                => $this->id,
            'uuid'              => $this->uuid,
            'name'              => $this->name,
            'last_name'         => $this->last_name,
            'email'             => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'password'          => $this->password,
            'remember_token'    => $this->remember_token,
            'updated_at'        => $this->updated_at,
            'created_at'        => $this->created_at,
        ];
    }


    public function toArrayForCreate(): array
    {
        return [
            'name'              => $this->name,
            'last_name'         => $this->last_name,
            'email'             => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'password'          => $this->password,
            'remember_token'    => $this->remember_token,
        ];
    }
}
