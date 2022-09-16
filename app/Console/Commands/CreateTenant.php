<?php

namespace App\Console\Commands;

use PDO;
use App\Models\Tenant;
use Illuminate\Console\Command;

class CreateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create tenant';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tenantName = $this->argument('name');
        $dbhost = config('database.connections.tenant.host');
        $dbport = config('database.connections.tenant.port');
        $dbusername = config('database.connections.tenant.username');
        $dbpassword = config('database.connections.tenant.password');


        $data['db_host'] = $dbhost;
        $data['db_username'] = $dbusername;
        $data['db_password'] = $dbpassword;


        $pdo = $this->getPDOConnection(
            $dbhost,
            $dbport,
            $dbusername,
            $dbpassword
        );
        //Store database name
        $dbname = $this->getRealName($tenantName);
        $tenantExists = Tenant::where('name', $dbname)->first();
        if ($tenantExists) {
            $this->error('Tenant already exists!');
            return 0;
        }
        $tenant = Tenant::create([
            'name' => $dbname,
            'db_name' => $dbname,
            'db_host' => $dbhost,
            'db_username' => $dbusername,
            'db_password' => $dbpassword,
        ]);

        $pdo->exec(sprintf(
            'CREATE DATABASE IF NOT EXISTS %s CHARACTER SET %s COLLATE %s;',
            $dbname,
            'utf8mb4',
            'utf8mb4_unicode_ci'
        ));
        $tenant->connect();
        tenant_migrate();
        \App\Models\Tenants\User::factory(200)->create();

        $this->info('Tenant was created successful!');

        return 0;
    }

    /**
     * @param  string $host
     * @param  integer $port
     * @param  string $username
     * @param  string $password
     * @return PDO
     */
    private function getPDOConnection($host, $port, $username, $password)
    {
        return new PDO(sprintf('mysql:host=%s;port=%d;', $host, $port), $username, $password);
    }

    private  function getRealName($str)
    {
        $str = strtolower($str);
        $res = preg_replace('/[0-9\@\.\;\" "]+/', '', $str);
        return $res;
    }
}
