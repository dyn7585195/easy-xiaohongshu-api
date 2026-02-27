<?php

namespace EasyXiaohongshu\Support;

class Signer
{
    /**
     * 生成 MD5 签名
     * @param array $params 请求参数
     * @param string $appSecret 应用密钥
     * @return string
     */
    public static function md5Sign(array $params, $appSecret)
    {
        // 按 ASCII 升序排序
        ksort($params);
        
        // 拼接参数（key+value 形式）
        $signString = '';
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
            $signString .= $key . $value;
        }
        
        // 首尾添加 app_secret
        $signString = $appSecret . $signString . $appSecret;
        
        // 生成 MD5 签名
        return md5($signString);
    }

    /**
     * 验证签名
     * @param array $params 请求参数
     * @param string $appSecret 应用密钥
     * @param string $sign 待验证签名
     * @return bool
     */
    public static function verifySign(array $params, $appSecret, $sign)
    {
        // 移除 sign 参数
        if (isset($params['sign'])) {
            unset($params['sign']);
        }
        
        // 重新计算签名
        $calculatedSign = self::md5Sign($params, $appSecret);
        
        // 比较签名
        return $calculatedSign === $sign;
    }
}