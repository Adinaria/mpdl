<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('uuid')->index()->nullable()->default(null)->after('id');
            $table->string('default_role')->index()->nullable()->default(null)
                ->comment('Признак, по которому нельзя будет управлять ролью, так как она дефолтная');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('uuid');
            $table->dropSoftDeletes();
        });
    }
};
