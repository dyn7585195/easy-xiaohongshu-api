<?php

/**
 * 小红书 SDK 基础使用示例
 */

require_once __DIR__ . '/../vendor/autoload.php';

use EasyXiaohongshu\LittleRedBook;

// 配置参数
$config = [
    'app_id' => 'your_app_id_here',
    'app_secret' => 'your_app_secret_here',
    'timeout' => 30,
    'debug' => false,
];

// 初始化 SDK
$app = new LittleRedBook($config);

// ============================================
// 示例 1: 登录认证 - 获取用户 openid 和 session_key
// ============================================
try {
    $code = 'temporary_login_code_from_frontend';
    $result = $app->api()->code2Session($code);
    
    echo "登录成功:\n";
    echo "OpenID: " . $result['data']['openid'] . "\n";
    echo "Session Key: " . $result['data']['session_key'] . "\n";
} catch (\Exception $e) {
    echo "登录失败: " . $e->getMessage() . "\n";
}

// ============================================
// 示例 2: 获取 Access Token
// ============================================
try {
    // 自动获取（会缓存并自动刷新）
    $accessToken = $app->getAccessToken()->getToken();
    echo "Access Token: " . $accessToken . "\n";
} catch (\Exception $e) {
    echo "获取 Token 失败: " . $e->getMessage() . "\n";
}

// ============================================
// 示例 3: 商品管理 - 获取商品信息
// ============================================
try {
    $outProductId = 'your_product_id';
    $result = $app->api()->getProductInfo($outProductId);
    
    echo "商品信息:\n";
    print_r($result);
} catch (\Exception $e) {
    echo "获取商品信息失败: " . $e->getMessage() . "\n";
}

// ============================================
// 示例 4: 商品管理 - 同步商品
// ============================================
try {
    $productData = [
        'out_product_id' => 'product_123',
        'name' => '测试商品',
        'short_title' => '测试',
        'desc' => '这是一个测试商品',
        'path' => '/pages/product/detail?id=123',
        'top_image' => 'https://example.com/image.jpg',
        'category_id' => 'category_id_here',
        'biz_create_time' => time(),
        'biz_update_time' => time(),
        'skus' => [
            [
                'out_sku_id' => 'sku_123',
                'name' => '默认规格',
                'sku_image' => 'https://example.com/sku.jpg',
                'sale_price' => 1000, // 单位：分
                'status' => 1, // 1:上架 0:下架
            ]
        ]
    ];
    
    $result = $app->api()->syncProduct($productData);
    echo "商品同步成功:\n";
    print_r($result);
} catch (\Exception $e) {
    echo "商品同步失败: " . $e->getMessage() . "\n";
}

// ============================================
// 示例 5: SKU 管理 - 批量上架
// ============================================
try {
    $outSkuIds = ['sku_123', 'sku_124'];
    $result = $app->api()->batchOnlineSku($outSkuIds);
    
    echo "SKU 上架成功:\n";
    print_r($result);
} catch (\Exception $e) {
    echo "SKU 上架失败: " . $e->getMessage() . "\n";
}

// ============================================
// 示例 6: 订单管理 - 创建订单
// ============================================
try {
    $orderData = [
        'out_order_id' => 'order_123',
        'open_id' => 'user_open_id',
        'path' => '/pages/order/detail',
        'biz_create_time' => time(),
        'product_infos' => [
            [
                'out_product_id' => 'product_123',
                'out_sku_id' => 'sku_123',
                'num' => 1,
                'sale_price' => 1000,
                'real_price' => 1000,
            ]
        ],
        'price_info' => [
            'order_price' => 1000,
        ]
    ];
    
    $result = $app->api()->createOrder($orderData);
    echo "订单创建成功:\n";
    print_r($result);
} catch (\Exception $e) {
    echo "订单创建失败: " . $e->getMessage() . "\n";
}

// ============================================
// 示例 7: 订单管理 - 获取支付 Token
// ============================================
try {
    $outOrderId = 'order_123';
    $openId = 'user_open_id';
    $result = $app->api()->getPayToken($outOrderId, $openId);
    
    echo "支付 Token:\n";
    print_r($result);
} catch (\Exception $e) {
    echo "获取支付 Token 失败: " . $e->getMessage() . "\n";
}

// ============================================
// 示例 8: 链接管理 - 生成 URL Link
// ============================================
try {
    $pageUrl = 'pages/index/index?id=123';
    $expireInterval = 30; // 30天后过期
    $result = $app->api()->getUrlLink($pageUrl, $expireInterval);
    
    echo "URL Link: " . $result['data']['url'] . "\n";
} catch (\Exception $e) {
    echo "生成 URL Link 失败: " . $e->getMessage() . "\n";
}

// ============================================
// 示例 9: 链接管理 - 生成小程序二维码
// ============================================
try {
    $width = 430;
    $pageUrl = 'pages/index/index?id=123';
    $qrcodeImage = $app->api()->getQrcode($width, $pageUrl);
    
    // 保存二维码图片
    file_put_contents('qrcode.png', $qrcodeImage);
    echo "二维码已保存到 qrcode.png\n";
} catch (\Exception $e) {
    echo "生成二维码失败: " . $e->getMessage() . "\n";
}

// ============================================
// 示例 10: 类目管理 - 搜索类目
// ============================================
try {
    $categoryName = '服装';
    $result = $app->api()->searchCategories($categoryName);
    
    echo "类目搜索结果:\n";
    print_r($result);
} catch (\Exception $e) {
    echo "搜索类目失败: " . $e->getMessage() . "\n";
}

// ============================================
// 示例 11: 交易管理 - 凭证核销
// ============================================
try {
    $verifyData = [
        'verify_code' => 'verification_code_here',
        'out_order_id' => 'order_123',
        // 其他核销数据
    ];
    $result = $app->api()->verifyVoucher($verifyData);
    
    echo "核销成功:\n";
    print_r($result);
} catch (\Exception $e) {
    echo "核销失败: " . $e->getMessage() . "\n";
}

echo "\n所有示例执行完成!\n";
