<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('vlans', function (Blueprint $table) {
            if (!Schema::hasColumn('vlans', 'port_mode')) {
                $table->string('port_mode', 20)->nullable()->after('uplink_port');
            }
            if (!Schema::hasColumn('vlans', 'native_port')) {
                $table->string('native_port', 30)->nullable()->after('port_mode');
            }
            if (!Schema::hasColumn('vlans', 'native_vlan')) {
                $table->integer('native_vlan')->nullable()->after('native_port');
            }
            if (!Schema::hasColumn('vlans', 'vlanif_ip')) {
                $table->string('vlanif_ip', 45)->nullable()->after('native_vlan');
            }
            if (!Schema::hasColumn('vlans', 'vlanif_netmask')) {
                $table->string('vlanif_netmask', 45)->nullable()->after('vlanif_ip');
            }
        });
    }

    public function down(): void {
        Schema::table('vlans', function (Blueprint $table) {
            if (Schema::hasColumn('vlans', 'vlanif_netmask')) $table->dropColumn('vlanif_netmask');
            if (Schema::hasColumn('vlans', 'vlanif_ip')) $table->dropColumn('vlanif_ip');
            if (Schema::hasColumn('vlans', 'native_vlan')) $table->dropColumn('native_vlan');
            if (Schema::hasColumn('vlans', 'native_port')) $table->dropColumn('native_port');
            if (Schema::hasColumn('vlans', 'port_mode')) $table->dropColumn('port_mode');
        });
    }
};
