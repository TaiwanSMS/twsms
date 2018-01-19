<?php

namespace Recca0120\TwSMS\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\TwSMS\TwSMSServiceProvider;

class TwSMSServiceProviderTest extends TestCase
{
    protected function setUp()
    {
        if (version_compare(PHP_VERSION, '5.6', '<') === true) {
            $this->markTestSkipped('PHP VERSION must bigger then 5.6');
        }
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testRegister()
    {
        $serviceProvider = new TwSMSServiceProvider(
            $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess')
        );

        $app->shouldReceive('singleton')->once()->with('Recca0120\TwSMS\Client', m::on(function ($closure) use ($app) {
            $app->shouldReceive('offsetGet')->once()->with('config')->andReturn(
                $config = [
                    'services.twsms' => [
                        'username' => 'foo',
                        'password' => 'bar',
                    ],
                ]
            );

            $client = $closure($app);
            $this->assertInstanceOf('Recca0120\TwSMS\Client', $client);
            $this->assertAttributeEquals('foo', 'username', $client);
            $this->assertAttributeEquals('bar', 'password', $client);

            return true;
        }));

        $serviceProvider->register();
    }
}
