# Pull Request: [功能简述]

## 📋 变更类型
请勾选适用项：
- [ ] 🐛 Bug 修复
- [ ] ✨ 新功能
- [ ] 🔒 安全加固
- [ ] 📝 文档更新
- [ ] 🎨 UI/样式改进
- [ ] ♻️ 代码重构
- [ ] ⚡ 性能优化
- [ ] 🧪 测试增强

---

## 📖 变更说明
<!-- 简要描述本次 PR 的目的和实现方式 -->

**解决的问题**:


**实现方案**:


**影响范围**:


---

## 🔐 SystemIntegrity 架构合规检查（必填）

> **⚠️ 违反任一硬约束 = 架构缺陷，PR 将被直接退回**

### ✅ 核心规则合规性

#### 1️⃣ 关键类构造函数接线
- [ ] **已验证**：所有新增/修改的关键类构造函数**首行**包含：
  ```php
  SystemIntegrity::quickCheck();
  ```
- [ ] **不适用**：本 PR 未涉及类构造函数

**涉及的类**（如适用）：
- [ ] `bl-kernel/*.class.php` - 核心类
- [ ] `bl-kernel/helpers/*.class.php` - 辅助类
- [ ] `bl-kernel/abstract/*.class.php` - 抽象类
- [ ] 继承自 `dbJSON`/`dbList`/`Plugin` 的类

---

#### 2️⃣ 敏感方法接线
- [ ] **已验证**：所有写入/授权敏感方法**首行**包含：
  ```php
  SystemIntegrity::isAuthorized();
  ```
- [ ] **不适用**：本 PR 未涉及敏感操作

**涉及的方法**（如适用）：
- [ ] 配置写入：`Site::set()`, `Users::set()`, `Security::set()` 等
- [ ] DB/存储写入：`dbJSON::save()`, `Pages::add()`, `Users::add()` 等
- [ ] 文件系统操作：
  - [ ] 上传：`ajax/upload-images.php`, `ajax/logo-upload.php`
  - [ ] 删除：`ajax/delete-image.php`, `Filesystem::rmfile()`
  - [ ] 创建：`Filesystem::mkdir()`
- [ ] 缓存/持久化：缓存清理、会话持久化
- [ ] 授权文件：读取/写入/校验 `PATH_AUTHZ.'license.json'`

**特殊处理说明**（如有循环依赖）：


---

#### 3️⃣ 关键路径登记
- [ ] **已验证**：所有新增文件已在 `bl-kernel/boot/init.php` 接线区登记
- [ ] **不适用**：本 PR 未新增关键文件

**登记的资源**（如适用）：
```php
// 示例（请替换为实际登记代码）
SystemIntegrity::registerCritical('resource_name', PATH_CONSTANT.'file.ext', [
    'type'     => 'file', // 'file' | 'dir'
    'required' => true,   // true（必需） | false（可选）
]);
```

**新增文件清单**：
| 资源名称 | 绝对路径 | 类型 | required | 说明 |
|---------|---------|------|----------|------|
| `example_resource` | `PATH_KERNEL.'example.php'` | file | true | 示例文件 |

---

#### 4️⃣ 未授权访问控制
- [ ] **已验证**：新增后台路由已添加到白名单（如需放行）
- [ ] **已验证**：路径匹配使用 `urldecode()` + `strtolower()`
- [ ] **已验证**：非白名单路由正确重定向到 `/admin/authorization-settings?reason=missing`
- [ ] **不适用**：本 PR 未涉及路由变更

**白名单路由**（如适用）：
```php
$whitelist = [
    '',                        // 空路径重定向
    'login',                   // 登录页
    'logout',                  // 登出
    'ajax',                    // AJAX 请求
    'authorization-settings',  // 授权页
    'new-user',                // 创建首个管理员
    'user-password',           // 修改密码
    // 新增路由（如有）：
];
```

---

#### 5️⃣ 禁止事项声明
- [ ] **已确认**：未修改 `SystemIntegrity` 类内部实现
- [ ] **已确认**：未移除或注释任何 `quickCheck()` / `isAuthorized()` 调用
- [ ] **已确认**：未将 `required=true` 的关键文件改为 `required=false`（除非有合理理由）
- [ ] **已确认**：未绕过检查机制（如直接操作文件系统）

**例外说明**（如有）：


---

### 🧪 功能测试

#### 基础功能测试
- [ ] 本地开发环境测试通过
- [ ] 未授权访问自动跳转到授权页
- [ ] 授权后功能正常执行
- [ ] 删除授权文件后，敏感操作正确拦截（返回 500 + "System Integrity Violation: LICENSE_MISSING"）

#### 边界场景测试
- [ ] 进程级缓存正确生效（通过日志/调试验证）
- [ ] 授权文件更新后立即生效（调用 `SystemIntegrity::clearCache()`）
- [ ] 多站点环境测试（如适用）
- [ ] PHP 不同版本兼容性（如适用）

---

### 📦 i18n 资源完整性

- [ ] **已验证**：新增页面包含完整翻译文件
  - [ ] `bl-languages/pages/[NAV_KEY]/en.json`
  - [ ] `bl-languages/pages/[NAV_KEY]/zh_CN.json`
- [ ] **已验证**：静态资源完整
  - [ ] `bl-kernel/css/[NAV_KEY].css`
  - [ ] `bl-kernel/js/[NAV_KEY].js`
- [ ] **已验证**：翻译键完整性（至少包含 `nav`, `title`）
- [ ] **不适用**：本 PR 未新增页面

**翻译文件清单**（如适用）：


---

## 📊 代码质量

- [ ] 代码符合项目编码规范（PSR-12 或团队约定）
- [ ] 已添加必要的注释和文档
- [ ] 已处理所有 TODO/FIXME 标记
- [ ] 无调试代码残留（`var_dump`, `print_r`, `console.log` 等）
- [ ] 敏感信息已移除（密码、密钥、内部路径等）

---

## 🔍 安全审查

- [ ] 用户输入已正确验证和清理
- [ ] SQL/JSON 注入防护到位
- [ ] 路径遍历漏洞已防御
- [ ] 文件上传安全检查（类型/大小/内容验证）
- [ ] 权限检查正确实现（`checkRole()` 或 `isAuthorized()`）
- [ ] 无硬编码凭证

---

## 📸 截图/录屏（可选）
<!-- 如有 UI 变更，请提供前后对比截图 -->


---

## 🔗 关联 Issue
<!-- 关闭 Issue：Closes #123 -->
<!-- 相关 Issue：Related to #456 -->


---

## ✅ 最终检查清单

提交前请确认：
- [ ] 所有 SystemIntegrity 硬约束已满足
- [ ] 所有测试用例通过
- [ ] 代码已自我审查
- [ ] 文档已更新（如适用）
- [ ] Changelog 已更新（如适用）
- [ ] 分支已从 `main` 最新代码 rebase

---

## 📝 备注
<!-- 其他需要说明的内容 -->


---

**审查者注意事项**：
1. 重点检查 SystemIntegrity 接线完整性
2. 验证敏感操作的授权检查
3. 确认路径登记与实际文件一致
4. 测试未授权访问拦截逻辑
5. 检查进程缓存清理时机

---

**提交者签名**：我已仔细阅读并确认满足所有架构约束。

Date: <!-- 自动填充 -->
