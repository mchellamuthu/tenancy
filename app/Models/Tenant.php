<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    /**
     * Establish a connection with the tenant's database.
     */
    public function connect()
    {

        if (!$this->connected()) {

            // Erase the tenant connection, thus making Laravel get the default values all over again.
            DB::purge('tenant');
            // Make sure to use the database name we want to establish a connection.
            Config::set('database.connections.tenant.host', $this->db_host);
            Config::set('database.connections.tenant.database', $this->db_name);
            Config::set('database.connections.tenant.username', $this->db_username);
            Config::set('database.connections.tenant.password', $this->db_password);
            // Rearrange the connection data
            DB::reconnect('tenant');
            // Ping the database. This will throw an exception in case the database does not exists.
            Schema::connection('tenant')->getConnection()->reconnect();
        }
    }
    /**
     * Check if the current tenant connection settings matches the company's database settings.
     *
     * @return bool
     */
    private function connected()
    {
        $connection = Config::get('database.connections.tenant');
        return $connection['username'] == $this->db_username &&
            $connection['password'] == $this->db_password &&
            $connection['database'] == $this->db_name;
    }

     /**
     * Run Tenant Migrations in the connected tenant database.
     */
   public function migrate()
    {

        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations_tenant',
            '--force' => true
        ]);
    }
}
