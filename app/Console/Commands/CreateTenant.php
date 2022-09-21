<?php

namespace App\Console\Commands;

use PDO;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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

        $statement = sprintf(
            'CREATE DATABASE IF NOT EXISTS %s CHARACTER SET %s COLLATE %s;',
            $dbname,
            'utf8mb4',
            'utf8mb4_unicode_ci'
        );
        DB::connection('tenant')->statement($statement);

        $tenant->connect()->use();
        $tenant->migrate();
        $rand = mt_rand(50, 200);
        \App\Models\Tenants\User::factory($rand)->create();
        $this->call('horizon:supervisor',[
            'name'=>$tenant->name,
            'connection' =>'redis',
            '--queue' => $tenant->name,
            '--balance' => 'auto',
            '--max-processes' => 1,
            '--max-time' => 0,
            '--max-jobs' => 0,
            '--memory' => 128,
            '--tries' => 1,
            '--timeout' => 60,
            '--nice' => 0,
        ]);

        return 0;
    }

    private  function getRealName($str)
    {
        $str = strtolower($str);
        $res = preg_replace('/[0-9\@\.\;\" "]+/', '', $str);
        return $res;
    }
}
