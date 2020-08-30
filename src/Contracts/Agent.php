<?php

declare(strict_types=1);

namespace Arcanedev\Agent\Contracts;

use Illuminate\Http\Request;

/**
 * Interface  Agent
 *
 * @package   Arcanedev\Agent\Contracts
 * @author    ARCANEDEV <arcanedev.maroc@gmail.com>
 */
interface Agent
{
    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Set the request instance.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Arcanedev\Agent\Agent
     */
    public function setRequest(Request $request);

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Parse the given request.
     *
     * @param  \Illuminate\Http\Request|null  $request
     *
     * @return \Arcanedev\Agent\Contracts\Agent
     */
    public function parse(Request $request = null): Agent;

    /**
     * Make a detector.
     *
     * @param  string  $key
     *
     * @return \Arcanedev\Agent\Contracts\Detector|mixed
     */
    public function detector(string $key): Detector;
}
