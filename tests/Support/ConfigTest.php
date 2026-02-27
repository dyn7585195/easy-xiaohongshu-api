<?php

namespace EasyXiaohongshu\Tests\Support;

use EasyXiaohongshu\Support\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testConfig()
    {
        $config = new Config([
            'app_id' => 'test_app_id',
            'app_secret' => 'test_app_secret',
            'timeout' => 60,
        ]);
        
        // 测试获取配置
        $this->assertEquals('test_app_id', $config->get('app_id'));
        $this->assertEquals('test_app_secret', $config->get('app_secret'));
        $this->assertEquals(60, $config->get('timeout'));
        
        // 测试获取默认配置
        $this->assertEquals('https://api.xiaohongshu.com', $config->get('api_url'));
        $this->assertEquals('https://open-api.xiaohongshu.com', $config->get('open_api_url'));
        $this->assertFalse($config->get('debug'));
        
        // 测试获取不存在的配置
        $this->assertNull($config->get('non_existent'));
        $this->assertEquals('default_value', $config->get('non_existent', 'default_value'));
        
        // 测试设置配置
        $config->set('debug', true);
        $this->assertTrue($config->get('debug'));
        
        // 测试获取所有配置
        $allConfig = $config->all();
        $this->assertIsArray($allConfig);
        $this->assertArrayHasKey('app_id', $allConfig);
        $this->assertArrayHasKey('app_secret', $allConfig);
        $this->assertArrayHasKey('api_url', $allConfig);
    }
}