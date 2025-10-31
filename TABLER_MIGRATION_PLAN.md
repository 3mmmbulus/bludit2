# Tabler 后台管理界面移植方案

## 📋 项目概述

**目标**: 将 Bludit 后台管理界面从 Bootstrap 4 + 自定义样式迁移到 Tabler Admin Template

**Tabler 优势**:
- 基于 Bootstrap 5
- 现代化 UI/UX 设计
- 内置大量组件和页面模板
- 响应式设计
- 支持深色模式
- 包含 Tabler Icons (1400+ 图标)

---

## 🎯 移植策略

### 方案 A: 渐进式迁移 (推荐)
**适合**: 保持系统稳定,逐步优化
**优点**: 
- 风险低,可随时回滚
- 可以先测试再全面应用
- 保留现有功能完整性

**缺点**:
- 时间较长
- 需要维护两套样式

### 方案 B: 全面重构
**适合**: 希望彻底重做后台界面
**优点**:
- 一次性获得全新 UI
- 代码更整洁

**缺点**:
- 工作量大
- 可能影响现有功能
- 需要大量测试

---

## 📦 需要的资源

### 1. Tabler 核心文件
```
从 https://github.com/tabler/tabler/releases 下载最新版本

需要的文件:
├── dist/
│   ├── css/
│   │   ├── tabler.min.css         (主样式)
│   │   └── tabler-flags.min.css   (国旗图标,可选)
│   ├── js/
│   │   └── tabler.min.js          (主 JS)
│   └── libs/
│       └── @tabler/icons/         (Tabler Icons 字体)
```

### 2. 依赖项更新
- Bootstrap 4.6.2 → Bootstrap 5.3.x (Tabler 已内置)
- jQuery → 可选 (Tabler 不强制依赖)
- Bootstrap Icons → Tabler Icons (可保留作为备选)

---

## 🛠️ 实施步骤

### 阶段 1: 准备工作 (1-2小时)

#### 1.1 下载 Tabler
```bash
cd /www/wwwroot/maigewan
mkdir -p tabler-temp
cd tabler-temp
wget https://github.com/tabler/tabler/archive/refs/tags/v1.0.0-beta20.tar.gz
tar -xzf v1.0.0-beta20.tar.gz
```

#### 1.2 复制文件到项目
```bash
# 创建新主题目录
mkdir -p bl-kernel/admin/themes/tabler

# 复制 Tabler 资源
cp -r tabler-1.0.0-beta20/dist/css bl-kernel/admin/themes/tabler/
cp -r tabler-1.0.0-beta20/dist/js bl-kernel/admin/themes/tabler/
cp -r tabler-1.0.0-beta20/dist/libs bl-kernel/admin/themes/tabler/
```

#### 1.3 更新 Theme 辅助类
在 `bl-kernel/helpers/theme.class.php` 添加 Tabler 加载方法:

```php
public static function cssTabler()
{
    return '<link rel="stylesheet" href="' . DOMAIN_ADMIN_THEME . 'css/tabler.min.css?version=' . BLUDIT_VERSION . '">' . PHP_EOL;
}

public static function jsTabler()
{
    return '<script src="' . DOMAIN_ADMIN_THEME . 'js/tabler.min.js?version=' . BLUDIT_VERSION . '"></script>' . PHP_EOL;
}

public static function cssTablerIcons()
{
    return '<link rel="stylesheet" href="' . DOMAIN_ADMIN_THEME . 'libs/@tabler/icons/tabler-icons.min.css?version=' . BLUDIT_VERSION . '">' . PHP_EOL;
}
```

---

### 阶段 2: 创建 Tabler 主题 (3-5小时)

#### 2.1 创建主题结构
```
bl-kernel/admin/themes/tabler/
├── css/
│   ├── tabler.min.css
│   └── bludit-tabler.css      (自定义覆盖样式)
├── js/
│   ├── tabler.min.js
│   └── bludit-tabler.js       (自定义 JS)
├── libs/
│   └── @tabler/icons/
├── html/
│   ├── sidebar.php            (侧边栏)
│   ├── navbar.php             (顶部导航)
│   └── footer.php             (页脚)
└── index.php                  (主模板)
```

#### 2.2 创建主模板 index.php
基于 Tabler 的 HTML 结构重写:

```php
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title><?php echo $layout['title'] ?></title>
    
    <!-- CSS files -->
    <?php
        echo Theme::cssTabler();
        echo Theme::cssTablerIcons();
        echo Theme::css(array('bludit-tabler.css'), DOMAIN_ADMIN_THEME_CSS);
    ?>
</head>
<body>
    <div class="page">
        <!-- Sidebar -->
        <?php include('html/sidebar.php'); ?>
        
        <!-- Navbar -->
        <?php include('html/navbar.php'); ?>
        
        <div class="page-wrapper">
            <!-- Page header -->
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <?php echo Bootstrap::pageTitle($layout['title'], $layout['icon']); ?>
                </div>
            </div>
            
            <!-- Page body -->
            <div class="page-body">
                <div class="container-xl">
                    <?php echo $layout['content'] ?>
                </div>
            </div>
            
            <!-- Footer -->
            <?php include('html/footer.php'); ?>
        </div>
    </div>
    
    <!-- JS files -->
    <?php
        echo Theme::jsTabler();
        echo Theme::js(array('bludit-tabler.js'), DOMAIN_ADMIN_THEME_JS);
        Theme::plugins('adminBodyEnd');
    ?>
</body>
</html>
```

#### 2.3 创建侧边栏 html/sidebar.php
参考 Tabler 的侧边栏结构:

```php
<aside class="navbar navbar-vertical navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand">
            <a href="<?php echo HTML_PATH_ADMIN_ROOT ?>">
                <img src="<?php echo HTML_PATH_CORE_IMG.'logo.svg' ?>" 
                     alt="Bludit" class="navbar-brand-image">
            </a>
        </h1>
        
        <div class="navbar-nav flex-row d-lg-none">
            <!-- 移动端额外按钮 -->
        </div>
        
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'dashboard' ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-dashboard"></i>
                        </span>
                        <span class="nav-link-title">仪表板</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'new-content' ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-plus"></i>
                        </span>
                        <span class="nav-link-title">新建内容</span>
                    </a>
                </li>
                
                <!-- 更多菜单项... -->
            </ul>
        </div>
    </div>
</aside>
```

---

### 阶段 3: 图标系统迁移 (2-3小时)

#### 3.1 图标映射策略

**选项 1**: 使用 Tabler Icons (推荐)
```
Tabler Icons 类名格式: ti ti-{icon-name}
示例: <i class="ti ti-home"></i>
```

**选项 2**: 保留 Bootstrap Icons
```
可以同时保留,作为 Tabler Icons 的补充
```

#### 3.2 图标替换映射表

| 功能 | Bootstrap Icons | Tabler Icons |
|------|----------------|--------------|
| 仪表板 | bi-speedometer2 | ti-dashboard |
| 首页 | bi-house-door | ti-home |
| 新建 | bi-plus-circle-fill | ti-plus-circle |
| 内容 | bi-archive | ti-folder |
| 用户 | bi-person | ti-user |
| 设置 | bi-gear | ti-settings |
| 退出 | bi-box-arrow-right | ti-logout |

#### 3.3 批量替换脚本
```bash
# 将 Bootstrap Icons 类名替换为 Tabler Icons
cd /www/wwwroot/maigewan/bl-kernel/admin/themes/tabler

# 替换图标类名
find . -name "*.php" -exec sed -i 's/bi bi-speedometer2/ti ti-dashboard/g' {} \;
find . -name "*.php" -exec sed -i 's/bi bi-house-door/ti ti-home/g' {} \;
# ... 更多替换
```

---

### 阶段 4: 组件迁移 (5-8小时)

需要重写的主要组件:

#### 4.1 表单组件
- 文本框 → Tabler input
- 下拉框 → Tabler select (或继续使用 Select2)
- 按钮 → Tabler button
- 开关 → Tabler switch

#### 4.2 列表/表格
- 内容列表 → Tabler table
- 分页 → Tabler pagination

#### 4.3 模态框
- Bootstrap Modal → Tabler Modal

#### 4.4 提示/通知
- 警告框 → Tabler Alert
- Toast 通知 → Tabler Toast

---

### 阶段 5: 测试和优化 (3-5小时)

#### 5.1 功能测试清单
- [ ] 登录页面
- [ ] 仪表板显示
- [ ] 侧边栏导航
- [ ] 内容列表
- [ ] 新建/编辑内容
- [ ] 媒体管理器
- [ ] 用户管理
- [ ] 设置页面
- [ ] 插件页面
- [ ] 响应式布局 (手机/平板)

#### 5.2 性能优化
- [ ] 压缩 CSS/JS
- [ ] 移除未使用的组件
- [ ] 优化图片资源
- [ ] 启用浏览器缓存

---

## 🔄 渐进式迁移详细步骤

### Step 1: 切换主题开关
在后台设置中添加主题选择:

```php
// bl-kernel/admin/boot/init.php
define('ADMIN_THEME', 'booty'); // 或 'tabler'
```

### Step 2: 逐页迁移
优先级排序:
1. 登录页面
2. 仪表板
3. 内容列表
4. 内容编辑
5. 其他管理页面

### Step 3: 数据迁移
无需数据库迁移,仅前端变化

### Step 4: 插件兼容性
检查第三方插件的 CSS/JS 是否与 Tabler 兼容

---

## 📝 需要修改的核心文件

### 必改文件
1. `bl-kernel/helpers/theme.class.php` - 添加 Tabler 加载方法
2. `bl-kernel/admin/themes/tabler/index.php` - 新主题模板
3. `bl-kernel/admin/themes/tabler/html/*.php` - 组件模板

### 可选修改
1. `bl-kernel/admin/boot/init.php` - 主题切换逻辑
2. `bl-kernel/admin/views/*.php` - 视图文件 (如需深度定制)

---

## ⚠️ 注意事项

### 兼容性问题
1. **Bootstrap 5 vs Bootstrap 4**
   - 类名变化: `ml-*` → `ms-*`, `mr-*` → `me-*`
   - jQuery 依赖: Bootstrap 5 不再依赖 jQuery
   - 某些组件 API 变化

2. **Select2 兼容性**
   - 需要 Select2 Bootstrap 5 主题
   - 或考虑使用 Tabler 原生 select

3. **日期选择器**
   - 当前使用 datetimepicker
   - 考虑迁移到 Tabler 推荐的 Flatpickr

### 数据安全
- 备份数据库 (虽然只是前端变更)
- 备份当前主题文件
- 使用 Git 版本控制

### 性能考虑
- Tabler 完整版约 300KB (压缩后)
- 可以按需加载组件减小体积
- 建议启用 CDN 加速

---

## 🎨 自定义和扩展

### 修改主题色
在 `bludit-tabler.css` 中覆盖 CSS 变量:

```css
:root {
    --tblr-primary: #0078D4;  /* 自定义主色 */
    --tblr-secondary: #6c757d;
}
```

### 添加自定义组件
参考 Tabler 文档创建:
- https://preview.tabler.io/docs/

### 深色模式支持
Tabler 内置深色模式切换:

```html
<body data-bs-theme="dark">
```

---

## 📊 工作量估算

| 任务 | 预计时间 | 难度 |
|------|---------|------|
| 环境准备 | 1-2 小时 | ⭐ |
| 主题框架搭建 | 3-5 小时 | ⭐⭐ |
| 图标系统迁移 | 2-3 小时 | ⭐⭐ |
| 组件迁移 | 5-8 小时 | ⭐⭐⭐ |
| 测试优化 | 3-5 小时 | ⭐⭐ |
| **总计** | **14-23 小时** | |

---

## 🚀 快速开始

### 最简方案 (仅换样式)
1. 下载 Tabler CSS/JS
2. 替换 `Theme::cssBootstrap()` 为 `Theme::cssTabler()`
3. 微调布局适配

**优点**: 快速,工作量小
**缺点**: 无法使用 Tabler 高级组件

### 完整方案 (推荐)
按照上述阶段 1-5 完整实施

**优点**: 获得完整 Tabler 功能
**缺点**: 工作量大,需要仔细测试

---

## 📚 参考资源

- Tabler 官方文档: https://tabler.io/docs
- Tabler GitHub: https://github.com/tabler/tabler
- Tabler Icons: https://tabler-icons.io/
- Bootstrap 5 迁移指南: https://getbootstrap.com/docs/5.3/migration/
- Tabler 在线预览: https://preview.tabler.io/

---

## ✅ 检查清单

开始前:
- [ ] 备份数据库
- [ ] 备份当前主题文件
- [ ] 创建 Git 分支
- [ ] 下载 Tabler 资源

实施中:
- [ ] 创建新主题目录
- [ ] 复制 Tabler 文件
- [ ] 修改 Theme 辅助类
- [ ] 创建主模板
- [ ] 迁移组件
- [ ] 替换图标

完成后:
- [ ] 功能测试
- [ ] 响应式测试
- [ ] 性能测试
- [ ] 浏览器兼容性测试
- [ ] 插件兼容性测试

---

## 🤔 我的建议

**推荐方案**: 渐进式迁移

**理由**:
1. 降低风险,可随时回滚
2. 可以先在开发环境测试
3. 逐步优化,避免一次性大改动
4. 保持系统稳定运行

**第一步**: 
创建 Tabler 主题作为可选主题,与现有 Booty 主题并存,允许用户切换选择。

**第二步**: 
待 Tabler 主题稳定后,再考虑是否完全替换。

---

需要我帮您开始实施吗? 我可以:
1. 下载和配置 Tabler 资源
2. 创建基础主题结构
3. 修改必要的辅助类
4. 创建示例页面供您测试
