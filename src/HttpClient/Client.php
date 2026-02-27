<?php

namespace EasyXiaohongshu\HttpClient;

use EasyXiaohongshu\LittleRedBook;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;

class Client
{
    /**
     * 应用实例
     * @var LittleRedBook
     */
    protected $app;

    /**
     * Guzzle 客户端
     * @var GuzzleClient
     */
    protected $guzzle;

    /**
     * 构造函数
     * @param LittleRedBook $app 应用实例
     */
    public function __construct(LittleRedBook $app)
    {
        $this->app = $app;
        $this->guzzle = new GuzzleClient([
            'timeout' => $app->getConfig()->get('timeout'),
            'verify' => true,
        ]);
    }

    /**
     * 获取 Guzzle 客户端实例
     * @return GuzzleClient
     */
    public function getGuzzleClient()
    {
        return $this->guzzle;
    }

    /**
     * 发送请求
     * @param string $method HTTP 方法
     * @param string $endpoint 接口路径
     * @param array $options 请求选项
     * @return array
     * @throws \Exception
     */
    public function request($method, $endpoint, $options = [])
    {
        // 构建完整 URL
        $url = $this->buildUrl($endpoint);
        
        // 添加 Access Token
        if ($this->needsAccessToken($endpoint)) {
            $options = $this->addAccessToken($endpoint, $options);
        }
        
        try {
            $response = $this->guzzle->request($method, $url, $options);
            $body = (string) $response->getBody();
            $result = json_decode($body, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response: ' . $body);
            }
            
            // 检查响应状态
            $this->checkResponse($result);
            
            return $result;
        } catch (ClientException $e) {
            $response = $e->getResponse();
            if ($response) {
                $body = (string) $response->getBody();
                $error = json_decode($body, true);
                if (isset($error['error_code'])) {
                    throw new \Exception(sprintf('API Error: %s (Code: %d)', $error['error_msg'] ?? 'Unknown error', $error['error_code']));
                }
            }
            throw new \Exception('Request failed: ' . $e->getMessage());
        }
    }

    /**
     * 构建完整 URL
     * @param string $endpoint 接口路径
     * @return string
     */
    public function buildUrl($endpoint)
    {
        $baseUrl = $this->app->getConfig()->get('api_url');
        if (strpos($endpoint, 'ark/open_api') === 0) {
            $baseUrl = $this->app->getConfig()->get('open_api_url');
        } elseif (strpos($endpoint, 'api/rmp') === 0) {
            $baseUrl = $this->app->getConfig()->get('miniapp_url');
        }
        return rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');
    }

    /**
     * 判断是否需要 Access Token
     * @param string $endpoint 接口路径
     * @return bool
     */
    protected function needsAccessToken($endpoint)
    {
        // 获取 access_token 接口不需要 access_token
        if (strpos($endpoint, 'api/rmp/token') !== false) {
            return false;
        }
        // 登录接口需要 Access Token（作为 query 参数）
        if (strpos($endpoint, 'api/rmp/session') !== false) {
            return true;
        }
        return true;
    }

    /**
     * 添加 Access Token 到请求选项
     * @param string $endpoint 接口路径
     * @param array $options 请求选项
     * @return array
     * @throws \Exception
     */
    protected function addAccessToken($endpoint, $options)
    {
        $accessToken = $this->app->getAccessToken()->getToken();
        if (empty($accessToken)) {
            throw new \Exception('Access token is missing');
        }
        
        // 小程序 API 使用 query 参数传递 access_token
        if (strpos($endpoint, 'api/rmp') === 0) {
            $options['query']['access_token'] = $accessToken;
        } else {
            // 其他 API 使用 Authorization header
            $options['headers']['Authorization'] = 'Bearer ' . $accessToken;
        }
        
        return $options;
    }

    /**
     * 检查响应状态
     * @param array $result 响应结果
     * @throws \Exception
     */
    protected function checkResponse($result)
    {
        if (isset($result['error_code']) && $result['error_code'] !== 0) {
            throw new \Exception(sprintf('API Error: %s (Code: %d)', $result['error_msg'] ?? 'Unknown error', $result['error_code']));
        }
        if (isset($result['code']) && $result['code'] !== 0) {
            throw new \Exception(sprintf('API Error: %s (Code: %d)', $result['msg'] ?? 'Unknown error', $result['code']));
        }
        if (isset($result['success']) && $result['success'] !== true) {
            throw new \Exception('API Error: Operation failed');
        }
    }
}