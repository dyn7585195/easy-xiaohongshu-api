<?php

namespace EasyXiaohongshu\Auth;

use EasyXiaohongshu\LittleRedBook;

class AccessToken
{
    /**
     * 应用实例
     * @var LittleRedBook
     */
    protected $app;

    /**
     * Access Token 缓存
     * @var array
     */
    protected $token;

    /**
     * 构造函数
     * @param LittleRedBook $app 应用实例
     */
    public function __construct(LittleRedBook $app)
    {
        $this->app = $app;
        $this->token = [];
    }

    /**
     * 获取 Access Token
     * @return string
     * @throws \Exception
     */
    public function getToken()
    {
        // 检查是否有缓存且未过期
        if ($this->token && $this->token['expires_at'] > time()) {
            return $this->token['access_token'];
        }
        
        // 重新获取 Token
        $token = $this->refreshToken();
        $this->token = $token;
        return $token['access_token'];
    }

    /**
     * 刷新 Access Token
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC010382 获取应用调用凭证
     */
    public function refreshToken()
    {
        // 构建请求参数
        $params = [
            'appid' => $this->app->getConfig()->get('app_id'),
            'secret' => $this->app->getConfig()->get('app_secret'),
        ];
        
        // 发送请求
        $response = $this->app->request('POST', 'api/rmp/token', [
            'form_params' => $params,
        ]);
        
        // 处理响应
        if (!isset($response['data']['access_token'])) {
            throw new \Exception('Failed to get access token');
        }
        
        // 计算过期时间（access_token 有效期为 2 小时）
        $token = [
            'access_token' => $response['data']['access_token'],
            'expire_in' => $response['data']['expire_in'] ?? 7200,
            'expires_at' => time() + ($response['data']['expire_in'] ?? 7200),
        ];
        
        return $token;
    }

    /**
     * 手动设置 Token
     * @param array $token Token 信息
     * @return AccessToken
     */
    public function setToken(array $token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * 清除 Token 缓存
     * @return AccessToken
     */
    public function clearToken()
    {
        $this->token = [];
        return $this;
    }

    /**
     * 获取 Token 信息
     * @return array
     */
    public function getTokenInfo()
    {
        return $this->token;
    }

    /**
     * 检查 Token 是否过期
     * @return bool
     */
    public function isExpired()
    {
        return empty($this->token) || $this->token['expires_at'] <= time();
    }
}