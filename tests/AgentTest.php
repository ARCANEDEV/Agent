<?php

declare(strict_types=1);

namespace Arcanedev\Agent\Tests;

use Illuminate\Http\Request;

/**
 * Class     AgentTest
 *
 * @package  Arcanedev\Agent\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class AgentTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\Agent\Agent */
    private $agent;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function setUp(): void
    {
        parent::setUp();

        $this->agent = $this->app->make(\Arcanedev\Agent\Contracts\Agent::class);
    }

    public function tearDown(): void
    {
        unset($this->agent);

        parent::tearDown();
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated(): void
    {
        $expectations = [
            \Arcanedev\Agent\Contracts\Agent::class,
            \Arcanedev\Agent\Agent::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $this->agent);
        }
    }

    /** @test */
    public function it_must_throw_exception_when_detector_not_found()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage("Method [crawler] not found");

        $this->agent->crawler();
    }

    /** @test */
    public function it_can_set_and_parse_request(): void
    {
        $request = Request::create('test-request', 'GET', [], [], [], [
            'HTTP_ACCEPT_LANGUAGE' => 'nl-NL,nl;q=0.8,en-US;q=0.6,en;q=0.4',
        ]);

        $this->agent->setRequest($request);

        $detector = $this->agent->parse()->language();

        static::assertEquals(['nl-nl', 'nl', 'en-us', 'en'], $detector->keys());
    }

    /** @test */
    public function it_can_get_languages(): void
    {
        $detector = $this->parseLanguage('nl-NL,nl;q=0.8,en-US;q=0.6,en;q=0.4');

        $expected = [
            'nl-nl' => 1.0,
            'nl'    => 0.8,
            'en-us' => 0.6,
            'en'    => 0.4,
        ];

        static::assertEquals($expected, $detector->languages());
        static::assertEquals(array_keys($expected), $detector->keys());
    }

    /** @test */
    public function it_can_get_languages_sorted(): void
    {
        $detector = $this->parseLanguage('en;q=0.4,en-US,nl;q=0.6');

        static::assertEquals(['en-us', 'nl', 'en'], $detector->keys());
    }

    /**
     * @test
     *
     * @dataProvider  provideBrowsersData
     *
     * @param  string  $userAgent
     * @param  array   $client
     */
    public function it_can_check_client(string $userAgent, array $client): void
    {
        $device = $this->parseDevice($userAgent);

        static::assertEquals($client['name'], $device->clientName(), $userAgent);
        static::assertEquals($client['short_name'], $device->clientShortName(), $userAgent);

        static::assertTrue($device->isClientName($client['name']), $userAgent);
        static::assertTrue($device->isClientName($client['short_name']), $userAgent);
    }

    /**
     * @test
     *
     * @dataProvider provideBrowserVersions
     *
     * @param  string  $userAgent
     * @param  string  $version
     */
    public function it_can_get_client_version(string $userAgent, string $version): void
    {
        $device = $this->parseDevice($userAgent);

        static::assertEquals($version, $device->clientVersion(), $userAgent);
    }

    /**
     * @test
     *
     * @dataProvider  provideOperatingSystemsData
     *
     * @param  string  $userAgent
     * @param  array   $os
     */
    public function it_can_check_operating_systems(string $userAgent, array $os): void
    {
        $device = $this->parseDevice($userAgent);

        static::assertEquals($os['name'], $device->osName(), $userAgent);
        static::assertEquals($os['short_name'], $device->osShortName(), $userAgent);

        static::assertTrue($device->isOsName($os['name']), $userAgent);
        static::assertTrue($device->isOsName($os['short_name']), $userAgent);
    }

    /**
     * @test
     *
     * @dataProvider  provideOperatingSystemsVersions
     *
     * @param  string  $userAgent
     * @param  string  $version
     */
    public function it_can_get_os_version(string $userAgent, string $version): void
    {
        $device = $this->parseDevice($userAgent);

        static::assertEquals($version, $device->osVersion(), $userAgent);
    }

    /**
     * @test
     *
     * @dataProvider provideDesktops
     *
     * @param string $userAgent
     */
    public function it_can_check_is_desktop(string $userAgent): void
    {
        $device = $this->parseDevice($userAgent);

        static::assertTrue($device->isDesktop(), $userAgent);

        static::assertFalse($device->isMobile(), $userAgent);
        static::assertFalse($device->isSmartphone(), $userAgent);
        static::assertFalse($device->isTablet(), $userAgent);
        static::assertFalse($device->isBot(), $userAgent);
    }

    /**
     * @test
     *
     * @dataProvider providePhones
     *
     * @param  string  $userAgent
     */
    public function it_can_check_is_phone(string $userAgent): void
    {
        $device = $this->parseDevice($userAgent);

        static::assertTrue($device->isSmartphone(), $userAgent);
        static::assertTrue($device->isMobile(), $userAgent);

        static::assertFalse($device->isDesktop(), $userAgent);
        static::assertFalse($device->isTablet(), $userAgent);
        static::assertFalse($device->isBot(), $userAgent);
    }

    /**
     * @test
     *
     * @dataProvider  provideRobotsData
     *
     * @param  string  $userAgent
     * @param  string  $robot
     */
    public function it_can_check_is_robot(string $userAgent, string $robot): void
    {
        $device = $this->parseDevice($userAgent);

        static::assertTrue($device->isBot(), $userAgent);

        static::assertFalse($device->isVisitor());
        static::assertFalse($device->isDesktop(), $userAgent);
        static::assertFalse($device->isMobile(), $userAgent);
        static::assertFalse($device->isTablet(), $userAgent);
        static::assertFalse($device->isSmartphone(), $userAgent);

        static::assertEquals($robot, $device->botName());
    }

    /**
     * @test
     *
     * @dataProvider provideMobilesData
     *
     * @param  string  $userAgent
     * @param  array   $mobile
     */
    public function it_can_check_is_mobile_device(string $userAgent, array $mobile): void
    {
        $device = $this->parseDevice($userAgent);

        static::assertTrue($device->isMobile(), $userAgent);

        static::assertFalse($device->isDesktop(), $userAgent);
        static::assertFalse($device->isBot(), $userAgent);

        static::assertEquals($mobile['brand'], $device->getBrandName(), $userAgent);
        static::assertEquals($mobile['model'], $device->getModel(), $userAgent);
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
    public function provideOperatingSystemsData(): array
    {
        return [
            [
                'Mozilla/5.0 (Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko',
                ['name' => 'Windows', 'short_name' => 'WIN'],
            ],[
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2',
                ['name' => 'Mac', 'short_name' => 'MAC'],
            ],[
                'Mozilla/5.0 (iPad; CPU OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko ) Version/5.1 Mobile/9B176 Safari/7534.48.3',
                ['name' => 'iOS', 'short_name' => 'IOS'],
            ],[
                'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:24.0) Gecko/20100101 Firefox/24.0',
                ['name' => 'Ubuntu', 'short_name' => 'UBT'],
            ],[
                'Mozilla/5.0 (BlackBerry; U; BlackBerry 9900; en) AppleWebKit/534.11+ (KHTML, like Gecko) Version/7.1.0.346 Mobile Safari/534.11+',
                ['name' => 'BlackBerry OS', 'short_name' => 'BLB'],
            ],[
                'Mozilla/5.0 (Linux; U; Android 2.2; en-us; Nexus One Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
                ['name' => 'Android', 'short_name' => 'AND'],
            ],[
                'Mozilla/5.0 (X11; CrOS x86_64 6680.78.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.102 Safari/537.36',
                ['name' => 'Chrome OS', 'short_name' => 'COS'],
            ],
        ];
    }

    /**
     * Get the Operating Systems' versions.
     *
     * @return array
     */
    public function provideOperatingSystemsVersions(): array
    {
        return [
            [
                'Mozilla/5.0 (Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko',
                '8.1',
            ],
            [
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2',
                '10.6',
            ],
            [
                'Mozilla/5.0 (iPad; CPU OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko ) Version/5.1 Mobile/9B176 Safari/7534.48.3',
                '5.1',
            ],
            [
                'Mozilla/5.0 (BlackBerry; U; BlackBerry 9900; en) AppleWebKit/534.11+ (KHTML, like Gecko) Version/7.1.0.346 Mobile Safari/534.11+',
                '7.1',
            ],
            [
                'Mozilla/5.0 (Linux; U; Android 2.2; en-us; Nexus One Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
                '2.2',
            ],
            [
                'Mozilla/5.0 (X11; CrOS x86_64 6680.78.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.102 Safari/537.36',
                '41.0',
            ],
        ];
    }

    /**
     * Get the Browsers.
     *
     * @return array
     */
    public function provideBrowsersData(): array
    {
        return [
            [
                'Mozilla/5.0 (Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko',
                ['name' => 'Internet Explorer', 'short_name' => 'IE'],
            ],[
                'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25',
                ['name' => 'Mobile Safari', 'short_name' => 'MF'],
            ],[
                'Mozilla/5.0 (Windows; U; Win 9x 4.90; SG; rv:1.9.2.4) Gecko/20101104 Netscape/9.1.0285',
                ['name' => 'Netscape', 'short_name' => 'NS'],
            ],[
                'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0',
                ['name' => 'Firefox', 'short_name' => 'FF'],
            ],[
                'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36',
                ['name' => 'Chrome', 'short_name' => 'CH'],
            ],[
                'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201',
                ['name' => 'UNK', 'short_name' => 'UNK'],
            ],[
                'Opera/9.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14',
                ['name' => 'Opera', 'short_name' => 'OP'],
            ],[
                'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.115 Safari/537.36 OPR/27.0.1689.76',
                ['name' => 'Opera', 'short_name' => 'OP'],
            ],[
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12',
                ['name' => 'Microsoft Edge', 'short_name' => 'PS'],
            ],[
                'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25',
                ['name' => 'Mobile Safari', 'short_name' => 'MF'],
            ],[
                'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36 Vivaldi/1.2.490.43',
                ['name' => 'Vivaldi', 'short_name' => 'VI'],
            ],[
                'Mozilla/5.0 (Linux; U; Android 4.0.4; en-US; LT28h Build/6.1.E.3.7) AppleWebKit/534.31 (KHTML, like Gecko) UCBrowser/9.2.2.323 U3/0.8.0 Mobile Safari/534.31',
                ['name' => 'UC Browser', 'short_name' => 'UC'],
            ],
        ];
    }

    /**
     * Get the Browsers' versions.
     *
     * @return array
     */
    public function provideBrowserVersions(): array
    {
        return [
            [
                'Mozilla/5.0 (compatible; MSIE 10.6; Windows NT 6.1; Trident/5.0; InfoPath.2; SLCC1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 2.0.50727) 3gpp-gba UNTRUSTED/1.0',
                '9.0',
            ],
            [
                'Mozilla/5.0 (Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko',
                '11.0',
            ],
            [
                'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25',
                '6.0',
            ],
            [
                'Mozilla/5.0 (Windows; U; Win 9x 4.90; SG; rv:1.9.2.4) Gecko/20101104 Netscape/9.1.0285',
                '9.1',
            ],
            [
                'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0',
                '25.0',
            ],
            [
                'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36',
                '32.0',
            ],
            [
                'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201',
                'UNK',
            ],
            [
                'Opera/9.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14',
                '12.14',
            ],
            [
                'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; de) Opera 11.51',
                '11.51',
            ],
            [
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12',
                '12',
            ],
            [
                'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36 Vivaldi/1.2.490.43',
                '1.2',
            ],
        ];
    }

    /**
     * Provide user agents with desktop.
     *
     * @return array
     */
    public function provideDesktops(): array
    {
        return [
            ['Mozilla/5.0 (Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko'],
            ['Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:24.0) Gecko/20100101 Firefox/24.0'],
            ['Mozilla/5.0 (Windows; U; Win 9x 4.90; SG; rv:1.9.2.4) Gecko/20101104 Netscape/9.1.0285'],
            ['Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0'],
            ['Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36'],
            ['Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201'],
            ['Opera/9.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14'],
            ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2'],
        ];
    }

    /**
     * Provide user agents with phone.
     *
     * @return array
     */
    public function providePhones(): array {
        return [
            [
                'Mozilla/5.0 (iPhone; U; ru; CPU iPhone OS 4_2_1 like Mac OS X; ru) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148a Safari/6533.18.5',
            ],
            [
                'Mozilla/5.0 (Linux; U; Android 2.3.4; fr-fr; HTC Desire Build/GRJ22) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
            ],
            [
                'Mozilla/5.0 (BlackBerry; U; BlackBerry 9900; en) AppleWebKit/534.11+ (KHTML, like Gecko) Version/7.1.0.346 Mobile Safari/534.11+',
            ],
            [
                'Mozilla/5.0 (Linux; U; Android 2.2; en-us; Nexus One Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
            ],
        ];
    }

    /**
     * Get the Robots.
     *
     * @return array
     */
    public function provideRobotsData(): array
    {
        return [
            [
                'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
                'Googlebot',
            ],[
                'facebookexternalhit/1.1 (+http(s)://www.facebook.com/externalhit_uatext.php)',
                'Facebook External Hit',
            ],[
                'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)',
                'BingBot',
            ],[
                'Twitterbot/1.0',
                'Twitterbot',
            ],[
                'Mozilla/5.0 (compatible; YandexBot/3.0; +http://yandex.com/bots)',
                'Yandex Bot',
            ],
        ];
    }

    /**
     * Get the mobiles data.
     *
     * @return array
     */
    public function provideMobilesData(): array
    {
        return [
            [
                'Mozilla/5.0 (iPhone; U; ru; CPU iPhone OS 4_2_1 like Mac OS X; ru) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148a Safari/6533.18.5',
                ['brand' => 'Apple', 'model' => 'iPhone'],
            ],
            [
                'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25',
                ['brand' => 'Apple', 'model' => 'iPad'],
            ],
            [
                'Mozilla/5.0 (Linux; U; Android 2.3.4; fr-fr; HTC Desire Build/GRJ22) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
                ['brand' => 'HTC', 'model' => 'Desire'],
            ],
            [
                'Mozilla/5.0 (BlackBerry; U; BlackBerry 9900; en) AppleWebKit/534.11+ (KHTML, like Gecko) Version/7.1.0.346 Mobile Safari/534.11+',
                ['brand' => 'RIM', 'model' => 'BlackBerry 9900'],
            ],
            [
                'Mozilla/5.0 (Linux; U; Android 2.2; en-us; Nexus One Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
                ['brand' => 'Google', 'model' => 'Nexus One'],
            ],
            [
                'Mozilla/5.0 (Linux; U; Android 4.0.3; en-us; ASUS Transformer Pad TF300T Build/IML74K) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Safari/534.30',
                ['brand' => 'Asus', 'model' => 'Transformer Pad TF300T'],
            ],
        ];
    }

    /**
     * Get the device detector instance.
     *
     * @param string $userAgent
     *
     * @return \Arcanedev\Agent\Detectors\DeviceDetector
     */
    protected function parseDevice(string $userAgent)
    {
        $request = Request::create('test-request', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => $userAgent,
        ]);

        return $this->agent->parse($request)->device();
    }

    /**
     * Get the language detector instance.
     *
     * @param  string  $acceptLanguage
     *
     * @return \Arcanedev\Agent\Detectors\LanguageDetector
     */
    protected function parseLanguage(string $acceptLanguage)
    {
        $request = Request::create('test-request', 'GET', [], [], [], [
            'HTTP_ACCEPT_LANGUAGE' => $acceptLanguage,
        ]);

        return $this->agent->parse($request)->language();
    }
}
