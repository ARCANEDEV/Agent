<?php namespace Arcanedev\Agent\Tests;

/**
 * Class     AgentTest
 *
 * @package  Arcanedev\Agent\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class AgentTest extends TestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var  \Arcanedev\Agent\Agent */
    private $agent;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    public function setUp()
    {
        parent::setUp();

        $this->agent = $this->app->make(\Arcanedev\Agent\Contracts\Agent::class);
    }

    public function tearDown()
    {
        unset($this->agent);

        parent::tearDown();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
     */
    /** @test */
    public function it_can_be_instantiated()
    {
        $expectations = [
            \Arcanedev\Agent\Contracts\Agent::class,
            \Arcanedev\Agent\Agent::class,
        ];

        foreach ($expectations as $expected) {
            $this->assertInstanceOf($expected, $this->agent);
        }
    }

    /** @test */
    public function it_can_get_languages()
    {
        $this->agent->setHttpHeaders([
            'HTTP_ACCEPT_LANGUAGE' => 'nl-NL,nl;q=0.8,en-US;q=0.6,en;q=0.4',
        ]);

        $this->assertEquals(['nl-nl', 'nl', 'en-us', 'en'], $this->agent->languages());
    }

    /** @test */
    public function it_can_get_languages_sorted()
    {
        $this->agent->setHttpHeaders([
            'HTTP_ACCEPT_LANGUAGE' => 'en;q=0.4,en-US,nl;q=0.6',
        ]);

        $this->assertEquals(['en-us', 'nl', 'en'], $this->agent->languages());
    }

    /**
     * @test
     *
     * @dataProvider  provideBrowsersData
     *
     * @param  string  $ua
     * @param  string  $browser
     */
    public function it_can_check_browsers($ua, $browser)
    {
        $this->agent->setUserAgent($ua);

        $this->assertEquals($browser, $this->agent->browser(), $ua);
        $this->assertTrue($this->agent->is($browser), $browser);

        if ( ! strpos($browser, ' ')) {
            $method = "is{$browser}";
            $this->assertTrue($this->agent->{$method}(), $ua);
        }
    }

    /** @test */
    public function it_can_get_browser_version()
    {
        foreach($this->browserVersions as $ua => $version) {
            $this->agent->setUserAgent($ua);

            $browser = $this->agent->browser();

            $this->assertEquals($version, $this->agent->version($browser), $ua);
        }
    }

    /**
     * @test
     *
     * @dataProvider  provideOperatingSystemsData
     *
     * @param  string  $ua
     * @param  string  $platform
     */
    public function it_can_check_operating_systems($ua, $platform)
    {
        $this->agent->setUserAgent($ua);
        $this->assertEquals($platform, $this->agent->platform(), $ua);
        $this->assertTrue($this->agent->is($platform), $platform);

        if ( ! strpos($platform, ' ')) {
            $method = "is{$platform}";
            $this->assertTrue($this->agent->{$method}(), $ua);
        }
    }

    /** @test */
    public function it_can_get_os_version()
    {
        foreach($this->operatingSystemVersions as $ua => $version) {
            $this->agent->setUserAgent($ua);

            $platform = $this->agent->platform();

            $this->assertEquals($version, $this->agent->version($platform), $ua);
        }
    }

    /** @test */
    public function it_can_check_is_desktop()
    {
        foreach($this->desktops as $ua) {
            $this->agent->setUserAgent($ua);

            $this->assertTrue($this->agent->isDesktop(), $ua);

            $this->assertFalse($this->agent->isMobile(), $ua);
            $this->assertFalse($this->agent->isTablet(), $ua);
            $this->assertFalse($this->agent->isPhone(), $ua);
            $this->assertFalse($this->agent->isRobot(), $ua);
        }
    }

    /** @test */
    public function it_can_check_is_phone()
    {
        foreach($this->phones as $ua) {
            $this->agent->setUserAgent($ua);

            $this->assertTrue($this->agent->isPhone(), $ua);
            $this->assertTrue($this->agent->isMobile(), $ua);

            $this->assertFalse($this->agent->isDesktop(), $ua);
            $this->assertFalse($this->agent->isTablet(), $ua);
            $this->assertFalse($this->agent->isRobot(), $ua);
        }
    }

    /** @test
     *
     * @dataProvider  provideRobotsData
     *
     * @param  string  $ua
     * @param  string  $robot
     */
    public function it_can_check_is_robot($ua, $robot)
    {
        $this->agent->setUserAgent($ua);

        $this->assertTrue($this->agent->isRobot(), $ua);
        $this->assertEquals($robot, $this->agent->robot());

        $this->assertFalse($this->agent->isDesktop(), $ua);
        $this->assertFalse($this->agent->isMobile(), $ua);
        $this->assertFalse($this->agent->isTablet(), $ua);
        $this->assertFalse($this->agent->isPhone(), $ua);
    }

    /** @test */
    public function it_can_check_is_mobile_device()
    {
        foreach($this->mobileDevices as $ua => $device) {
            $this->agent->setUserAgent($ua);

            $this->assertTrue($this->agent->isMobile(), $ua);
            $this->assertEquals($device, $this->agent->device(), $ua);

            $this->assertFalse($this->agent->isDesktop(), $ua);
            $this->assertFalse($this->agent->isRobot(), $ua);

            if ( ! strpos($device, ' ')) {
                $method = "is{$device}";
                $this->assertTrue($this->agent->{$method}(), "Error: [$method] method on [$ua] user agent");
            }
        }
    }

    /** @test */
    public function it_can_check_is_desktop_device()
    {
        foreach($this->desktopDevices as $ua => $device) {
            $this->agent->setUserAgent($ua);

            $this->assertTrue($this->agent->isDesktop(), $ua);
            $this->assertEquals($device, $this->agent->device(), $ua);

            $this->assertFalse($this->agent->isMobile(), $ua);
            $this->assertFalse($this->agent->isTablet(), $ua);
            $this->assertFalse($this->agent->isPhone(), $ua);
            $this->assertFalse($this->agent->isRobot(), $ua);

            if ( ! strpos($device, ' ')) {
                $method = "is{$device}";
                $this->assertTrue($this->agent->{$method}(), "Error: [$method] method on [$ua] user agent");
            }
        }
    }

    /* ------------------------------------------------------------------------------------------------
     |  Data
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the Operating Systems.
     *
     * @return array
     */
    public function provideOperatingSystemsData()
    {
        return [
            [
                'Mozilla/5.0 (Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko',
                'Windows',
            ],[
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2',
                'OS X',
            ],[
                'Mozilla/5.0 (iPad; CPU OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko ) Version/5.1 Mobile/9B176 Safari/7534.48.3',
                'iOS',
            ],[
                'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:24.0) Gecko/20100101 Firefox/24.0',
                'Ubuntu',
            ],[
                'Mozilla/5.0 (BlackBerry; U; BlackBerry 9900; en) AppleWebKit/534.11+ (KHTML, like Gecko) Version/7.1.0.346 Mobile Safari/534.11+',
                'BlackBerryOS',
            ],[
                'Mozilla/5.0 (Linux; U; Android 2.2; en-us; Nexus One Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
                'AndroidOS',
            ],[
                'Mozilla/5.0 (X11; CrOS x86_64 6680.78.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.102 Safari/537.36',
                'ChromeOS'
            ],
        ];
    }

    /**
     * Get the Browsers.
     *
     * @return array
     */
    public function provideBrowsersData()
    {
        return [
            [
                'Mozilla/5.0 (Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko',
                'IE',
            ],[
                'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25',
                'Safari',
            ],[
                'Mozilla/5.0 (Windows; U; Win 9x 4.90; SG; rv:1.9.2.4) Gecko/20101104 Netscape/9.1.0285',
                'Netscape',
            ],[
                'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0',
                'Firefox',
            ],[
                'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36',
                'Chrome',
            ],[
                'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201',
                'Mozilla',
            ],[
                'Opera/9.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14',
                'Opera',
            ],[
                'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.115 Safari/537.36 OPR/27.0.1689.76',
                'Opera',
            ],[
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12',
                'Edge',
            ],[
                'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25',
                'Safari',
            ],[
                'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36 Vivaldi/1.2.490.43',
                'Vivaldi',
            ],
        ];
    }

    /**
     * Get the Robots.
     *
     * @return array
     */
    public function provideRobotsData()
    {
        return [
            [
                'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
                'Google',
            ],[
                'facebookexternalhit/1.1 (+http(s)://www.facebook.com/externalhit_uatext.php)',
                'Facebook',
            ],[
                'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)',
                'Bing',
            ],[
                'Twitterbot/1.0',
                'Twitter',
            ],[
                'Mozilla/5.0 (compatible; YandexBot/3.0; +http://yandex.com/bots)',
                'Yandex',
            ],
        ];
    }

    private $mobileDevices = [
        'Mozilla/5.0 (iPhone; U; ru; CPU iPhone OS 4_2_1 like Mac OS X; ru) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148a Safari/6533.18.5' => 'iPhone',
        'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25' => 'iPad',
        'Mozilla/5.0 (Linux; U; Android 2.3.4; fr-fr; HTC Desire Build/GRJ22) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1' => 'HTC',
        'Mozilla/5.0 (BlackBerry; U; BlackBerry 9900; en) AppleWebKit/534.11+ (KHTML, like Gecko) Version/7.1.0.346 Mobile Safari/534.11+' => 'BlackBerry',
        'Mozilla/5.0 (Linux; U; Android 2.2; en-us; Nexus One Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1' => 'Nexus',
        'Mozilla/5.0 (Linux; U; Android 4.0.3; en-us; ASUS Transformer Pad TF300T Build/IML74K) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Safari/534.30' => 'AsusTablet',
    ];

    private $desktopDevices = [
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11) AppleWebKit/601.1.56 (KHTML, like Gecko) Version/9.0 Safari/601.1.56' => 'Macintosh',
    ];

    private $browserVersions = [
        'Mozilla/5.0 (compatible; MSIE 10.6; Windows NT 6.1; Trident/5.0; InfoPath.2; SLCC1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 2.0.50727) 3gpp-gba UNTRUSTED/1.0' => '10.6',
        'Mozilla/5.0 (Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko' => '11.0',
        'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25' => '6.0',
        'Mozilla/5.0 (Windows; U; Win 9x 4.90; SG; rv:1.9.2.4) Gecko/20101104 Netscape/9.1.0285' => '9.1.0285',
        'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0' => '25.0',
        'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36' => '32.0.1667.0',
        'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201' => '2.2',
        'Opera/9.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14' => '12.14',
        'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; de) Opera 11.51' => '11.51',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12' => '12',
        'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36 Vivaldi/1.2.490.43' => '1.2.490.43',
    ];

    private $operatingSystemVersions = [
        'Mozilla/5.0 (Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko' => '6.3',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2' => '10_6_8',
        'Mozilla/5.0 (iPad; CPU OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko ) Version/5.1 Mobile/9B176 Safari/7534.48.3' => '5_1',
        'Mozilla/5.0 (BlackBerry; U; BlackBerry 9900; en) AppleWebKit/534.11+ (KHTML, like Gecko) Version/7.1.0.346 Mobile Safari/534.11+' => '7.1.0.346',
        'Mozilla/5.0 (Linux; U; Android 2.2; en-us; Nexus One Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1' => '2.2',
        'Mozilla/5.0 (X11; CrOS x86_64 6680.78.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.102 Safari/537.36' => '6680.78.0',
    ];

    private $desktops = [
        'Mozilla/5.0 (Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko',
        'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:24.0) Gecko/20100101 Firefox/24.0',
        'Mozilla/5.0 (Windows; U; Win 9x 4.90; SG; rv:1.9.2.4) Gecko/20101104 Netscape/9.1.0285',
        'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0',
        'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36',
        'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201',
        'Opera/9.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2',
    ];

    private $phones = [
        'Mozilla/5.0 (iPhone; U; ru; CPU iPhone OS 4_2_1 like Mac OS X; ru) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148a Safari/6533.18.5',
        'Mozilla/5.0 (Linux; U; Android 2.3.4; fr-fr; HTC Desire Build/GRJ22) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
        'Mozilla/5.0 (BlackBerry; U; BlackBerry 9900; en) AppleWebKit/534.11+ (KHTML, like Gecko) Version/7.1.0.346 Mobile Safari/534.11+',
        'Mozilla/5.0 (Linux; U; Android 2.2; en-us; Nexus One Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
    ];
}
