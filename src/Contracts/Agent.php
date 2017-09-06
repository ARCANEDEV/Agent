<?php namespace Arcanedev\Agent\Contracts;

use Mobile_Detect;

/**
 * Interface  Agent
 *
 * @package   Arcanedev\Agent\Contracts
 * @author    ARCANEDEV <arcanedev.maroc@gmail.com>
 */
interface Agent
{
    /* -----------------------------------------------------------------
     |  Setters & Getters
     | -----------------------------------------------------------------
     */

    /**
     * Set the User-Agent to be used.
     *
     * @param  string|null  $userAgent  The user agent string to set.
     *
     * @return string|null
     */
    public function setUserAgent($userAgent = null);

    /**
     * Set the HTTP Headers. Must be PHP-flavored. This method will reset existing headers.
     *
     * @param  array $httpHeaders  The headers to set. If null, then using PHP's _SERVER to extract
     *                             the headers. The default null is left for backwards compatibility.
     */
    public function setHttpHeaders($httpHeaders = null);

    /**
     * Get the crawler detector.
     *
     * @return \Arcanedev\Agent\Detectors\CrawlerDetector
     */
    public function getCrawlerDetector();

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
     * @return string|float           The version of the property we are trying to extract.
     */
    public function version($propertyName, $type = Mobile_Detect::VERSION_TYPE_STRING);

    /**
     * Get the device name.
     *
     * @param  string|null  $userAgent
     *
     * @return string
     */
    public function device($userAgent = null);

    /**
     * Get the browser name.
     *
     * @param  string|null  $userAgent
     *
     * @return string
     */
    public function browser($userAgent = null);

    /**
     * Get the robot name.
     *
     * @param  string|null  $userAgent
     *
     * @return string
     */
    public function robot($userAgent = null);

    /**
     * Get the platform name.
     *
     * @param  string|null  $userAgent
     *
     * @return string
     */
    public function platform($userAgent = null);

    /**
     * Get the languages.
     *
     * @param  string|null  $acceptLanguage
     *
     * @return array
     */
    public function languages($acceptLanguage = null);

    /**
     * Check if the device is mobile.
     * Returns true if any type of mobile device detected, including special ones
     *
     * @param  null  $userAgent    deprecated
     * @param  null  $httpHeaders  deprecated
     *
     * @return bool
     */
    public function isMobile($userAgent = null, $httpHeaders = null);

    /**
     * Check if the device is a tablet.
     * Return true if any type of tablet device is detected.
     *
     * @param  string  $userAgent    deprecated
     * @param  array   $httpHeaders  deprecated
     *
     * @return bool
     */
    public function isTablet($userAgent = null, $httpHeaders = null);

    /**
     * Check if the device is a desktop computer.
     *
     * @return bool
     */
    public function isDesktop();

    /**
     * Check if device is a robot.
     *
     * @param  string|null  $userAgent
     *
     * @return bool
     */
    public function isRobot($userAgent = null);

    /**
     * Check if the device is a mobile phone.
     *
     * @return bool
     */
    public function isPhone();

    /**
     * This method checks for a certain property in the userAgent.
     *
     * @param  string  $key
     * @param  string  $userAgent    deprecated
     * @param  string  $httpHeaders  deprecated
     *
     * @return bool|int|null
     */
    public function is($key, $userAgent = null, $httpHeaders = null);
}
