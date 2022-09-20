<?php

namespace App\Jobs\Tenants;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use App\Jobs\Tenants\TenantTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class AddUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tenant;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->tenant->connect();
        \App\Models\Tenants\User::factory(20)->create();
    }

    public function tags()
    {
        return ["tenant : ".$this->tenant->name];
    }
}
