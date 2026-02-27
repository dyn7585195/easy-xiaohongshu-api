<?php

namespace EasyXiaohongshu\Tests\Support;

use EasyXiaohongshu\Support\Signer;
use PHPUnit\Framework\TestCase;

class SignerTest extends TestCase
{
    public function testMd5Sign()
    {
        $params = [
            'app_id' => 'test_app_id',
            'code' => 'test_code',
            'timestamp' => 1634567890,
        ];
        $appSecret = 'test_app_secret';
        
        $sign = Signer::md5Sign($params, $appSecret);
        
        // 验证签名是否为 32 位 MD5 值
        $this->assertIsString($sign);
        $this->assertEquals(32, strlen($sign));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/i', $sign);
    }

    public function testVerifySign()
    {
        $params = [
            'app_id' => 'test_app_id',
            'code' => 'test_code',
            'timestamp' => 1634567890,
        ];
        $appSecret = 'test_app_secret';
        
        // 生成签名
        $sign = Signer::md5Sign($params, $appSecret);
        
        // 验证签名
        $params['sign'] = $sign;
        $result = Signer::verifySign($params, $appSecret, $sign);
        $this->assertTrue($result);
        
        // 验证错误签名
        $result = Signer::verifySign($params, $appSecret, 'wrong_sign');
        $this->assertFalse($result);
    }
}