<?php

declare(strict_types=1);

namespace Arcanedev\Agent\Detectors;

use Arcanedev\Agent\Contracts\Detector;
use Illuminate\Http\Request;

/**
 * Class     LanguageDetector
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class LanguageDetector implements Detector
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * @var array
     */
    protected $languages;

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the languages.
     *
     * @return array
     */
    public function languages(): array
    {
        return $this->languages;
    }

    /**
     * Get the languages keys.
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->languages());
    }

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
        $this->languages = [];
        $acceptLanguage  = $request->server('HTTP_ACCEPT_LANGUAGE');

        if ( ! empty($acceptLanguage)) {
            $this->parse($acceptLanguage);
        }

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Parse the accept language.
     *
     * @param  string  $acceptLanguage
     */
    protected function parse(string $acceptLanguage): void
    {
        // Parse accept language string.
        foreach (explode(',', $acceptLanguage) as $piece) {
            $parts = explode(';', $piece);
            $language = strtolower($parts[0]);
            $priority = empty($parts[1]) ? 1. : floatval(str_replace('q=', '', $parts[1]));
            $this->languages[$language] = $priority;
        }

        // Sort languages by priority.
        arsort($this->languages);
    }
}
