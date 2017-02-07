<?php namespace Arcanedev\Agent\Facades;

use Arcanedev\Agent\Contracts\Agent as AgentContract;
use Illuminate\Support\Facades\Facade;

/**
 * Class     Agent
 *
 * @package  Arcanedev\Agent\Facades
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Agent extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return AgentContract::class; }
}
