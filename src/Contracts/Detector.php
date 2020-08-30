<?php

declare(strict_types=1);

namespace Arcanedev\Agent\Contracts;

use Illuminate\Http\Request;

/**
 * Interface     Detector
 *
 * @package  Arcanedev\Agent\Contracts
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
interface Detector
{
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
    public function handle(Request $request): Detector;
}
