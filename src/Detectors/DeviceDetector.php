<?php

declare(strict_types=1);

namespace Arcanedev\Agent\Detectors;

use Arcanedev\Agent\Contracts\Detector;
use DeviceDetector\DeviceDetector as BaseDetector;
use Illuminate\Http\Request;

/**
 * Class     DeviceDetector
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @mixin  \DeviceDetector\DeviceDetector
 */
class DeviceDetector implements Detector
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \DeviceDetector\DeviceDetector */
    protected $detector;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Handle the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return $this
     */
    public function handle(Request $request): Detector
    {
        $userAgent = $request->server('HTTP_USER_AGENT');

        $this->detector = tap(new BaseDetector($userAgent), function (BaseDetector $detector) {
            $detector->parse();
        });

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Accessors
     | -----------------------------------------------------------------
     */

    /**
     * Get the OS's name.
     *
     * @return string
     */
    public function osName(): string
    {
        return $this->getOs('name');
    }

    /**
     * Get the OS's short name.
     *
     * @return string
     */
    public function osShortName(): string
    {
        return $this->getOs('short_name');
    }

    /**
     * Get the OS's version.
     *
     * @return string
     */
    public function osVersion(): string
    {
        return $this->getOs('version');
    }

    /**
     * Get the client name.
     *
     * @return string
     */
    public function clientName()
    {
        return $this->getClient('name');
    }

    /**
     * Get the client name.
     *
     * @return string
     */
    public function clientShortName()
    {
        return $this->getClient('short_name');
    }

    /**
     * Get the client's version.
     *
     * @return string
     */
    public function clientVersion(): string
    {
        return $this->getClient('version');
    }

    /**
     * Get the bot's name.
     *
     * @return string
     */
    public function botName(): string
    {
        return $this->getBot()['name'] ?? BaseDetector::UNKNOWN;
    }

    /**
     * Check if it's a visitor.
     *
     * @return bool
     */
    public function isVisitor(): bool
    {
        return ! $this->isBot();
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check the given name matches client's name.
     *
     * @param  string  $name
     *
     * @return bool
     */
    public function isClientName($name): bool
    {
        return in_array($name, [
            $this->clientName(),
            $this->clientShortName(),
        ]);
    }

    /**
     * Check the given name matches Operating system's name.
     *
     * @param  string  $name
     *
     * @return bool
     */
    public function isOsName($name): bool
    {
        return in_array($name, [
            $this->osName(),
            $this->osShortName(),
        ]);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * @param  string  $name
     * @param  array   $params
     *
     * @return mixed
     */
    public function __call($name, $params)
    {
        return call_user_func_array([$this->detector, $name], $params);
    }
}
