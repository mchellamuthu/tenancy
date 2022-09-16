<?php

namespace App\Models;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
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
            tenant_connect(
                $this->db_host,
                $this->db_username,
                $this->db_password,
                $this->db_name
            );
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
}
