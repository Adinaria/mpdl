<?php

namespace Database\Seeders;

use App\Models\Role\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DefaultRoleSeeder extends Seeder
{
    public function run(): void
    {
        $defaultRoles      = collect(config('default_roles'));
        $nameRoles         = $this->getNameRoles();
        $needToCreateRoles = $defaultRoles->diff($nameRoles);

        $this->createRoles($needToCreateRoles);
    }

    private function getNameRoles(): Collection
    {
        return Role::query()
            ->pluck('name')
            ->collect();
    }

    private function createRoles(Collection $roles): void
    {
        // запись в бд через цикл, из-за того, что обычно роли пачками не не появляются в проекте
        foreach ($roles as $role) {
            Role::query()->create(['name' => $role]);
        }
    }
}
