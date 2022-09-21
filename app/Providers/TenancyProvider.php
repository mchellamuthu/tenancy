<?php

namespace App\Providers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\ServiceProvider;

class TenancyProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRequests();
        $this->configureQueue();
    }

    public function configureRequests()
    {
        if (!$this->app->runningInConsole()) {
            $api = $this->app['request']->segment(1);
            $domain = $this->app['request']->segment(2);
            if($api=='api'){
                Tenant::whereName($domain)->firstOrFail()->connect()->use();
            }
        }
    }

    public function configureQueue()
    {
        $this->app['queue']->createPayloadUsing(function () {
            return $this->app['tenant'] ?  ['tenant_id' => $this->app['tenant']->id] : [];
        });
        $this->app['events']->listen(JobProcessing::class, function ($event) {
            if (isset($event->job->payload()['tenant_id'])) {
                Tenant::find($event->job->payload()['tenant_id'])->connect()->use();
            }
        });
    }
}
