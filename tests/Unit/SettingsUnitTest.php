<?php

namespace Scriptoshi\Settings\Tests\Unit;

use Illuminate\Support\Facades\Cache;
use Scriptoshi\Settings\Models\Setting;
use Scriptoshi\Settings\Settings;
use Scriptoshi\Settings\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SettingsUnitTest extends TestCase
{


    #[Test]
    public function it_caches_all_settings()
    {
        Cache::shouldReceive('rememberForever')->once()->andReturn(collect(['key' => 'value']));

        $result = Settings::all();
        $this->assertEquals('value', $result['key']);
    }
}
