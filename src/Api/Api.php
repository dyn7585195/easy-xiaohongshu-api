<?php

namespace EasyXiaohongshu\Api;

use EasyXiaohongshu\LittleRedBook;
use EasyXiaohongshu\Support\Signer;

class Api
{
    /**
     * 应用实例
     * @var LittleRedBook
     */
    protected $app;

    /**
     * 构造函数
     * @param LittleRedBook $app 应用实例
     */
    public function __construct(LittleRedBook $app)
    {
        $this->app = $app;
    }

    // ===== 用户相关接口 =====

    /**
     * 登录认证
     * @param string $code 临时登录凭证
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC414670 code2Session 接口文档
     */
    public function code2Session(string $code)
    {
        // 构建请求参数
        $params = [
            'appid' => $this->app->getConfig()->get('app_id'),
            'secret' => $this->app->getConfig()->get('app_secret'),
            'code' => $code,
        ];

        // 发送 GET 请求
        $response = $this->app->request('GET', 'api/apps/jscode2session', [
            'query' => $params,
        ]);

        return $response;
    }

    // ===== 商品相关接口 =====

    /**
     * 获取商品信息
     * @param string $outProductId 外部商品id
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC371309 [交易组件]商品-获取信息
     */
    public function getProductInfo(string $outProductId)
    {
        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
            'out_product_id' => $outProductId,
        ];

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/mp/deal/product/info', [
            'form_params' => $params,
        ]);

        return $response;
    }

    /**
     * 批量获取商品信息
     * @param array $outProductIds 外部商品id列表
     * @param int $page 页码
     * @param int $pageSize 每页数量
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC042908 [交易组件]商品-批量获取信息
     */
    public function batchGetProductInfo(array $outProductIds, $page = 1, $pageSize = 20)
    {
        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
            'out_product_ids' => $outProductIds,
            'page' => $page,
            'page_size' => $pageSize,
        ];

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/mp/deal/product/batch_get', [
            'form_params' => $params,
        ]);

        return $response;
    }

    /**
     * 商品同步-生活服务
     * @param array $data 商品数据
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC886309 [交易组件]商品同步-生活服务
     */
    public function syncProduct(array $data)
    {
        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
        ] + $data;

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/mp/deal/product/sync', [
            'form_params' => $params,
        ]);

        return $response;
    }

    /**
     * 专业号主页商品排序
     * @param array $outSpuIds 外部spuId列表（最多6个）
     * @param int $isApply 是否开启自定义排序 0:否 1:是
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC897797 [交易组件]专业号主页商品排序
     */
    public function setHomePageItemTab(array $outSpuIds, $isApply = 1)
    {
        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
            'is_apply' => $isApply,
        ];

        // 添加外部spuId
        for ($i = 0; $i < min(6, count($outSpuIds)); $i++) {
            $params["out_spu_id_" . ($i + 1)] = $outSpuIds[$i];
        }

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/display/home_page_item_tab', [
            'form_params' => $params,
        ]);

        return $response;
    }

    // ===== SKU相关接口 =====

    /**
     * SKU商品-批量删除
     * @param array $outSkuIds 外部sku商品id集合
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC141992 [交易组件]sku商品-批量删除
     */
    public function batchDeleteSku(array $outSkuIds)
    {
        // 验证参数
        if (empty($outSkuIds)) {
            throw new \Exception('out_sku_ids is required');
        }

        if (count($outSkuIds) > 100) {
            throw new \Exception('out_sku_ids count must be less than or equal to 100');
        }

        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
            'out_sku_ids' => $outSkuIds,
        ];

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/mp/deal/product/sku/batch_delete', [
            'form_params' => $params,
        ]);

        return $response;
    }

    /**
     * SKU商品-批量上架
     * @param array $outSkuIds 外部sku商品id集合
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC644902 [交易组件]sku商品-批量上架
     */
    public function batchOnlineSku(array $outSkuIds)
    {
        // 验证参数
        if (empty($outSkuIds)) {
            throw new \Exception('out_sku_ids is required');
        }

        if (count($outSkuIds) > 100) {
            throw new \Exception('out_sku_ids count must be less than or equal to 100');
        }

        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
            'out_sku_ids' => $outSkuIds,
        ];

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/mp/deal/product/sku/batch_online', [
            'form_params' => $params,
        ]);

        return $response;
    }

    /**
     * SKU商品-批量下架
     * @param array $outSkuIds 外部sku商品id集合
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC270541 [交易组件]sku商品-批量下架
     */
    public function batchOfflineSku(array $outSkuIds)
    {
        // 验证参数
        if (empty($outSkuIds)) {
            throw new \Exception('out_sku_ids is required');
        }

        if (count($outSkuIds) > 100) {
            throw new \Exception('out_sku_ids count must be less than or equal to 100');
        }

        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
            'out_sku_ids' => $outSkuIds,
        ];

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/mp/deal/product/sku/batch_offline', [
            'form_params' => $params,
        ]);

        return $response;
    }

    // ===== 订单相关接口 =====

    /**
     * 获取即时订单列表
     * @param int $orderTimeFrom 开始时间戳
     * @param int $orderTimeTo 结束时间戳（间隔≤30分钟）
     * @param int $pageNo 页码
     * @param int $pageSize 分页大小（默认50，最大100）
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/ 订单相关接口
     */
    public function getLatestPackages($orderTimeFrom, $orderTimeTo, $pageNo = 1, $pageSize = 50)
    {
        // 构建请求参数
        $params = [
            'order_time_from' => $orderTimeFrom,
            'order_time_to' => $orderTimeTo,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
            'timestamp' => time(),
        ];
        
        // 生成签名
        $appSecret = $this->app->getConfig()->get('app_secret');
        $params['sign'] = Signer::md5Sign($params, $appSecret);
        
        // 发送请求
        $response = $this->app->request('GET', 'ark/open_api/v0/packages/latest_packages', [
            'query' => $params,
        ]);
        
        return $response;
    }

    /**
     * 获取订单详情
     * @param string $orderId 订单 ID
     * @return array
     * @throws \Exception
     */
    public function getOrderDetail(string $orderId)
    {
        // 构建请求参数
        $params = [
            'order_id' => $orderId,
            'timestamp' => time(),
        ];
        
        // 生成签名
        $appSecret = $this->app->getConfig()->get('app_secret');
        $params['sign'] = Signer::md5Sign($params, $appSecret);
        
        // 发送请求
        $response = $this->app->request('GET', 'ark/open_api/v0/orders/detail', [
            'query' => $params,
        ]);
        
        return $response;
    }

    /**
     * 担保支付订单新增
     * @param array $data 订单数据
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC948963 [交易组件]担保支付订单新增
     */
    public function createOrder(array $data)
    {
        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
        ] + $data;

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/mp/deal/order/upsert', [
            'form_params' => $params,
        ]);

        return $response;
    }

    /**
     * 订单-状态同步
     * @param int $status 状态：2：已支付，6：已发货，7：已完成，71：已关闭，998：已取消
     * @param string $outOrderId 外部订单id
     * @param int $bizUpdateTime 订单状态更新时间，时间戳单位到秒
     * @param string $openId 用户 openId
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC126495 [交易组件]订单-状态同步
     */
    public function syncOrderStatus($status, string $outOrderId, $bizUpdateTime, string $openId)
    {
        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
            'status' => $status,
            'out_order_id' => $outOrderId,
            'biz_update_time' => $bizUpdateTime,
            'open_id' => $openId,
        ];

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/mp/deal/order/sync_status', [
            'form_params' => $params,
        ]);

        return $response;
    }

    /**
     * 获取订单支付token
     * @param string $outOrderId 外部订单id
     * @param string $openId 用户openId
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC946174 [交易组件]获取订单支付token
     */
    public function getPayToken(string $outOrderId, string $openId)
    {
        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
            'out_order_id' => $outOrderId,
            'open_id' => $openId,
        ];

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/mp/deal/query_pay_token', [
            'form_params' => $params,
        ]);

        return $response;
    }

    /**
     * 担保支付订单-获取订单信息
     * @param string $outOrderId 外部订单id
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC510914 [交易组件]担保支付订单-获取订单信息
     */
    public function getOrderInfo(string $outOrderId)
    {
        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
            'out_order_id' => $outOrderId,
        ];

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/mp/deal/order/info', [
            'form_params' => $params,
        ]);

        return $response;
    }

    // ===== 链接相关接口 =====

    /**
     * 获取小程序 URL Link
     * @param string $pageUrl 扫码进入的小程序页面路径，根路径前不要添加 /，可携带 query，最大 1024 个字符
     * @param int $expireInterval 失效间隔天数，最长间隔天数为 365 天，不填默认为 365 天
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC274658 获取URL Link
     */
    public function getUrlLink(string $pageUrl, $expireInterval = 365)
    {
        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
            'page_url' => $pageUrl,
        ];

        // 添加可选参数
        if ($expireInterval !== null) {
            $params['expire_interval'] = $expireInterval;
        }

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/url', [
            'form_params' => $params,
        ]);

        return $response;
    }

    /**
     * 获取小程序二维码
     * @param int $width 二维码宽度，单位 px，最小 280，最大 1280
     * @param string $pageUrl 扫码进入的小程序页面路径，根路径前不要添加 /，可携带 query，最大 1024 个字符
     * @param int $expireInterval 失效间隔天数，最长间隔天数为 365 天，不填默认为 365 天
     * @return string 二维码图像字节流
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC974389 获取小程序二维码
     */
    public function getQrcode($width, string $pageUrl, $expireInterval = 365)
    {
        // 验证宽度参数
        if ($width < 280 || $width > 1280) {
            throw new \Exception('Width must be between 280 and 1280');
        }

        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
            'width' => $width,
            'page_url' => $pageUrl,
        ];

        // 添加可选参数
        if ($expireInterval !== null) {
            $params['expire_interval'] = $expireInterval;
        }

        // 发送 POST 请求，获取原始响应
        $response = $this->app->getClient()->getGuzzleClient()->request('POST', 
            $this->app->getClient()->buildUrl('api/rmp/qrcode'), 
            [
                'form_params' => $params,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->app->getAccessToken()->getToken(),
                ],
            ]
        );

        // 检查响应状态
        $contentType = $response->getHeaderLine('Content-Type');
        if (strpos($contentType, 'application/json') !== false) {
            // 错误响应
            $body = (string) $response->getBody();
            $error = json_decode($body, true);
            if (isset($error['code']) && $error['code'] !== 0) {
                throw new \Exception(sprintf('API Error: %s (Code: %d)', $error['msg'] ?? 'Unknown error', $error['code']));
            }
        }

        // 返回图像字节流
        return (string) $response->getBody();
    }

    /**
     * 获取不限制的小程序二维码
     * @param string $scene 自定义参数，最大 32 个可见字符，只支持数字、大小写英文以及部分特殊字符：-._~
     * @param string $page 扫码进入的小程序页面路径，根路径前不要添加 /，不能携带参数，参数请放在 scene 字段里，不填写默认跳转主页
     * @param int $width 二维码宽度，单位 px，最小 280，最大 1280
     * @return string 二维码图像字节流
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC164497 获取不限制的小程序二维码
     */
    public function getUnlimitedQrcode(string $scene, $page = '', $width = 430)
    {
        // 验证宽度参数
        if ($width < 280 || $width > 1280) {
            throw new \Exception('Width must be between 280 and 1280');
        }

        // 验证 scene 参数
        if (empty($scene)) {
            throw new \Exception('Scene parameter is required');
        }

        if (strlen($scene) > 32) {
            throw new \Exception('Scene parameter must be at most 32 characters');
        }

        // 验证 scene 参数只支持特定字符
        if (!preg_match('/^[a-zA-Z0-9._~-]+$/', $scene)) {
            throw new \Exception('Scene parameter only supports numbers, uppercase/lowercase letters, and special characters: -._~');
        }

        // 构建请求参数
        $params = [
            'appid' => $this->app->getConfig()->get('app_id'),
            'scene' => $scene,
            'width' => $width,
        ];

        // 添加可选参数
        if (!empty($page)) {
            $params['page'] = $page;
        }

        // 发送 POST 请求，获取原始响应
        $response = $this->app->getClient()->getGuzzleClient()->request('POST', 
            $this->app->getClient()->buildUrl('api/rmp/qrcode/unlimited'), 
            [
                'form_params' => $params,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->app->getAccessToken()->getToken(),
                ],
            ]
        );

        // 检查响应状态
        $contentType = $response->getHeaderLine('Content-Type');
        if (strpos($contentType, 'application/json') !== false) {
            // 错误响应
            $body = (string) $response->getBody();
            $error = json_decode($body, true);
            if (isset($error['code']) && $error['code'] !== 0) {
                throw new \Exception(sprintf('API Error: %s (Code: %d)', $error['msg'] ?? 'Unknown error', $error['code']));
            }
        }

        // 返回图像字节流
        return (string) $response->getBody();
    }

    // ===== 类目相关接口 =====

    /**
     * 搜索可用类目
     * @param string $categoryName 类目名称
     * @param bool $forceCategoryV2 是否强制走新类目
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC381925 搜索可用类目
     */
    public function searchCategories(string $categoryName, $forceCategoryV2 = false)
    {
        // 验证必需参数
        if (empty($categoryName)) {
            throw new \Exception('Category name is required');
        }

        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
            'category_name' => $categoryName,
        ];

        // 添加可选参数
        if ($forceCategoryV2 !== null) {
            $params['force_category_v2'] = $forceCategoryV2 ? 1 : 0;
        }

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/mp/deal/category/search', [
            'form_params' => $params,
        ]);

        return $response;
    }

    /**
     * 获取小程序设置的工业类目
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC104157 获取小程序设置的工业类目
     */
    public function getAppCategories()
    {
        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
        ];

        // 发送 GET 请求
        $response = $this->app->request('GET', 'api/rmp/apps/category', [
            'query' => $params,
        ]);

        return $response;
    }

    // ===== 交易相关接口 =====

    /**
     * 凭证核销
     * @param array $data 核销数据
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC654368 [交易组件]凭证核销
     */
    public function verifyVoucher(array $data)
    {
        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
        ] + $data;

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/mp/deal/verify', [
            'form_params' => $params,
        ]);

        return $response;
    }

    /**
     * 预约单状态同步
     * @param array $data 预约单数据
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC842183 [交易组件]预约单状态同步
     */
    public function syncReservationStatus(array $data)
    {
        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
        ] + $data;

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/mp/deal/reservation/sync_status', [
            'form_params' => $params,
        ]);

        return $response;
    }

    /**
     * 售后单-新增
     * @param array $data 售后单数据
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC767572 [交易组件]售后单-新增
     */
    public function createAfterSale(array $data)
    {
        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
        ] + $data;

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/mp/deal/after_sale/create', [
            'form_params' => $params,
        ]);

        return $response;
    }

    /**
     * 同步售后单状态
     * @param array $data 售后单数据
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC321107 [交易组件]同步售后单状态
     */
    public function syncAfterSaleStatus(array $data)
    {
        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
        ] + $data;

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/mp/deal/after_sale/sync_status', [
            'form_params' => $params,
        ]);

        return $response;
    }

    /**
     * 获取售后订单详情
     * @param string $afterSaleId 售后单id
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC383213 [交易组件]获取售后订单详情
     */
    public function getAfterSaleDetail(string $afterSaleId)
    {
        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
            'after_sale_id' => $afterSaleId,
        ];

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/mp/deal/after_sale/info', [
            'form_params' => $params,
        ]);

        return $response;
    }

    /**
     * 查询结算
     * @param array $data 结算查询数据
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC649868 [交易组件]查询结算
     */
    public function querySettlement(array $data)
    {
        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
        ] + $data;

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/mp/deal/settlement/query', [
            'form_params' => $params,
        ]);

        return $response;
    }

    /**
     * 结算咨询
     * @param array $data 结算咨询数据
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC170236 [交易组件]结算咨询
     */
    public function consultSettlement(array $data)
    {
        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
        ] + $data;

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/mp/deal/settlement/consult', [
            'form_params' => $params,
        ]);

        return $response;
    }

    /**
     * 结算明细查询
     * @param array $data 结算明细查询数据
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC497536 [交易组件]结算明细查询
     */
    public function querySettlementDetail(array $data)
    {
        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
        ] + $data;

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/mp/deal/settlement/order_query', [
            'form_params' => $params,
        ]);

        return $response;
    }

    /**
     * 查询核销信息
     * @param array $data 核销信息查询数据
     * @return array
     * @throws \Exception
     * @see https://miniapp.xiaohongshu.com/doc/DC458746 本地担保交易能力（API列表）查询核销信息
     */
    public function queryVerifyInfo(array $data)
    {
        // 构建请求参数
        $params = [
            'app_id' => $this->app->getConfig()->get('app_id'),
        ] + $data;

        // 发送 POST 请求
        $response = $this->app->request('POST', 'api/rmp/mp/deal/verify/query', [
            'form_params' => $params,
        ]);

        return $response;
    }
}