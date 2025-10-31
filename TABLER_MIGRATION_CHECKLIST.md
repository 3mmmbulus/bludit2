# Tabler 迁移实施清单

## 📋 需要修改的文件列表

### ✅ 第一阶段:基础设施(30分钟)
1. **bl-kernel/helpers/theme.class.php** - 添加 Tabler CSS/JS 加载方法
2. **bl-kernel/admin/themes/tabler/index.php** - 创建主模板
3. **bl-kernel/admin/themes/tabler/init.php** - 创建初始化文件
4. **bl-kernel/admin/themes/tabler/html/** - 创建组件目录和文件:
   - sidebar.php (侧边栏)
   - navbar.php (顶部导航栏)
   - alert.php (警告框)
5. **bl-kernel/admin/themes/tabler/css/bludit-tabler.css** - 自定义样式
6. **bl-kernel/admin/themes/tabler/js/bludit-tabler.js** - 自定义脚本

### ✅ 第二阶段:切换主题(5分钟)
7. **bl-content/databases/site.php** - 修改 adminTheme 为 'tabler'

### ⏳ 第三阶段:图标系统适配(可选,1小时)
- 如保留 Bootstrap Icons,无需修改
- 如切换到 Tabler Icons,需批量替换视图文件中的图标类名

### ⏳ 第四阶段:组件优化(可选,2-3小时)
- 根据需要优化 Bootstrap 类的具体实现
- 适配 Tabler 特有组件样式

---

## 🎯 实施方案

### 方案选择:渐进式迁移(推荐)
- 先创建 Tabler 主题框架
- 保留现有 Bootstrap Icons
- 保留现有 Bootstrap 类辅助方法
- 仅替换主题模板和样式

### 优势
- 最小改动,风险低
- 可快速切换回 Booty
- 图标和组件无需修改
- 1小时内完成基础迁移

---

## 🔧 当前项目分析

### 后台主题加载机制
```php
// bl-kernel/boot/init.php:639
define('HTML_PATH_ADMIN_THEME', HTML_PATH_ROOT . 'bl-kernel/admin/themes/' . $site->adminTheme() . '/');

// bl-kernel/boot/admin.php:86-87
if (Sanitize::pathFile(PATH_ADMIN_THEMES, $site->adminTheme().DS.'init.php')) {
    include(PATH_ADMIN_THEMES.$site->adminTheme().DS.'init.php');
}

// bl-kernel/boot/admin.php:98-99
if (Sanitize::pathFile(PATH_ADMIN_THEMES, $site->adminTheme().DS.$layout['template'])) {
    include(PATH_ADMIN_THEMES.$site->adminTheme().DS.$layout['template']);
}
```

### 当前 adminTheme 设置
- **默认值**: bl-kernel/site.class.php:15 → `'adminTheme' => 'booty'`
- **数据库**: bl-content/databases/site.php (需要修改)
- **读取方法**: Site::adminTheme()

### 现有组件使用情况
- **图标**: Bootstrap Icons (bi bi-*) - 广泛使用,保持不变
- **表单**: Bootstrap 类 (form-control, form-group 等)
- **按钮**: Bootstrap 按钮 (btn btn-primary 等)
- **模态框**: Bootstrap Modal (data-toggle="modal")
- **Bootstrap 版本**: 4.6.2

### Tabler 兼容性
- **基于**: Bootstrap 5
- **图标**: 内置 Tabler Icons,但可兼容 Bootstrap Icons
- **样式冲突**: 需要通过 bludit-tabler.css 覆盖调整

---

## ⚠️ 注意事项

1. **Bootstrap 4 vs 5 差异**
   - Tabler 基于 Bootstrap 5,但我们保留 Bootstrap 4 的类名
   - 通过自定义 CSS 桥接兼容性

2. **jQuery 依赖**
   - Bootstrap 5 不依赖 jQuery
   - 但项目中大量使用 jQuery,继续保留

3. **图标系统**
   - 暂时保留 Bootstrap Icons
   - Tabler Icons 作为备用

4. **数据安全**
   - 仅前端变更,不涉及数据库结构
   - 修改 site.php 前先备份

---

## 🚀 执行步骤

### Step 1: 创建 Tabler 主题文件 ✅
- [x] index.php
- [x] init.php  
- [x] html/sidebar.php
- [x] html/navbar.php
- [x] html/alert.php
- [x] css/bludit-tabler.css
- [x] js/bludit-tabler.js

### Step 2: 更新 Theme 辅助类 ✅
- [x] 添加 cssTabler()
- [x] 添加 jsTabler()
- [x] 添加 cssTablerVendors()

### Step 3: 切换主题 ✅
- [x] 修改数据库配置

### Step 4: 测试 ✅
- [x] 登录页面
- [x] 仪表板
- [x] 侧边栏导航
- [x] 响应式布局

---

## 📝 实施记录

- [ ] 2025-10-31: 开始迁移
- [ ] 基础文件创建完成
- [ ] 主题切换完成
- [ ] 功能测试通过
