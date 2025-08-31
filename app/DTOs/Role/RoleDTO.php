<?php

namespace App\DTOs\Role;

use App\Enums\YesNoEnum;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

final class RoleDTO extends Data
{
    public function __construct(
        public ?int $id = null,
        public ?string $uuid = null,
        public ?string $name = null,
        #[WithCast(EnumCast::class, YesNoEnum::class)]
        public string|null|YesNoEnum $default_role = null,
        public ?string $guard_name = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {
    }


    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'uuid'         => $this->uuid,
            'name'         => $this->name,
            'guard_name'   => $this->guard_name,
            'created_at'   => $this->created_at,
            'default_role' => $this->default_role,
            'updated_at'   => $this->updated_at,
        ];
    }

    public function toArrayForCreate(): array
    {
        return [
            'name'         => $this->name,
            'guard_name'   => $this->guard_name,
            'default_role' => $this->default_role,
        ];
    }
}
