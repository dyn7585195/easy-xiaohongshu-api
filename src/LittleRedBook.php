<?php

namespace EasyXiaohongshu;

use EasyXiaohongshu\Auth\AccessToken;
use EasyXiaohongshu\Api\Api;
use EasyXiaohongshu\HttpClient\Client;
use EasyXiaohongshu\Support\Config;

class LittleRedBook
{
    /**
     * 配置对象
     * @var Config
     */
    protected $config;

    /**
     * HTTP 客户端
     * @var Client
     */
    protected $client;

    /**
     * Access Token 实例
     * @var AccessToken
     */
    protected $accessToken;

    /**
     * 构造函数
     * @param array $config 配置参数
     */
    public function __construct(array $config)
    {
        $this->config = new Config($config);
        $this->client = new Client($this);
        $this->accessToken = new AccessToken($this);
    }

    /**
     * 获取配置
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 获取 HTTP 客户端
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * 获取 Access Token 实例
     * @return AccessToken
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * 统一API接口（所有接口的集合）
     * @return Api
     */
    public function api()
    {
        return new Api($this);
    }

    /**
     * 发送请求
     * @param string $method HTTP 方法
     * @param string $endpoint 接口路径
     * @param array $options 请求选项
     * @return array
     */
    public function request($method, $endpoint, $options = [])
    {
        return $this->client->request($method, $endpoint, $options);
    }
}
