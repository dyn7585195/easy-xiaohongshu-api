<?php

namespace EasyXiaohongshu\Support;

class Config
{
    /**
     * 配置数组
     * @var array
     */
    protected $config;

    /**
     * 构造函数
     * @param array $config 配置参数
     */
    public function __construct(array $config)
    {
        $this->config = array_merge($this->getDefaultConfig(), $config);
    }

    /**
     * 获取默认配置
     * @return array
     */
    protected function getDefaultConfig()
    {
        return [
            'app_id' => '',
            'app_secret' => '',
            'api_url' => 'https://api.xiaohongshu.com',
            'open_api_url' => 'https://open-api.xiaohongshu.com',
            'miniapp_url' => 'https://miniapp.xiaohongshu.com',
            'timeout' => 30,
            'debug' => false,
        ];
    }

    /**
     * 获取配置值
     * @param string $key 配置键
     * @param mixed $default 默认值
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * 设置配置值
     * @param string $key 配置键
     * @param mixed $value 配置值
     * @return Config
     */
    public function set($key, $value)
    {
        $this->config[$key] = $value;
        return $this;
    }

    /**
     * 获取所有配置
     * @return array
     */
    public function all()
    {
        return $this->config;
    }
}