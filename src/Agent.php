<?php namespace Arcanedev\Agent;

use Arcanedev\Agent\Contracts\Agent as AgentContract;
use Mobile_Detect;

/**
 * Class     Agent
 *
 * @package  Arcanedev\Agent
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Agent extends Mobile_Detect implements AgentContract
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * List of desktop devices.
     *
     * @var array
     */
    protected static $additionalDevices = [
        'Macintosh' => 'Macintosh',
    ];

    /**
     * List of additional operating systems.
     *
     * @var array
     */
    protected static $additionalOperatingSystems = [
        'Windows'    => 'Windows',
        'Windows NT' => 'Windows NT',
        'OS X'       => 'Mac OS X',
        'Debian'     => 'Debian',
        'Ubuntu'     => 'Ubuntu',
        'Macintosh'  => 'PPC',
        'OpenBSD'    => 'OpenBSD',
        'Linux'      => 'Linux',
        'ChromeOS'   => 'CrOS',
    ];

    /**
     * List of additional browsers.
     * Note: 'Vivaldi' must be above Chrome, otherwise it'll fail.
     *
     * @var array
     */
    protected static $additionalBrowsers = [
        'Opera'    => 'Opera|OPR',
        'Edge'     => 'Edge',
        'Vivaldi'  => 'Vivaldi',
        'Chrome'   => 'Chrome',
        'Firefox'  => 'Firefox',
        'Safari'   => 'Safari',
        'IE'       => 'MSIE|IEMobile|MSIEMobile|Trident/[.0-9]+',
        'Netscape' => 'Netscape',
        'Mozilla'  => 'Mozilla',
    ];

    /**
     * List of additional properties.
     *
     * @var array
     */
    protected static $additionalProperties = [
        // Operating systems
        'Windows'      => 'Windows NT [VER]',
        'Windows NT'   => 'Windows NT [VER]',
        'OS X'         => 'OS X [VER]',
        'BlackBerryOS' => ['BlackBerry[\w]+/[VER]', 'BlackBerry.*Version/[VER]', 'Version/[VER]'],
        'AndroidOS'    => 'Android [VER]',
        'ChromeOS'     => 'CrOS x86_64 [VER]',

        // Browsers
        'Opera'    => [' OPR/[VER]', 'Opera Mini/[VER]', 'Version/[VER]', 'Opera [VER]'],
        'Netscape' => 'Netscape/[VER]',
        'Mozilla'  => 'rv:[VER]',
        'IE'       => ['IEMobile/[VER];', 'IEMobile [VER]', 'MSIE [VER];', 'rv:[VER]'],
        'Edge'     => 'Edge/[VER]',
        'Vivaldi'  => 'Vivaldi/[VER]',
    ];

    /**
     * List of robots.
     *
     * @var array
     */
    protected static $robots = [
        'Google'      => 'googlebot',
        'MSNBot'      => 'msnbot',
        'Baiduspider' => 'baiduspider',
        'Bing'        => 'bingbot',
        'Yahoo'       => 'yahoo',
        'Lycos'       => 'lycos',
        'Facebook'    => 'facebookexternalhit',
        'Twitter'     => 'Twitterbot',
        'Yandex'      => 'Yandex',
    ];

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get all detection rules. These rules include the additional
     * platforms and browsers.
     *
     * @return array
     */
    public function getDetectionRulesExtended()
    {
        static $rules;

        if ( ! $rules) {
            $rules = $this->mergeRules(
                static::$additionalDevices, // NEW
                static::getPhoneDevices(),
                static::getTabletDevices(),
                static::getOperatingSystems(),
                static::$additionalOperatingSystems, // NEW
                static::getBrowsers(),
                static::$additionalBrowsers, // NEW
                static::getUtilities()
            );
        }

        return $rules;
    }

    /**
     * Retrieve the current set of rules.
     *
     * @return array
     */
    public function getRules()
    {
        return $this->detectionType == static::DETECTION_TYPE_EXTENDED
            ? static::getDetectionRulesExtended()
            : parent::getRules();
    }

    /**
     * Get the device name.
     *
     * @param  string|null  $userAgent
     *
     * @return string
     */
    public function device($userAgent = null)
    {
        // Get device rules
        $rules = $this->mergeRules(
            static::$additionalDevices, // NEW
            static::$phoneDevices,
            static::$tabletDevices,
            static::$utilities
        );

        return $this->findDetectionRulesAgainstUA($rules, $userAgent);
    }

    /**
     * Get the browser name.
     *
     * @param  string|null  $userAgent
     *
     * @return string
     */
    public function browser($userAgent = null)
    {
        // Get browser rules
        $rules = $this->mergeRules(
            static::$additionalBrowsers, // NEW
            static::$browsers
        );

        return $this->findDetectionRulesAgainstUA($rules, $userAgent);
    }

    /**
     * Get the robot name.
     *
     * @param  string|null  $userAgent
     *
     * @return string
     */
    public function robot($userAgent = null)
    {
        // Get bot rules
        $rules = $this->mergeRules(
            static::$robots, // NEW
            [static::$utilities['Bot']],
            [static::$utilities['MobileBot']]
        );

        return $this->findDetectionRulesAgainstUA($rules, $userAgent);
    }

    /**
     * Get the platform name.
     *
     * @param  string|null  $userAgent
     *
     * @return string
     */
    public function platform($userAgent = null)
    {
        // Get platform rules
        $rules = $this->mergeRules(
            static::$operatingSystems,
            static::$additionalOperatingSystems // NEW
        );

        return $this->findDetectionRulesAgainstUA($rules, $userAgent);
    }

    /**
     * Get the languages.
     *
     * @param  string|null  $acceptLanguage
     *
     * @return array
     */
    public function languages($acceptLanguage = null)
    {
        if ( ! $acceptLanguage) {
            $acceptLanguage = $this->getHttpHeader('HTTP_ACCEPT_LANGUAGE');
        }

        $languages = [];

        if ($acceptLanguage) {
            // Parse accept language string.
            foreach (explode(',', $acceptLanguage) as $piece) {
                $parts = explode(';', $piece);
                $language = strtolower($parts[0]);
                $priority = empty($parts[1]) ? 1. : floatval(str_replace('q=', '', $parts[1]));
                $languages[$language] = $priority;
            }

            // Sort languages by priority.
            arsort($languages);

            $languages = array_keys($languages);
        }

        return $languages;
    }

    /**
     * Match a detection rule and return the matched key.
     *
     * @param  array  $rules
     * @param  null   $userAgent
     *
     * @return string
     */
    protected function findDetectionRulesAgainstUA(array $rules, $userAgent = null)
    {
        // Loop given rules
        foreach ($rules as $key => $regex) {
            if (empty($regex)) continue;

            // Check match
            if ($this->match($regex, $userAgent))
                return $key ?: reset($this->matchesArray);
        }

        return false;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Check the version of the given property in the User-Agent.
     * Will return a float number. (eg. 2_0 will return 2.0, 4.3.1 will return 4.31)
     *
     * @param  string  $propertyName  The name of the property. See self::getProperties() array
     *                                keys for all possible properties.
     * @param  string  $type          Either self::VERSION_TYPE_STRING to get a string value or
     *                                self::VERSION_TYPE_FLOAT indicating a float value. This parameter is optional
     *                                and defaults to self::VERSION_TYPE_STRING. Passing an invalid parameter will
     *                                default to the this type as well.
     *
     * @return string|float The version of the property we are trying to extract.
     */
    public function version($propertyName, $type = self::VERSION_TYPE_STRING)
    {
        $check = key(static::$additionalProperties);

        // Check if the additional properties have been added already
        if ( ! array_key_exists($check, static::$properties)) {
            // TODO: why is mergeRules not working here?
            static::$properties = array_merge(
                static::$properties,
                static::$additionalProperties
            );
        }

        return parent::version($propertyName, $type);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Methods
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Check if the device is a desktop computer.
     *
     * @return bool
     */
    public function isDesktop()
    {
        return ! $this->isMobile() && ! $this->isTablet() && ! $this->isRobot();
    }

    /**
     * Check if device is a robot.
     *
     * @param  string|null  $userAgent
     *
     * @return bool
     */
    public function isRobot($userAgent = null)
    {
        // Get bot rules
        $rules = $this->mergeRules(
            [static::$utilities['Bot']],
            [static::$utilities['MobileBot']],
            static::$robots // NEW
        );

        foreach ($rules as $regex) {
            if ($this->match($regex, $userAgent))
                return true;
        }

        return false;
    }

    /**
     * Check if the device is a mobile phone.
     *
     * @return bool
     */
    public function isPhone()
    {
        return $this->isMobile() && ! $this->isTablet();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Merge multiple rules into one array.
     *
     * @param  array  $rulesGroups
     *
     * @return array
     */
    protected function mergeRules(...$rulesGroups)
    {
        $merged = [];

        foreach ($rulesGroups as $rules) {
            foreach ($rules as $key => $value) {
                if (empty($merged[$key])) {
                    $merged[$key] = $value;
                }
                else {
                    if (is_array($merged[$key])) {
                        $merged[$key][] = $value;
                    }
                    else {
                        $merged[$key] .= '|' . $value;
                    }
                }
            }
        }

        return $merged;
    }

    /**
     * Changing detection type to extended.
     *
     * @inherit
     *
     * @param  string  $name
     * @param  array   $arguments
     *
     * @return bool|mixed
     */
    public function __call($name, $arguments)
    {
        // Make sure the name starts with 'is', otherwise
        if (substr($name, 0, 2) != 'is') {
            throw new \BadMethodCallException("No such method exists: $name");
        }

        $this->setDetectionType(self::DETECTION_TYPE_EXTENDED);

        return $this->matchUAAgainstKey(substr($name, 2));
    }
}
