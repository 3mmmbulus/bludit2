# 主题颜色模式切换功能实现说明

## 修改摘要

实现了后台主题在浅色和深色模式切换时,左侧导航栏也会相应改变样式的功能。

## 修改文件

### 1. 新增文件

#### `/bl-kernel/admin/themes/tabler/js/theme-settings.js`
- 主题设置管理器
- 功能:
  - 读取和保存主题设置到 localStorage
  - 动态应用主题模式(浅色/深色)
  - 动态切换导航栏样式
  - 更新主题设置面板的单选按钮状态
  - 提供主题重置功能

#### `/bl-kernel/admin/themes/tabler/css/theme-settings.css`
- 主题样式定义
- 功能:
  - 浅色模式:导航栏透明背景,参考 Tabler 的 `layout-vertical-transparent.html`
  - 深色模式:保持原有深色背景 (#1d2535)
  - 平滑过渡动画
  - 响应式适配(移动端)

### 2. 修改文件

#### `/bl-kernel/admin/themes/tabler/index.php`
- 在 `<body>` 标签后立即加载主题脚本,防止页面闪烁
- 添加了 `theme-settings.css` 到 CSS 列表

#### `/bl-kernel/admin/themes/tabler/html/sidebar.php`
- 移除固定的 `data-bs-theme="dark"` 属性
- 添加初始类名 `navbar-light navbar-transparent`
- 让 JavaScript 动态控制主题属性

## 功能特点

### 浅色模式
- 导航栏:透明背景,边框分隔
- 链接:深色文字,hover 时主题色高亮
- 下拉菜单:白色背景,细边框
- 图标:深色,不透明度 0.7

### 深色模式
- 导航栏:深灰色背景 (#1d2535)
- 链接:浅色文字(rgba(255, 255, 255, 0.7))
- 下拉菜单:深灰色背景 (#2d3748)
- 图标:浅色,不透明度 0.5

### 切换机制
1. 用户在"主题设置"面板选择颜色模式
2. JavaScript 保存选择到 localStorage
3. 更新 `<html>` 标签的 `data-bs-theme` 属性
4. 更新导航栏的 `data-bs-theme` 属性和类名
5. CSS 根据属性应用对应样式

## 技术实现

### 存储键名
- `tabler-theme`: 主题模式(light/dark)
- `tabler-theme-primary`: 主题色(blue/azure/indigo等)
- `tabler-theme-radius`: 圆角大小(0/1/2)

### CSS 选择器
```css
/* 浅色模式 */
:root:not([data-bs-theme="dark"]) .navbar-vertical { ... }

/* 深色模式 */
[data-bs-theme="dark"] .navbar-vertical { ... }
```

### JavaScript API
```javascript
// 获取当前主题
window.themeSettings.getTheme()

// 设置主题
window.themeSettings.setTheme('dark')

// 重置为默认
window.themeSettings.reset()

// 重新应用主题
window.themeSettings.apply()
```

## 参考文件
- `/tabler-temp/tabler-material/html/dashboard/layout-vertical-transparent.html`
  - Tabler 官方浅色透明导航栏示例

## 兼容性
- 所有现代浏览器(支持 CSS 自定义属性和 localStorage)
- 移动端响应式支持
- 渐进式增强,不支持的浏览器会回退到默认样式

## 使用说明
1. 点击侧边栏底部的"主题设置"按钮
2. 在弹出面板中选择"浅色"或"深色"
3. 导航栏会立即切换样式
4. 设置会自动保存,下次访问时保持

## 注意事项
- 本次修改不涉及 SystemIntegrity 架构,无需调用相关方法
- 所有样式通过 CSS 类和属性控制,不直接修改 HTML 结构
- JavaScript 在页面加载早期执行,防止主题闪烁
- 移动端导航栏折叠时会显示背景色(避免透明看不清)
