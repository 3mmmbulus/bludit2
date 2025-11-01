# Footer 使用指南

## 📁 文件位置

公共 Footer 文件位于：
```
/bl-kernel/admin/themes/tabler/includes/
├── footer.php         # 完整版 Footer（用于后台主界面）
└── footer-simple.php  # 简化版 Footer（用于登录、系统初始化等独立页面）
```

## 🎨 样式说明

### 1. 完整版 Footer (`footer.php`)

**适用场景**: 后台管理界面（Dashboard、设置页面等）

**特点**:
- 使用 Tabler 官方 `footer` 组件样式
- 包含版权信息、关于、文档等链接
- 支持多语言
- 响应式设计

**引入方式**:
```php
<?php include(PATH_ADMIN_THEMES . $site->adminTheme() . DS . 'includes' . DS . 'footer.php'); ?>
```

**示例效果**:
```
© 2025 Maigewan | About | Documentation    Powered by Bludit | Tabler
```

---

### 2. 简化版 Footer (`footer-simple.php`)

**适用场景**: 登录页、系统初始化、错误页等独立页面

**特点**:
- 简洁的单行样式
- 适合居中布局的独立页面
- 自动显示当前年份

**引入方式**:
```php
<?php include(PATH_ADMIN_THEMES . $site->adminTheme() . DS . 'includes' . DS . 'footer-simple.php'); ?>
```

**示例效果**:
```
© 2025 Maigewan • Powered by Bludit
```

---

## 📝 使用示例

### 在主模板中使用完整版

```php
<!doctype html>
<html>
<head>...</head>
<body>
    <div class="page">
        <!-- Header -->
        <?php include(...); ?>
        
        <!-- Main Content -->
        <div class="page-wrapper">
            <div class="page-body">
                <!-- Your content here -->
            </div>
            
            <!-- Footer -->
            <?php include(PATH_ADMIN_THEMES . $site->adminTheme() . DS . 'includes' . DS . 'footer.php'); ?>
        </div>
    </div>
</body>
</html>
```

### 在独立页面中使用简化版

```php
<!doctype html>
<html>
<head>...</head>
<body class="d-flex flex-column">
    <div class="page page-center">
        <div class="container container-tight py-4">
            
            <!-- Logo -->
            <div class="text-center mb-4">...</div>
            
            <!-- Main Card -->
            <div class="card card-md">
                <div class="card-body">
                    <!-- Your content here -->
                </div>
            </div>
            
            <!-- Footer -->
            <?php include(PATH_ADMIN_THEMES . $site->adminTheme() . DS . 'includes' . DS . 'footer-simple.php'); ?>
        </div>
    </div>
</body>
</html>
```

---

## 🎨 自定义说明

### 修改版权信息

编辑对应的 footer 文件，修改：
```php
© <?php echo date('Y') ?> Maigewan
```

### 修改链接

在 `footer.php` 中修改链接列表：
```php
<li class="list-inline-item">
    <a href="你的链接" target="_blank" class="link-secondary">
        链接文本
    </a>
</li>
```

### 添加版本号

```php
<li class="list-inline-item">
    v<?php echo BLUDIT_VERSION ?>
</li>
```

---

## 🌍 多语言支持

Footer 已集成多语言支持，使用全局 `$L` 对象：

```php
<?php echo isset($L) ? $L->get('About') : 'About' ?>
```

需要在语言文件中添加对应的翻译键。

---

## 📱 响应式设计

Footer 使用 Tabler 的响应式类：
- `col-12 col-lg-auto` - 移动端全宽，桌面端自适应
- `mt-3 mt-lg-0` - 移动端上边距，桌面端无边距
- `flex-row-reverse` - 桌面端反向排列

---

## 🎯 最佳实践

1. **后台主界面** 使用 `footer.php`（完整版）
2. **登录/注册页** 使用 `footer-simple.php`（简化版）
3. **系统初始化页** 使用 `footer-simple.php`（简化版）
4. **错误页面** 使用 `footer-simple.php`（简化版）

---

## 🔄 已应用的页面

- ✅ `/bl-kernel/admin/themes/tabler/system-init.php` - 系统初始化页

## 📋 待应用的页面

- [ ] 登录页
- [ ] 后台主模板
- [ ] 设置页面
- [ ] 其他管理页面
