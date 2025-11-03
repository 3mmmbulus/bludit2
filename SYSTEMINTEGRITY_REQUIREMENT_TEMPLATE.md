# SystemIntegrity 架构规范与需求模板

---

## 📋 0. 任务元信息（占位符 - 必填）

| 字段 | 值 | 说明 |
|-----|---|------|
| **需求代号** | `[[FEATURE_ID]]` | 示例：FEAT-2025-001 |
| **目标路由** | `[[ROUTE]]` | 示例：/admin/authorization-settings |
| **导航唯一名** | `[[NAV_KEY]]` | 示例：authorization-settings（用于 i18n/CSS/JS/菜单） |
| **权限等级** | `[[PERMISSION]]` | admin / editor / superadmin |
| **涉及写操作** | `[[YES/NO]]` | YES 时所有写入口首行要求 `isAuthorized()` |
| **作用范围** | `[[SCOPE]]` | PER-SITE（单站点）/ GLOBAL（全局共享） |
| **数据库操作** | `[[DB_OPS]]` | READ / WRITE / BOTH / NONE |

---

## 📂 1. 目录与命名规范（必须遵守）

### 1.1 i18n 翻译文件

| 语言 | 路径 | 必需键 |
|-----|------|--------|
| 英文 | `bl-languages/pages/[[NAV_KEY]]/en.json` | `nav`, `title` |
| 中文 | `bl-languages/pages/[[NAV_KEY]]/zh_CN.json` | `nav`, `title` |

**建议补充键**（如有表单/操作）：
```json
{
  "nav": "[[菜单名]]",
  "title": "[[页面标题]]",
  "btn_save": "保存",
  "btn_cancel": "取消",
  "btn_delete": "删除",
  "msg_saved": "保存成功",
  "msg_deleted": "删除成功",
  "msg_error": "操作失败",
  "msg_forbidden": "权限不足"
}
```

### 1.2 静态资源

| 类型 | 路径 | 用途 |
|-----|------|------|
| CSS | `bl-kernel/css/[[NAV_KEY]].css` | 页面专用样式 |
| JS | `bl-kernel/js/[[NAV_KEY]].js` | 页面专用脚本 |

**例外情况**：
- 工具页面（如 `system-init`、`site-bootstrap`）可不需要 CSS/JS
- 纯展示页面可复用全局样式

### 1.3 控制器与视图

| 类型 | 路径 | 说明 |
|-----|------|------|
| 控制器 | `bl-kernel/admin/controllers/[[NAV_KEY]].php` | 业务逻辑处理，不写 HTML |
| 视图 | `bl-kernel/admin/views/[[NAV_KEY]].php` | HTML 模板，不写业务逻辑 |

---

## 🔒 2. SystemIntegrity 架构硬约束（强制遵守）

> **⚠️ 违反任一项 = 架构缺陷，PR 直接退回**

---

### 2.1 关键类构造函数接线

**规则**：所有关键类构造函数**首行**必须调用：
```php
SystemIntegrity::quickCheck();
```

**适用范围**：
- ✅ 继承自 `dbJSON`、`dbList`、`Plugin` 的类
- ✅ `bl-kernel/*.class.php` 核心类
- ✅ `bl-kernel/helpers/*.class.php` 辅助类
- ❌ **例外**：`SystemIntegrity` 类本身

**验证方法**：
```bash
# 检查是否有遗漏
grep -rn "function __construct" bl-kernel/ | \
  grep -v "SystemIntegrity::quickCheck" | \
  grep -v "class SystemIntegrity"
```

**示例**：
```php
class dbJSON {
    function __construct($file, $firstLine=true)
    {
        SystemIntegrity::quickCheck(); // ← 必须在首行
        $this->file = $file;
        // ...
    }
}
```

---

### 2.2 敏感方法接线

**规则**：涉及写入/授权敏感的方法**首行**必须调用：
```php
SystemIntegrity::isAuthorized();
```

**适用场景分类**：

#### A. 配置写入
- `Site::set()` - 站点配置
- `Users::set()` - 用户配置
- `Security::set()` - 安全配置
- 其他配置类的 `set()` 方法

#### B. DB/存储写入
- `dbJSON::save()` - JSON 数据库保存
- `dbJSON::truncate()` - 清空数据库
- `Pages::add/edit/delete()` - 页面增删改
- `Users::add/delete()` - 用户增删
- `dbList::add/remove/edit()` - 列表操作
- `Syslog::add()` - 日志记录（可选）
- `Plugin::save()` - 插件配置保存

#### C. 文件系统操作
**上传类**：
- `bl-kernel/ajax/upload-images.php`
- `bl-kernel/ajax/logo-upload.php`
- `bl-kernel/ajax/profile-picture-upload.php`

**删除类**：
- `bl-kernel/ajax/delete-image.php`
- `bl-kernel/ajax/logo-remove.php`
- `Filesystem::rmfile()`
- `Filesystem::rmdir()`

**创建类**：
- `Filesystem::mkdir()`

#### D. 授权文件操作
- 读取/写入/校验 `PATH_AUTHZ.'license.json'`
- 用户数据库操作 `PATH_AUTHZ.'users.php'`

#### E. 特殊处理：循环依赖
**场景**：`authorization-settings.php` 创建授权文件时不能调用 `isAuthorized()`

**解决方案**：
```php
// 使用角色检查代替
if (!checkRole(['admin'], false)) {
    http_response_code(403);
    die('Forbidden');
}

// 写入授权文件
file_put_contents($licenseFile, $data);

// 立即清除缓存并启用授权检查
SystemIntegrity::clearCache();
SystemIntegrity::setPolicy(['require_license' => true]);
```

---

### 2.3 关键路径登记

**规则**：新增关键文件/目录必须在 `bl-kernel/boot/init.php` 接线区登记

**位置**：第 560-600 行附近（`// === SystemIntegrity 接线区 ===` 注释下方）

**语法**：
```php
SystemIntegrity::registerCritical('<name>', '<absolute_path>', [options]);
```

**标准登记模板**：

#### 导航页面资源（每个 NAV_KEY 都需要）
```
lang_pages_[[NAV_KEY]]_dir    → bl-languages/pages/[[NAV_KEY]]                [type=dir,  required=true]
lang_pages_[[NAV_KEY]]_en     → bl-languages/pages/[[NAV_KEY]]/en.json       [type=file, required=true]
lang_pages_[[NAV_KEY]]_zh     → bl-languages/pages/[[NAV_KEY]]/zh_CN.json    [type=file, required=true]
css_[[NAV_KEY]]               → bl-kernel/css/[[NAV_KEY]].css                 [type=file, required=true*]
js_[[NAV_KEY]]                → bl-kernel/js/[[NAV_KEY]].js                   [type=file, required=true*]
```
**注**：带 `*` 的项，工具页面可设置 `required=false` 或不登记

#### 控制器与视图
```
controller_[[NAV_KEY]]        → bl-kernel/admin/controllers/[[NAV_KEY]].php  [type=file, required=true]
view_[[NAV_KEY]]              → bl-kernel/admin/views/[[NAV_KEY]].php        [type=file, required=true]
```

#### 授权相关（初始化阶段可不存在）
```
authz_license                 → PATH_AUTHZ.'license.json'                     [type=file, required=false]
authz_users                   → PATH_AUTHZ.'users.php'                        [type=file, required=false]
```

#### 安全增强类（必需）
```
helper_password               → PATH_HELPERS.'password.class.php'             [type=file, required=true]
helper_cookie                 → PATH_HELPERS.'cookie.class.php'               [type=file, required=true]
helper_session                → PATH_HELPERS.'session.class.php'              [type=file, required=true]
```

#### 数据库文件（如涉及）
```
db_pages                      → PATH_DATABASES.'pages.php'                    [type=file, required=true]
db_site                       → PATH_DATABASES.'site.php'                     [type=file, required=true]
db_users                      → PATH_AUTHZ.'users.php'                        [type=file, required=false]
```

**参数说明**：
| 参数 | 值 | 说明 |
|-----|---|------|
| `type` | `file` / `dir` | 文件或目录 |
| `required` | `true` / `false` | 是否必需存在 |
| `algo` | `sha256` / `md5` | 哈希算法（可选，发版后启用） |
| `hash` | `abc123...` | 预期哈希值（可选） |

---

### 2.4 初始化策略

**位置**：`bl-kernel/boot/init.php` 第 603-609 行

**当前策略**（动态检测）：
```php
// 动态策略：有授权文件则强制检查
$licenseFileExists = is_readable(PATH_AUTHZ.'license.json');
SystemIntegrity::setPolicy([
  'require_license' => $licenseFileExists,  
  'license_file'    => PATH_AUTHZ.'license.json',
  'cache_ttl'       => 60,        // 进程级缓存 TTL（秒）
  'fail_http_500'   => true,      // 违规时返回 500
]);
```

**生产环境强化策略**：
```php
SystemIntegrity::setPolicy([
  'require_license' => true,      // 强制授权
  'cache_ttl'       => 0,         // 禁用缓存（安全优先）
  'fail_http_500'   => true,
]);
```

**开发环境放宽策略**：
```php
SystemIntegrity::setPolicy([
  'require_license' => false,     // 跳过授权检查
  'cache_ttl'       => 3600,      // 1 小时缓存
]);
```

---

### 2.5 未授权访问控制

**位置**：`init.php` 第 615-650 行

**白名单路由**（未授权时可访问）：
```php
$whitelist = [
    '',                        // 空路径（自动重定向到授权页）
    'login',                   // 登录页
    'logout',                  // 登出
    'ajax',                    // AJAX 请求（内部有权限验证）
    'authorization-settings',  // 授权页
    'new-user',                // 创建首个管理员
    'user-password',           // 修改密码
];
```

**路径匹配规范**：
```php
$firstSeg = strtolower(urldecode(trim(strtok($after, '/'))));
//          ^^^^^^^^^^ ^^^^^^^^^ 
//          1. 转小写   2. URL 解码（防止编码绕过）
```

**非白名单路由处理**：
```php
if (!in_array($firstSeg, $whitelist, true)) {
    $dest = $adminPrefix . 'authorization-settings?reason=missing';
    header('Cache-Control: no-store');
    header('Location: ' . $dest, true, 302);
    exit;
}
```

---

### 2.6 禁止事项

| 禁止操作 | 说明 | 后果 |
|---------|------|------|
| ❌ 修改 `SystemIntegrity` 内部实现 | 除非在类内扩展 | PR 退回 |
| ❌ 删除/注释 `quickCheck()` 调用 | 降低防护深度 | PR 退回 |
| ❌ 删除/注释 `isAuthorized()` 调用 | 绕过授权检查 | PR 退回 |
| ❌ 将 `required=true` 改为 `false` | 除非有充分理由 | 需说明 |
| ❌ 绕过检查机制 | 如直接 `file_put_contents()` | PR 退回 |
| ❌ 硬编码授权信息 | 密码/密钥/路径 | 安全风险 |

**允许的扩展方式**：
```php
// ✅ 在 SystemIntegrity 类内新增方法
public static function verifyPermissions() {
    // 新增文件权限检查
}

// ✅ 新增可选验证项
SystemIntegrity::registerCritical('my_file', PATH, [
    'sizeMin' => 1024,      // 最小字节
    'sizeMax' => 1048576,   // 最大字节
    'mtimeMin' => time()-86400, // 最早修改时间
]);
```

---

## 🧪 3. 测试要求（必测场景）

### 场景 1：未授权拦截
```bash
# 1. 删除授权文件
rm bl-kernel/_maigewan/authz/license.json

# 2. 访问敏感操作
curl -I http://localhost/admin/[[ROUTE]]

# 预期结果：302 → /admin/authorization-settings?reason=missing
```

### 场景 2：授权后正常
```bash
# 1. 上传授权文件（通过授权页或手动复制）

# 2. 访问敏感操作
curl http://localhost/admin/[[ROUTE]]

# 预期结果：200 + 正常页面内容
```

### 场景 3：关键文件缺失
```bash
# 1. 删除关键类文件
mv bl-kernel/helpers/password.class.php{,.bak}

# 2. 清除缓存
php -r "require 'index.php'; SystemIntegrity::clearCache();"

# 3. 访问页面
curl http://localhost/admin/[[ROUTE]]

# 预期结果：500 + "System Integrity Violation: PATH_MISSING"

# 4. 恢复文件
mv bl-kernel/helpers/password.class.php{.bak,}
```

### 场景 4：进程缓存验证
```bash
# 1. 运行测试脚本
php test_systemintegrity.php

# 预期结果：
# - 首次调用耗时 > 0.5ms
# - 缓存命中耗时 < 0.05ms
# - clearCache() 后重新执行检查
```

---

## 📋 4. 产出清单（文字化设计，不写代码）

### 4.1 文件创建清单

| 序号 | 文件路径 | 用途 | 必需 | 备注 |
|-----|---------|------|------|------|
| 1 | `bl-languages/pages/[[NAV_KEY]]/en.json` | 英文翻译 | ✅ | 最少键：nav, title |
| 2 | `bl-languages/pages/[[NAV_KEY]]/zh_CN.json` | 中文翻译 | ✅ | 最少键：nav, title |
| 3 | `bl-kernel/css/[[NAV_KEY]].css` | 页面样式 | ⚠️ | 工具页可省略 |
| 4 | `bl-kernel/js/[[NAV_KEY]].js` | 页面脚本 | ⚠️ | 工具页可省略 |
| 5 | `bl-kernel/admin/controllers/[[NAV_KEY]].php` | 控制器 | ✅ | 首行调用 quickCheck() |
| 6 | `bl-kernel/admin/views/[[NAV_KEY]].php` | 视图模板 | ✅ | 纯 HTML，不写业务逻辑 |

### 4.2 SystemIntegrity 接线清单

**在 `bl-kernel/boot/init.php` 接线区添加**（仅列路径）：

```php
// === 新增页面：[[NAV_KEY]] ===

// 语言文件
SystemIntegrity::registerCritical('lang_pages_[[NAV_KEY]]_dir', PATH_LANGUAGES.'pages/[[NAV_KEY]]', ['type'=>'dir', 'required'=>true]);
SystemIntegrity::registerCritical('lang_pages_[[NAV_KEY]]_en', PATH_LANGUAGES.'pages/[[NAV_KEY]]/en.json', ['type'=>'file', 'required'=>true]);
SystemIntegrity::registerCritical('lang_pages_[[NAV_KEY]]_zh', PATH_LANGUAGES.'pages/[[NAV_KEY]]/zh_CN.json', ['type'=>'file', 'required'=>true]);

// 静态资源（如果有）
SystemIntegrity::registerCritical('css_[[NAV_KEY]]', PATH_KERNEL.'css/[[NAV_KEY]].css', ['type'=>'file', 'required'=>true]);
SystemIntegrity::registerCritical('js_[[NAV_KEY]]', PATH_CORE_JS.'[[NAV_KEY]].js', ['type'=>'file', 'required'=>true]);

// 控制器与视图
SystemIntegrity::registerCritical('controller_[[NAV_KEY]]', PATH_ADMIN_CONTROLLERS.'[[NAV_KEY]].php', ['type'=>'file', 'required'=>true]);
SystemIntegrity::registerCritical('view_[[NAV_KEY]]', PATH_ADMIN_VIEWS.'[[NAV_KEY]].php', ['type'=>'file', 'required'=>true]);
```

### 4.3 控制器接线要点

**控制器开头**（第 3-6 行）：
```php
<?php defined('BLUDIT') or die('Bludit CMS.');

// SystemIntegrity check
SystemIntegrity::quickCheck();

// 其他代码...
```

**POST 处理**（如 `涉及写操作=YES`）：
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    SystemIntegrity::isAuthorized();  // ← 必须在首行
    
    // 验证权限（二次确认）
    if (!checkRole([[[PERMISSION]]], false)) {
        http_response_code(403);
        die('Forbidden');
    }
    
    // 业务逻辑...
}
```

**特殊情况**（如授权页循环依赖）：
```php
// 不调用 isAuthorized()，改用角色检查
if (!checkRole(['admin'], false)) {
    http_response_code(403);
    die('Forbidden');
}

// 写入授权文件后清除缓存
if ($result !== false) {
    SystemIntegrity::clearCache();
    SystemIntegrity::setPolicy(['require_license' => true]);
}
```

### 4.4 敏感方法接线清单

**如本次修改涉及以下方法，首行添加 `isAuthorized()`**：

#### 配置类
- [ ] `Site::set()`
- [ ] `Users::set()`
- [ ] `Security::set()`
- [ ] `Categories::set()`
- [ ] `Tags::set()`

#### 数据库类
- [ ] `dbJSON::save()`
- [ ] `dbJSON::truncate()`
- [ ] `Pages::add()`
- [ ] `Pages::edit()`
- [ ] `Pages::delete()`
- [ ] `Users::add()`
- [ ] `Users::delete()`
- [ ] `dbList::add()`
- [ ] `dbList::remove()`
- [ ] `dbList::edit()`

#### 文件系统类
- [ ] `Filesystem::mkdir()`
- [ ] `Filesystem::rmfile()`
- [ ] `Filesystem::rmdir()`

#### AJAX 端点
- [ ] `ajax/upload-images.php`
- [ ] `ajax/logo-upload.php`
- [ ] `ajax/profile-picture-upload.php`
- [ ] `ajax/delete-image.php`
- [ ] `ajax/logo-remove.php`
- [ ] `ajax/save-as-draft.php`

### 4.5 菜单注册（如需要）

**位置**：`bl-kernel/admin/themes/[theme]/index.php` 或导航配置文件

**菜单项结构**：
```
key: [[NAV_KEY]]
label: 从 i18n 读取（$L->get('[[NAV_KEY]]-nav')）
route: /admin/[[NAV_KEY]]
permission: [[PERMISSION]]
icon: （可选）
```

---

## ✅ 5. PR 提交检查清单

### 5.1 SystemIntegrity 合规性

- [ ] **控制器接线**：首行含 `SystemIntegrity::quickCheck()`
- [ ] **敏感方法接线**：所有写入方法首行含 `isAuthorized()`
- [ ] **路径登记**：新增文件已在 `init.php` 登记，`required` 属性正确
- [ ] **路径匹配**：使用 `urldecode()` + `strtolower()` 处理
- [ ] **未改动核心**：未修改 `SystemIntegrity` 类内部实现
- [ ] **策略正确**：`require_license` 策略符合环境要求

### 5.2 功能测试

- [ ] **未授权访问**：删除授权文件后自动跳转（场景 1）
- [ ] **授权后正常**：上传授权后正常访问（场景 2）
- [ ] **关键文件缺失**：删除关键文件触发 500（场景 3）
- [ ] **进程缓存**：缓存机制正常工作（场景 4）

### 5.3 代码质量

- [ ] **无调试代码**：移除所有 `var_dump()`, `print_r()`, `console.log()`, `die()` 调试语句
- [ ] **敏感信息**：移除硬编码的密码、密钥、内部路径
- [ ] **代码规范**：符合 PSR-12 或项目约定
- [ ] **注释完整**：关键逻辑有注释说明
- [ ] **错误处理**：有适当的错误处理和日志记录

### 5.4 i18n 完整性

- [ ] **翻译文件**：`en.json` 和 `zh_CN.json` 都已创建
- [ ] **必需键**：至少包含 `nav` 和 `title`
- [ ] **表单键**：如有表单，包含 `btn_save`, `msg_saved` 等
- [ ] **错误消息**：包含 `msg_error`, `msg_forbidden` 等

### 5.5 文档更新（如需要）

- [ ] **README**：更新功能列表（如有）
- [ ] **CHANGELOG**：记录变更内容
- [ ] **API 文档**：更新接口文档（如涉及 API）

---

## 📚 6. 参考资料

### 完整文档
- **架构约束定稿**：`SI_POLICY_v1.2.md`
- **敏感方法清单**：`SENSITIVE_METHODS_CHECKLIST.md`（100+ 项）
- **快速检查清单**：`PR_QUICK_CHECKLIST.md`（5 分钟自检）
- **多层防护可视化**：`MULTILAYER_PROTECTION_VISUALIZATION.md`
- **PR 模板**：`.github/PULL_REQUEST_TEMPLATE.md`

### 测试工具
- **自动化测试**：`test_systemintegrity.php`
- **删除影响测试**：`test_delete_quickcheck.sh`

### 已接线示例
- **用户管理**：`bl-kernel/users.class.php`
- **登录验证**：`bl-kernel/login.class.php`
- **安全配置**：`bl-kernel/admin/controllers/security-general.php`
- **授权管理**：`bl-kernel/admin/controllers/authorization-settings.php`

---

## 🎯 7. 常见问题 FAQ

### Q1: 为什么删除 `quickCheck()` 后没报错？
**A**: 因为多层防护机制：`init.php` 全局检查 + 路由拦截 + 控制器检查 + 方法级检查。即使删除单层，其他层仍在保护。

### Q2: 什么时候用 `isAuthorized()`，什么时候用 `checkRole()`？
**A**: 
- `isAuthorized()` - 检查授权文件是否存在（用于敏感写入操作）
- `checkRole()` - 检查用户角色权限（用于访问控制）
- 授权页特殊处理：只用 `checkRole()`，避免循环依赖

### Q3: `required=false` 什么时候用？
**A**: 
- 授权文件（`license.json`）- 首次安装时不存在
- 用户数据库（`users.php`）- 初始化前不存在
- 可选的 CSS/JS 文件 - 工具页面可不需要

### Q4: 如何测试进程缓存是否生效？
**A**: 运行 `php test_systemintegrity.php`，查看：
- 首次调用耗时 > 0.5ms
- 缓存命中耗时 < 0.05ms

### Q5: 如何清除缓存使授权立即生效？
**A**: 
```php
SystemIntegrity::clearCache();
SystemIntegrity::setPolicy(['require_license' => true]);
```

### Q6: 能否临时禁用授权检查（开发环境）？
**A**: 可以，在 `init.php` 中设置：
```php
SystemIntegrity::setPolicy(['require_license' => false]);
```

### Q7: 新增的文件一定要登记吗？
**A**: 
- ✅ 必须登记：控制器、视图、翻译文件、核心类
- ⚠️ 建议登记：CSS、JS、辅助文件
- ❌ 无需登记：临时文件、用户上传的文件

---

## 📊 8. 附录：完整接线示例

### 示例：新增"系统监控"页面

#### 元信息
```
需求代号：FEAT-2025-002
目标路由：/admin/system-monitor
导航唯一名：system-monitor
权限等级：admin
涉及写操作：NO
作用范围：PER-SITE
数据库操作：READ
```

#### 文件清单
```
✅ bl-languages/pages/system-monitor/en.json
✅ bl-languages/pages/system-monitor/zh_CN.json
✅ bl-kernel/css/system-monitor.css
✅ bl-kernel/js/system-monitor.js
✅ bl-kernel/admin/controllers/system-monitor.php
✅ bl-kernel/admin/views/system-monitor.php
```

#### init.php 接线
```php
// === 系统监控页面 ===
SystemIntegrity::registerCritical('lang_pages_system_monitor_dir', PATH_LANGUAGES.'pages/system-monitor', ['type'=>'dir', 'required'=>true]);
SystemIntegrity::registerCritical('lang_pages_system_monitor_en', PATH_LANGUAGES.'pages/system-monitor/en.json', ['type'=>'file', 'required'=>true]);
SystemIntegrity::registerCritical('lang_pages_system_monitor_zh', PATH_LANGUAGES.'pages/system-monitor/zh_CN.json', ['type'=>'file', 'required'=>true]);
SystemIntegrity::registerCritical('css_system_monitor', PATH_KERNEL.'css/system-monitor.css', ['type'=>'file', 'required'=>true]);
SystemIntegrity::registerCritical('js_system_monitor', PATH_CORE_JS.'system-monitor.js', ['type'=>'file', 'required'=>true]);
```

#### 控制器要点
```
第 3 行：SystemIntegrity::quickCheck();
第 10 行：只读操作，无需 isAuthorized()
第 20 行：权限检查 checkRole(['admin'], false)
```

---

**最后确认**：违反任一 SystemIntegrity 硬约束 = 架构缺陷，PR 直接退回。

**版本**：v1.3（2025-11-03）
