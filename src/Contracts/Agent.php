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
}
