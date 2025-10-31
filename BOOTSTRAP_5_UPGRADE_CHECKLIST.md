# Bootstrap 5.3.1 全局升级文件清单

> 升级日期: 2025年10月31日
> 目标: 将整个项目从 Bootstrap 4.6.2 升级到 Bootstrap 5.3.1
> 策略: 全局替换,删除 Booty 主题,统一使用 Bootstrap 5

---

## 📋 一、核心文件替换

### 1.1 Bootstrap 核心文件 (必须)

```bash
# 需要替换的文件
bl-kernel/css/bootstrap.min.css          # Bootstrap 4.6.2 → 5.3.1
bl-kernel/js/bootstrap.bundle.min.js     # Bootstrap 4.6.2 → 5.3.1

# 替换方法
# 从 Tabler 提取或从官方下载
cp tabler-temp/tabler-1.0.0-beta20/dist/libs/bootstrap/dist/css/bootstrap.min.css bl-kernel/css/
cp tabler-temp/tabler-1.0.0-beta20/dist/libs/bootstrap/dist/js/bootstrap.bundle.min.js bl-kernel/js/
```

**影响范围:** 所有使用 `Theme::cssBootstrap()` 和 `Theme::jsBootstrap()` 的页面

---

## 📝 二、Theme Helper 类修改

### 2.1 theme.class.php (关键)

**文件:** `bl-kernel/helpers/theme.class.php`

**需要修改的方法:**
- `cssBootstrap()` - 已指向正确路径,无需改动
- `jsBootstrap()` - 已指向正确路径,无需改动
- `jquery()` - **保留,Bootstrap 5 仍需 jQuery 支持插件**

**状态:** ✅ 无需修改 (路径已正确)

---

## 🎨 三、后台主题文件修改

### 3.1 Tabler 主题 init.php (核心)

**文件:** `bl-kernel/admin/themes/tabler/init.php`

**需要修改的 Bootstrap 4 特性:**

#### A. 模态框按钮 (Modal Dismiss)
```php
# 第 29 行 - 修改前
data-dismiss="modal"

# 修改后
data-bs-dismiss="modal"
```

#### B. 表单组 (Form Groups) - Bootstrap 5 已移除
```php
# 多处需要修改 (第 122, 183, 212, 260, 283, 330, 363 行)
# 修改前
class="form-group m-0"
class="form-group row"

# 修改后 - 使用 margin utilities
class="mb-3"
class="row mb-3"
```

#### C. 自定义文件上传 (Custom File Input)
```php
# 第 158, 164, 165 行 - 修改前
class="custom-file"
class="custom-file-input"
class="custom-file-label"

# 修改后 - Bootstrap 5 原生样式
class="mb-3"
class="form-control"
<label class="form-label">...</label>
```

#### D. 自定义选择框 (Custom Select)
```php
# 第 325, 358 行 - 修改前
class="custom-select"

# 修改后
class="form-select"
```

**修改行数统计:**
- `data-dismiss` → `data-bs-dismiss`: 1 处
- `form-group` → `mb-3` 或移除: 7 处
- `custom-file*` → 原生样式: 3 处
- `custom-select` → `form-select`: 2 处

---

### 3.2 Tabler 主题 index.php

**文件:** `bl-kernel/admin/themes/tabler/index.php`

**需要修改:**
```php
# 第 23 行
'select2-bootstrap4.min.css'  →  'select2.min.css' 或 'select2-bootstrap5.min.css'
```

**说明:** Select2 需要更新到支持 Bootstrap 5 的版本

---

### 3.3 Tabler CSS 兼容文件

**文件:** `bl-kernel/admin/themes/tabler/css/bludit-tabler.css`

**需要移除或更新的兼容类:**
```css
/* 这些 Bootstrap 4 到 5 的兼容类可以移除了 */
.ml-1 { margin-left: 0.25rem !important; }
.ml-2 { margin-left: 0.5rem !important; }
.ml-3 { margin-left: 1rem !important; }
.mr-1 { margin-right: 0.25rem !important; }
.mr-2 { margin-right: 0.5rem !important; }
.mr-3 { margin-right: 1rem !important; }
.pl-1 { padding-left: 0.25rem !important; }
.pl-2 { padding-left: 0.5rem !important; }
.pl-3 { padding-left: 1rem !important; }
.pr-1 { padding-right: 0.25rem !important; }
.pr-2 { padding-right: 0.5rem !important; }
.pr-3 { padding-right: 1rem !important; }

.form-group { margin-bottom: 1rem; }
```

**操作:** 可以删除整个文件,或保留 Tabler 特定样式

---

### 3.4 Tabler JS 兼容文件

**文件:** `bl-kernel/admin/themes/tabler/js/bludit-tabler.js`

**需要移除的自动转换代码:**
```javascript
// 删除以下兼容代码 (7-35 行)
// 兼容 Bootstrap 4 的 data-toggle 和 data-target
$('[data-toggle="modal"]').each(function() { ... });
$('[data-toggle="dropdown"]').each(function() { ... });
$('[data-toggle="collapse"]').each(function() { ... });
$('[data-dismiss="modal"]').each(function() { ... });
$('[data-dismiss="alert"]').each(function() { ... });
```

**操作:** 删除兼容代码,保留 Tabler 特定功能

---

## 📄 四、后台视图文件修改 (View Files)

### 4.1 需要修改 data-* 属性的文件

| 文件路径 | 行号 | 修改内容 |
|---------|------|---------|
| `bl-kernel/admin/views/edit-content.php` | 52 | `data-toggle="modal"` → `data-bs-toggle="modal"` |
| `bl-kernel/admin/views/edit-content.php` | 52 | `data-target="#..."` → `data-bs-target="#..."` |
| `bl-kernel/admin/views/edit-content.php` | 84-89 | `data-toggle="tab"` → `data-bs-toggle="tab"` (5处) |
| `bl-kernel/admin/views/login.php` | 31 | `class="mr-2"` → `class="me-2"` |

### 4.2 需要修改间距类的文件

| 文件路径 | Bootstrap 4 类 | Bootstrap 5 类 |
|---------|---------------|---------------|
| `edit-content.php` 行 59 | `ml-2` | `ms-2` |
| `edit-content.php` 行 93 | `pr-3 pl-3 pb-3` | `pe-3 ps-3 pb-3` |
| `users.php` 行 33 | `mr-1` | `me-1` |
| `dashboard.php` 行 91 | `ml-2` | `ms-2` |
| `themes.php` 行 22 | `ml-2` | `ms-2` |
| `login.php` 行 31 | `mr-2` | `me-2` |

### 4.3 需要移除 form-group 的文件

**受影响文件列表:**
```
bl-kernel/admin/views/cache-settings.php       (2 处)
bl-kernel/admin/views/spider-settings.php      (2 处)
bl-kernel/admin/views/login.php                (3 处)
bl-kernel/admin/views/dashboard.php            (1 处)
bl-kernel/admin/views/security-system.php      (2 处)
bl-kernel/admin/views/ads-settings.php         (2 处)
bl-kernel/admin/views/security-general.php     (2 处)
bl-kernel/admin/views/site-new.php             (2 处)
bl-kernel/admin/views/authorization-settings.php (1 处)
bl-kernel/admin/views/seo-settings.php         (3 处)
bl-kernel/admin/views/profile.php              (3 处)
```

**修改方法:**
```html
<!-- 修改前 -->
<div class="form-group">
    <label>...</label>
    <input class="form-control">
</div>

<!-- 修改后 -->
<div class="mb-3">
    <label class="form-label">...</label>
    <input class="form-control">
</div>
```

---

## 🔌 五、插件文件修改

### 5.1 Simple Stats 插件

**文件:** `bl-plugins/simple-stats/plugin.php`

**修改:**
```php
# 第 234-235 行
data-toggle="tab"  →  data-bs-toggle="tab"
```

---

## 🎨 六、Select2 插件升级

### 6.1 需要更新的文件

**当前使用:** `select2-bootstrap4.min.css`
**需要升级到:** `select2-bootstrap-5-theme.min.css`

**受影响文件:**
```
bl-kernel/admin/themes/tabler/index.php (第 23 行)
bl-kernel/admin/views/dashboard.php (第 57 行: theme: "bootstrap4")
bl-kernel/admin/views/edit-content.php (第 220 行: theme: "bootstrap4")
```

**修改后:**
```javascript
theme: "bootstrap-5"  // 或者删除此选项使用默认
```

---

## 🗑️ 七、需要删除的文件和目录

### 7.1 Booty 主题 (完全删除)

```bash
rm -rf bl-kernel/admin/themes/booty/
```

**包含文件:**
```
bl-kernel/admin/themes/booty/index.php
bl-kernel/admin/themes/booty/init.php
bl-kernel/admin/themes/booty/login.php
bl-kernel/admin/themes/booty/css/
bl-kernel/admin/themes/booty/img/
```

### 7.2 兼容性 CSS 文件 (可选删除)

```bash
# 可选:删除兼容层 CSS
rm bl-kernel/admin/themes/tabler/css/bludit-tabler.css

# 如果删除,需要从 index.php 中移除引用
```

### 7.3 兼容性 JS 文件 (可选删除)

```bash
# 可选:删除兼容层 JS
rm bl-kernel/admin/themes/tabler/js/bludit-tabler.js

# 如果删除,需要从 index.php 中移除引用
```

---

## ⚙️ 八、jQuery 处理策略

### 8.1 保留 jQuery

**原因:**
- datetimepicker 插件依赖 jQuery
- Select2 插件依赖 jQuery
- 部分自定义代码使用 jQuery

**状态:** ✅ 保留 `Theme::jquery()` 调用

**文件继续使用 jQuery:**
```
bl-kernel/helpers/theme.class.php
bl-kernel/admin/themes/tabler/index.php
bl-kernel/admin/views/content.php
```

---

## 🔍 九、测试检查清单

### 9.1 基础功能测试

- [ ] 后台登录页面显示正常
- [ ] 仪表盘 (Dashboard) 加载正常
- [ ] 左侧导航菜单展开/收起正常
- [ ] 顶部用户下拉菜单正常

### 9.2 内容管理测试

- [ ] 新建内容页面正常
- [ ] 编辑内容页面正常
- [ ] Tab 切换功能正常
- [ ] 图片上传模态框正常
- [ ] 日期时间选择器正常
- [ ] 分类选择下拉框正常

### 9.3 设置页面测试

- [ ] 用户管理页面正常
- [ ] 主题设置页面正常
- [ ] 插件配置页面正常
- [ ] 系统设置各项正常

### 9.4 插件功能测试

- [ ] Simple Stats 插件图表显示正常
- [ ] 其他插件功能正常

---

## 📊 十、修改统计汇总

| 类别 | 文件数量 | 修改点数量 |
|------|---------|-----------|
| 核心 Bootstrap 文件 | 2 | 2 (替换) |
| Theme Helper | 1 | 0 (无需改) |
| Tabler init.php | 1 | 13 处 |
| Tabler index.php | 1 | 1 处 |
| 后台视图文件 | 25+ | 50+ 处 |
| 插件文件 | 1 | 2 处 |
| CSS 兼容文件 | 1 | 删除 |
| JS 兼容文件 | 1 | 删除 |
| Select2 配置 | 3 | 3 处 |
| **总计** | **36+** | **70+** |

---

## 🚀 十一、升级执行步骤

### 步骤 1: 备份当前系统
```bash
cp -r /www/wwwroot/maigewan /www/wwwroot/maigewan_backup_$(date +%Y%m%d)
```

### 步骤 2: 替换 Bootstrap 核心文件
```bash
cp tabler-temp/tabler-1.0.0-beta20/dist/libs/bootstrap/dist/css/bootstrap.min.css bl-kernel/css/
cp tabler-temp/tabler-1.0.0-beta20/dist/libs/bootstrap/dist/js/bootstrap.bundle.min.js bl-kernel/js/
```

### 步骤 3: 修改 Tabler init.php
- 替换 `data-dismiss` → `data-bs-dismiss`
- 替换 `form-group` → `mb-3`
- 替换 `custom-file*` → 原生 Bootstrap 5 样式
- 替换 `custom-select` → `form-select`

### 步骤 4: 批量修改视图文件
```bash
# data-toggle → data-bs-toggle
find bl-kernel/admin/views -name "*.php" -exec sed -i 's/data-toggle="/data-bs-toggle="/g' {} \;

# data-target → data-bs-target
find bl-kernel/admin/views -name "*.php" -exec sed -i 's/data-target="/data-bs-target="/g' {} \;

# data-dismiss → data-bs-dismiss
find bl-kernel/admin/views -name "*.php" -exec sed -i 's/data-dismiss="/data-bs-dismiss="/g' {} \;

# ml-/mr- → ms-/me-
find bl-kernel/admin/views -name "*.php" -exec sed -i 's/class="\([^"]*\)ml-/class="\1ms-/g' {} \;
find bl-kernel/admin/views -name "*.php" -exec sed -i 's/class="\([^"]*\)mr-/class="\1me-/g' {} \;
find bl-kernel/admin/views -name "*.php" -exec sed -i 's/class="\([^"]*\)pl-/class="\1ps-/g' {} \;
find bl-kernel/admin/views -name "*.php" -exec sed -i 's/class="\([^"]*\)pr-/class="\1pe-/g' {} \;
```

### 步骤 5: 修改插件文件
```bash
sed -i 's/data-toggle="/data-bs-toggle="/g' bl-plugins/simple-stats/plugin.php
```

### 步骤 6: 更新 Select2 配置
手动修改以下文件中的 `theme: "bootstrap4"` → `theme: "bootstrap-5"`:
- bl-kernel/admin/views/dashboard.php
- bl-kernel/admin/views/edit-content.php

### 步骤 7: 删除旧主题和兼容层
```bash
rm -rf bl-kernel/admin/themes/booty/
rm bl-kernel/admin/themes/tabler/css/bludit-tabler.css
rm bl-kernel/admin/themes/tabler/js/bludit-tabler.js

# 从 tabler/index.php 中移除这两个文件的引用
```

### 步骤 8: 清理缓存
```bash
rm -rf bl-content/tmp/*
```

### 步骤 9: 测试系统
按照"测试检查清单"逐项测试所有功能

---

## ⚠️ 十二、注意事项

1. **jQuery 保留**: 不要删除 jQuery,多个插件仍然依赖
2. **渐进式测试**: 每完成一个步骤就测试一次
3. **保留备份**: 至少保留 7 天的备份
4. **数据库备份**: 升级前备份数据库
5. **Select2 版本**: 确保 Select2 支持 Bootstrap 5
6. **浏览器缓存**: 升级后清除浏览器缓存测试

---

## 📚 十三、参考资源

- [Bootstrap 5 迁移指南](https://getbootstrap.com/docs/5.3/migration/)
- [Bootstrap 5.3 文档](https://getbootstrap.com/docs/5.3/)
- [Tabler 官方文档](https://tabler.io/docs/)
- [Select2 Bootstrap 5 主题](https://select2.org/appearance#bootstrap-5-theme)

---

## ✅ 十四、升级完成确认

升级完成后,确认以下几点:

- [ ] 所有页面无 JavaScript 错误
- [ ] 所有模态框正常打开/关闭
- [ ] 所有下拉菜单正常工作
- [ ] 所有表单正常提交
- [ ] 所有 Tab 切换正常
- [ ] 所有插件功能正常
- [ ] 移动端响应式正常
- [ ] 无 CSS 样式错位

---

**生成时间:** 2025年10月31日
**文档版本:** v1.0
**预计升级时间:** 2-4 小时
**风险等级:** 中等

