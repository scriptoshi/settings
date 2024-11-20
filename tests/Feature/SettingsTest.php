<?php

namespace Scriptoshi\Settings\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Scriptoshi\Settings\Facades\Settings;
use Scriptoshi\Settings\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_store_and_retrieve_settings()
    {
        Settings::set('app.name', 'Scriptoshi');
        $this->assertEquals('Scriptoshi', Settings::get('app.name'));
    }

    #[Test]
    public function it_can_group_settings()
    {
        Settings::set('group1.setting1', 'value1');
        Settings::set('group1.setting2', 'value2');

        $group = Settings::for('group1');
        $this->assertArrayHasKey('setting1', $group);
        $this->assertArrayHasKey('setting2', $group);
    }

    #[Test]
    public function it_caches_settings()
    {
        Settings::set('cache.key', 'cached-value');
        $this->assertEquals('cached-value', Settings::get('cache.key'));

        // Simulate cache expiry
        Settings::refresh();
        $this->assertNull(Settings::get('cache.key'));
    }
}
