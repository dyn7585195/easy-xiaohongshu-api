# Easy Xiaohongshu API

小红书小程序服务端 API 的 PHP SDK，支持登录认证、商品管理、订单管理、交易处理等功能。

## 安装

使用 Composer 安装：

```bash
composer require easy-xiaohongshu-api/easy-xiaohongshu-api
```

## 配置

```php
use EasyXiaohongshu\LittleRedBook;

$config = [
    'app_id' => 'your_app_id',
    'app_secret' => 'your_app_secret',
    'api_url' => 'https://api.xiaohongshu.com', // 可选，默认值
    'open_api_url' => 'https://open-api.xiaohongshu.com', // 可选，默认值
    'miniapp_url' => 'https://miniapp.xiaohongshu.com', // 可选，默认值
    'timeout' => 30, // 可选，默认值
    'debug' => false, // 可选，默认值
];

$app = new LittleRedBook($config);
```

## 接口调用方式

所有接口都通过统一的 `api()` 方法调用：

```php
$api = $app->api();

// 调用任意接口
$result = $api->code2Session('your_code');
```

---

## 接口列表

### 一、登录认证接口

#### 1. code2Session - 登录凭证校验

**接口说明**：获取用户 openid 和 session_key。

**调用方式**：
```php
$result = $app->api()->code2Session('your_code');
```

**参数说明**：
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| code | string | 是 | 临时登录凭证，有效期5分钟 |

**响应示例**：
```json
{
    "data": {
        "openid": "user_open_id",
        "session_key": "session_key"
    },
    "success": true,
    "msg": "success",
    "code": 0
}
```

**官方文档**：[code2Session 接口文档](https://miniapp.xiaohongshu.com/doc/DC414670)

---

### 二、Access Token 接口

#### 2. 获取 Access Token

**接口说明**：获取小程序调用凭证。

**调用方式**：
```php
// 自动获取（会缓存并自动刷新）
$accessToken = $app->getAccessToken()->getToken();

// 手动刷新
$tokenInfo = $app->getAccessToken()->refreshToken();
```

**响应示例**：
```json
{
    "data": {
        "access_token": "your_access_token",
        "expire_in": 7200
    },
    "success": true,
    "msg": "success",
    "code": 0
}
```

**注意事项**：
- access_token 有效期为 2 小时
- SDK 会自动管理缓存和刷新

**官方文档**：[获取应用调用凭证](https://miniapp.xiaohongshu.com/doc/DC010382)

---

### 三、商品管理接口

#### 3. 获取商品信息

**接口说明**：根据外部商品ID获取商品详细信息。

**调用方式**：
```php
$result = $app->api()->getProductInfo('your_out_product_id');
```

**参数说明**：
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| outProductId | string | 是 | 外部商品ID |

**官方文档**：[商品-获取信息](https://miniapp.xiaohongshu.com/doc/DC371309)

---

#### 4. 批量获取商品信息

**接口说明**：批量查询商品信息。

**调用方式**：
```php
$result = $app->api()->batchGetProductInfo(
    ['product_id_1', 'product_id_2'], // 外部商品ID列表
    1,  // 页码
    20  // 每页数量
);
```

**参数说明**：
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| outProductIds | array | 是 | 外部商品ID列表 |
| page | int | 否 | 页码，默认1 |
| pageSize | int | 否 | 每页数量，默认20 |

**官方文档**：[商品-批量获取信息](https://miniapp.xiaohongshu.com/doc/DC042908)

---

#### 5. 商品同步-生活服务

**接口说明**：同步商品信息到小红书。

**调用方式**：
```php
$result = $app->api()->syncProduct([
    'out_product_id' => 'your_product_id',
    'name' => '商品名称',
    'short_title' => '短标题',
    'desc' => '商品描述',
    'path' => '/pages/product/detail',
    'top_image' => 'https://example.com/image.jpg',
    'category_id' => 'category_id',
    'biz_create_time' => time(),
    'biz_update_time' => time(),
    'skus' => [
        [
            'out_sku_id' => 'sku_id',
            'name' => 'SKU名称',
            'sku_image' => 'https://example.com/sku.jpg',
            'sale_price' => 1000, // 单位：分
            'status' => 1, // 1:上架 0:下架
        ]
    ]
]);
```

**官方文档**：[商品同步-生活服务](https://miniapp.xiaohongshu.com/doc/DC886309)

---

#### 6. 专业号主页商品排序

**接口说明**：设置专业号主页商品展示顺序。

**调用方式**：
```php
$result = $app->api()->setHomePageItemTab(
    ['spu_id_1', 'spu_id_2', 'spu_id_3'], // 外部spuId列表，最多6个
    1 // 是否开启自定义排序：0-否 1-是
);
```

**参数说明**：
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| outSpuIds | array | 是 | 外部spuId列表，最多6个 |
| isApply | int | 是 | 是否开启自定义排序：0-否 1-是 |

**官方文档**：[专业号主页商品排序](https://miniapp.xiaohongshu.com/doc/DC897797)

---

### 四、SKU 管理接口

#### 7. SKU商品-批量删除

**接口说明**：批量删除SKU商品。

**调用方式**：
```php
$result = $app->api()->batchDeleteSku(['sku_id_1', 'sku_id_2']);
```

**参数说明**：
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| outSkuIds | array | 是 | 外部sku商品ID列表，最多100个 |

**官方文档**：[sku商品-批量删除](https://miniapp.xiaohongshu.com/doc/DC141992)

---

#### 8. SKU商品-批量上架

**接口说明**：批量上架SKU商品。

**调用方式**：
```php
$result = $app->api()->batchOnlineSku(['sku_id_1', 'sku_id_2']);
```

**参数说明**：
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| outSkuIds | array | 是 | 外部sku商品ID列表，最多100个 |

**官方文档**：[sku商品-批量上架](https://miniapp.xiaohongshu.com/doc/DC644902)

---

#### 9. SKU商品-批量下架

**接口说明**：批量下架SKU商品。

**调用方式**：
```php
$result = $app->api()->batchOfflineSku(['sku_id_1', 'sku_id_2']);
```

**参数说明**：
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| outSkuIds | array | 是 | 外部sku商品ID列表，最多100个 |

**官方文档**：[sku商品-批量下架](https://miniapp.xiaohongshu.com/doc/DC270541)

---

### 五、订单管理接口

#### 10. 担保支付订单新增

**接口说明**：创建担保支付订单。

**调用方式**：
```php
$result = $app->api()->createOrder([
    'out_order_id' => 'your_order_id',
    'open_id' => 'user_open_id',
    'path' => '/pages/order/detail',
    'biz_create_time' => time(),
    'product_infos' => [
        [
            'out_product_id' => 'product_id',
            'out_sku_id' => 'sku_id',
            'num' => 1,
            'sale_price' => 1000,
            'real_price' => 1000,
        ]
    ],
    'price_info' => [
        'order_price' => 1000,
    ]
]);
```

**官方文档**：[担保支付订单新增](https://miniapp.xiaohongshu.com/doc/DC948963)

---

#### 11. 订单-状态同步

**接口说明**：同步订单状态到小红书。

**调用方式**：
```php
$result = $app->api()->syncOrderStatus(
    2,                      // 状态：2-已支付 6-已发货 7-已完成 71-已关闭 998-已取消
    'your_out_order_id',    // 外部订单ID
    time(),                 // 订单状态更新时间
    'user_open_id'          // 用户openId
);
```

**参数说明**：
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| status | int | 是 | 订单状态：2-已支付 6-已发货 7-已完成 71-已关闭 998-已取消 |
| outOrderId | string | 是 | 外部订单ID |
| bizUpdateTime | int | 是 | 订单状态更新时间戳 |
| openId | string | 是 | 用户openId |

**官方文档**：[订单-状态同步](https://miniapp.xiaohongshu.com/doc/DC126495)

---

#### 12. 获取订单支付token

**接口说明**：获取订单支付所需的token。

**调用方式**：
```php
$result = $app->api()->getPayToken('your_out_order_id', 'user_open_id');
```

**参数说明**：
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| outOrderId | string | 是 | 外部订单ID |
| openId | string | 是 | 用户openId |

**官方文档**：[获取订单支付token](https://miniapp.xiaohongshu.com/doc/DC946174)

---

#### 13. 担保支付订单-获取订单信息

**接口说明**：获取担保支付订单的详细信息。

**调用方式**：
```php
$result = $app->api()->getOrderInfo('your_out_order_id');
```

**参数说明**：
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| outOrderId | string | 是 | 外部订单ID |

**官方文档**：[担保支付订单-获取订单信息](https://miniapp.xiaohongshu.com/doc/DC510914)

---

### 六、链接管理接口

#### 14. 获取小程序 URL Link

**接口说明**：生成可在短信、邮件、网页等场景打开小程序的链接。

**调用方式**：
```php
$result = $app->api()->getUrlLink(
    'pages/index/index?id=123', // 页面路径
    30                          // 失效间隔天数，默认365天
);
```

**参数说明**：
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| pageUrl | string | 是 | 页面路径，根路径前不要添加/，可携带query |
| expireInterval | int | 否 | 失效间隔天数，最长365天，默认365 |

**响应示例**：
```json
{
    "data": {
        "url": "https://xiaohongshu.com/..."
    },
    "success": true,
    "msg": "success",
    "code": 0
}
```

**官方文档**：[获取URL Link](https://miniapp.xiaohongshu.com/doc/DC274658)

---

#### 15. 获取小程序二维码

**接口说明**：生成小程序二维码图片（有数量限制）。

**调用方式**：
```php
$qrcodeImage = $app->api()->getQrcode(
    430,                        // 二维码宽度，280-1280
    'pages/index/index?id=123', // 页面路径
    30                          // 失效间隔天数
);

// 保存二维码
file_put_contents('qrcode.png', $qrcodeImage);
```

**参数说明**：
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| width | int | 是 | 二维码宽度，280-1280像素 |
| pageUrl | string | 是 | 页面路径 |
| expireInterval | int | 否 | 失效间隔天数，默认365 |

**注意事项**：
- 此接口每天只能生成1000个二维码
- 返回的是图片字节流，不是JSON

**官方文档**：[获取小程序二维码](https://miniapp.xiaohongshu.com/doc/DC974389)

---

#### 16. 获取不限制的小程序二维码

**接口说明**：生成小程序二维码（无数量限制，有效期10年）。

**调用方式**：
```php
$qrcodeImage = $app->api()->getUnlimitedQrcode(
    '12345',                    // scene参数
    'pages/index/index',        // 页面路径（可选）
    430                         // 二维码宽度
);

// 保存二维码
file_put_contents('qrcode_unlimited.png', $qrcodeImage);
```

**参数说明**：
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| scene | string | 是 | 自定义参数，最大32字符，支持数字、字母、-._~ |
| page | string | 否 | 页面路径，参数请放在scene中 |
| width | int | 否 | 二维码宽度，280-1280，默认430 |

**注意事项**：
- scene参数不支持%，中文请使用其他编码方式
- 返回的是图片字节流

**官方文档**：[获取不限制的小程序二维码](https://miniapp.xiaohongshu.com/doc/DC164497)

---

### 七、类目管理接口

#### 17. 搜索可用类目

**接口说明**：搜索可用的商品类目。

**调用方式**：
```php
$result = $app->api()->searchCategories('服装', true);
```

**参数说明**：
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| categoryName | string | 是 | 类目名称 |
| forceCategoryV2 | bool | 否 | 是否强制走新类目，默认false |

**官方文档**：[搜索可用类目](https://miniapp.xiaohongshu.com/doc/DC381925)

---

#### 18. 获取小程序设置的工业类目

**接口说明**：获取当前小程序已设置的类目列表。

**调用方式**：
```php
$result = $app->api()->getAppCategories();
```

**响应示例**：
```json
{
    "data": {
        "category_info": [
            {
                "category_id": "category_id",
                "name": "类目名称",
                "require_claim_store": false,
                "support_trade": true,
                "trade_ability": "GENERAL_GUARANTEED_PAY",
                "path": ["一级类目", "二级类目", "三级类目"]
            }
        ]
    },
    "success": true,
    "msg": "success",
    "code": 0
}
```

**官方文档**：[获取小程序设置的工业类目](https://miniapp.xiaohongshu.com/doc/DC104157)

---

### 八、交易管理接口

#### 19. 凭证核销

**接口说明**：核销用户购买的凭证。

**调用方式**：
```php
$result = $app->api()->verifyVoucher([
    'verify_code' => '核销码',
    // 其他核销数据
]);
```

**官方文档**：[凭证核销](https://miniapp.xiaohongshu.com/doc/DC654368)

---

#### 20. 预约单状态同步

**接口说明**：同步预约单状态。

**调用方式**：
```php
$result = $app->api()->syncReservationStatus([
    'out_order_id' => 'order_id',
    'status' => 1,
    // 其他数据
]);
```

**官方文档**：[预约单状态同步](https://miniapp.xiaohongshu.com/doc/DC842183)

---

#### 21. 售后单-新增

**接口说明**：创建售后单。

**调用方式**：
```php
$result = $app->api()->createAfterSale([
    'out_order_id' => 'order_id',
    'out_after_sale_id' => 'after_sale_id',
    // 其他数据
]);
```

**官方文档**：[售后单-新增](https://miniapp.xiaohongshu.com/doc/DC767572)

---

#### 22. 同步售后单状态

**接口说明**：同步售后单状态。

**调用方式**：
```php
$result = $app->api()->syncAfterSaleStatus([
    'out_after_sale_id' => 'after_sale_id',
    'status' => 1,
    // 其他数据
]);
```

**官方文档**：[同步售后单状态](https://miniapp.xiaohongshu.com/doc/DC321107)

---

#### 23. 获取售后订单详情

**接口说明**：获取售后订单的详细信息。

**调用方式**：
```php
$result = $app->api()->getAfterSaleDetail('after_sale_id');
```

**参数说明**：
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| afterSaleId | string | 是 | 售后单ID |

**官方文档**：[获取售后订单详情](https://miniapp.xiaohongshu.com/doc/DC383213)

---

#### 24. 查询结算

**接口说明**：查询结算信息。

**调用方式**：
```php
$result = $app->api()->querySettlement([
    'start_time' => time() - 86400,
    'end_time' => time(),
    // 其他查询条件
]);
```

**官方文档**：[查询结算](https://miniapp.xiaohongshu.com/doc/DC649868)

---

#### 25. 结算咨询

**接口说明**：结算咨询接口。

**调用方式**：
```php
$result = $app->api()->consultSettlement([
    // 咨询数据
]);
```

**官方文档**：[结算咨询](https://miniapp.xiaohongshu.com/doc/DC170236)

---

#### 26. 结算明细查询

**接口说明**：查询结算明细。

**调用方式**：
```php
$result = $app->api()->querySettlementDetail([
    'start_time' => time() - 86400,
    'end_time' => time(),
    // 其他查询条件
]);
```

**官方文档**：[结算明细查询](https://miniapp.xiaohongshu.com/doc/DC497536)

---

#### 27. 查询核销信息

**接口说明**：查询核销信息。

**调用方式**：
```php
$result = $app->api()->queryVerifyInfo([
    'out_order_id' => 'order_id',
    // 其他查询条件
]);
```

**官方文档**：[查询核销信息](https://miniapp.xiaohongshu.com/doc/DC458746)

---

## 运行测试

```bash
composer test
```

## 代码风格检查

```bash
composer cs-fix
```

## 注意事项

1. **签名机制**：部分接口需要 MD5 签名，SDK 会自动处理签名生成
2. **Access Token**：SDK 会自动管理 Access Token 的获取和刷新
3. **错误处理**：所有接口调用都会抛出异常，需要自行捕获处理
4. **请求频率**：请遵守小红书开放平台的接口调用频率限制
5. **版本兼容**：如果文档提到 API 版本，请在代码中支持版本配置

## 许可证

MIT License
