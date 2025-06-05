<?php

namespace BnitoBzh\Notifications\Tests\Unit;

use BnitoBzh\Notifications\Channels\SmsmodeChannel;
use BnitoBzh\Notifications\SmsmodeChannelServiceProvider;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Notifications\Notification;
use Illuminate\Support\ServiceProvider;
use Mockery;
use Orchestra\Testbench\TestCase;
use ReflectionClass;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SmsmodeChannelServiceProviderTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function getPackageProviders($app): array
    {
        return [SmsmodeChannelServiceProvider::class];
    }

    public function testRegisterBindsSmsmodeChannel()
    {
        $this->app->instance('config', $this->app['config']);
        
        // Définir la configuration
        config(['smsmode.api_key' => 'test_api_key']);
        config(['smsmode.sender' => 'TEST_SENDER']);
        
        // Vérifier que le service est correctement lié
        $channel = $this->app->make(SmsmodeChannel::class);
        
        $this->assertInstanceOf(SmsmodeChannel::class, $channel);
    }
}