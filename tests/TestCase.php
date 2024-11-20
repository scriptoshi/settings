<?php

namespace Scriptoshi\Settings\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Scriptoshi\Settings\SettingsServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            SettingsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Configure the testing environment
        $app['config']->set('settings.cache_duration', 10);
        $app['config']->set('settings.default_group', 'test-group');
    }
}
