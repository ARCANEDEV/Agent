<?php namespace Arcanedev\Agent;

use Arcanedev\Support\PackageServiceProvider;

/**
 * Class     AgentServiceProvider
 *
 * @package  Arcanedev\Agent
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class AgentServiceProvider extends PackageServiceProvider
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'agent';

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the base path of the package.
     *
     * @return string
     */
    public function getBasePath()
    {
        return dirname(__DIR__);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Register the service provider.
     */
    public function register()
    {
        parent::register();

        $this->singleton(Contracts\Agent::class, function ($app) {
            /** @var  \Illuminate\Http\Request  $request */
            $request = $app['request'];

            return new Agent($request->server->all());
        });
    }

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Contracts\Agent::class,
        ];
    }
}
