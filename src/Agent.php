<?php

declare(strict_types=1);

namespace Arcanedev\Agent;

use Arcanedev\Agent\Contracts\{Agent as AgentContract, Detector};
use BadMethodCallException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

/**
 * Class     Agent
 *
 * @package  Arcanedev\Agent
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @method  \Arcanedev\Agent\Detectors\DeviceDetector   drive()
 * @method  \Arcanedev\Agent\Detectors\LanguageDetector language()
 */
class Agent implements AgentContract
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Parsed request.
     *
     * @var array
     */
    protected $parsed;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Agent constructor.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Set the request instance.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get the request instance.
     *
     * @return \Illuminate\Http\Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Get the detectors.
     *
     * @return array
     */
    protected function detectors(): array
    {
        return $this->app['config']['agent.detectors'];
    }

    /**
     * Get the supported detectors
     *
     * @return array
     */
    protected function supportedDetectors(): array
    {
        return array_keys($this->detectors());
    }

    /**
     * @param  string  $key
     *
     * @return \Arcanedev\Agent\Contracts\Detector|mixed
     */
    protected function getParsed(string $key): Detector
    {
        return $this->parsed[$key];
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Parse the given request.
     *
     * @param  \Illuminate\Http\Request|null  $request
     *
     * @return $this
     */
    public function parse(Request $request = null): AgentContract
    {
        if ( ! is_null($request)) {
            $this->setRequest($request);
        }

        foreach ($this->supportedDetectors() as $detector) {
            $this->parsed[$detector] = $this->detector($detector)->handle($this->getRequest());
        }

        return $this;
    }

    /**
     * Make a detector.
     *
     * @param  string  $key
     *
     * @return \Arcanedev\Agent\Contracts\Detector
     */
    public function detector(string $key): Detector
    {
        $detector = $this->detectors()[$key];

        return $this->app->make($detector['driver']);
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if the detector exists.
     *
     * @param  string  $name
     *
     * @return bool
     */
    protected function hasDetector(string $name): bool
    {
        return array_key_exists($name, $this->detectors());
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * @param  string  $name
     * @param  array   $params
     *
     * @return \Arcanedev\Agent\Contracts\Detector
     */
    public function __call($name, $params)
    {
        if ($this->hasDetector($name)) {
            return $this->getParsed($name);
        }

        throw new BadMethodCallException("Method [{$name}] not found");
    }
}
