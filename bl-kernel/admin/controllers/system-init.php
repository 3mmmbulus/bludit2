<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// System Initialization - First Time Setup
// ============================================================================

// 使用全局Language对象
global $L, $site, $Language;

// 检查URL参数中是否有语言切换请求
if (isset($_GET['language']) && isset($site)) {
    $requestedLang = Sanitize::html($_GET['language']);
    // 验证语言文件是否存在
    if (file_exists(PATH_LANGUAGES . $requestedLang . '.json')) {
        // 更新site语言设置（临时，不保存）
        $site->set(array('language' => $requestedLang));
        // 重新加载全局Language对象
        $Language = new Language($requestedLang);
        $L = $Language;
    }
}

// Load page-specific i18n using current language
$currentLang = isset($site) ? $site->language() : 'zh_CN';
$pageL = new Language($currentLang);

// 加载page-specific翻译文件
$pageTransFile = PATH_LANGUAGES . 'pages/system-init/' . $currentLang . '.json';
if (file_exists($pageTransFile)) {
    $pageTransData = json_decode(file_get_contents($pageTransFile), true);
    if (is_array($pageTransData)) {
        // 将page translations合并到pageL对象
        foreach ($pageTransData as $key => $value) {
            $pageL->db[$key] = $value;
        }
    }
}

$message = '';
$messageType = '';

// ============================================================================
// Functions
// ============================================================================

function initializeSystem($args)
{
    // ⚠️ 注意：系统初始化阶段不调用 isAuthorized()
    // 因为此时 users.php 还不存在，无法进行授权验证
    // 这是一个特殊的引导页面，仅在首次安装时可访问
    
    global $pageL;
    
    // 🔍 调试：函数开始
    $debugLog = PATH_ROOT . 'system-init-debug.log';
    file_put_contents($debugLog, date('Y-m-d H:i:s') . " - initializeSystem started\n", FILE_APPEND);
    
    error_log('[System-Init] initializeSystem started');
    
    $username = isset($args['username']) ? trim($args['username']) : '';
    $password = isset($args['password']) ? $args['password'] : '';
    $confirmPassword = isset($args['confirm_password']) ? $args['confirm_password'] : '';
    
    file_put_contents($debugLog, "Username: '$username', Password length: " . strlen($password) . "\n", FILE_APPEND);
    
    error_log('[System-Init] Username: ' . $username . ', Password length: ' . strlen($password));
    
    // 验证用户名
    if (empty($username)) {
        file_put_contents($debugLog, "FAIL: username_required\n", FILE_APPEND);
        error_log('[System-Init] Validation failed: username_required');
        Alert::set($pageL->get('username_required'), ALERT_STATUS_FAIL);
        return false;
    }
    
    // 用户名只能包含字母、数字、下划线和连字符
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
        Alert::set($pageL->get('username_invalid'), ALERT_STATUS_FAIL);
        return false;
    }
    
    // 用户名长度限制
    if (strlen($username) < 3 || strlen($username) > 20) {
        Alert::set($pageL->get('username_length'), ALERT_STATUS_FAIL);
        return false;
    }
    
    // 验证密码长度
    if (strlen($password) < 8) {
        Alert::set($pageL->get('password_too_short'), ALERT_STATUS_FAIL);
        return false;
    }
    
    // 验证密码强度（至少包含字母和数字）
    if (!preg_match('/[a-zA-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        Alert::set($pageL->get('password_weak'), ALERT_STATUS_FAIL);
        return false;
    }
    
    // 验证密码确认
    if ($password !== $confirmPassword) {
        Alert::set($pageL->get('password_mismatch'), ALERT_STATUS_FAIL);
        return false;
    }
    
    // 创建用户数据库文件
    $usersFile = PATH_AUTHZ . 'users.php';
    
    // 确保目录存在
    if (!is_dir(PATH_AUTHZ)) {
        mkdir(PATH_AUTHZ, 0755, true);
    }
    
    // 生成安全的密码哈希
    $salt = Text::randomText(16);
    $passwordHash = sha1($password . $salt); // 使用现有方式保持兼容
    
    // 生成认证令牌
    $tokenAuth = bin2hex(openssl_random_pseudo_bytes(32));
    
    // 构建用户数据
    $userData = [
        $username => [
            'nickname' => ucfirst($username),
            'firstName' => '',
            'lastName' => '',
            'role' => 'admin',
            'password' => $passwordHash,
            'salt' => $salt,
            'email' => '', // 系统初始化不收集邮箱
            'registered' => date('Y-m-d H:i:s'),
            'tokenRemember' => '',
            'tokenAuth' => $tokenAuth,
            'tokenAuthTTL' => '2009-03-15 14:00',
            'twitter' => '',
            'facebook' => '',
            'instagram' => '',
            'codepen' => '',
            'linkedin' => '',
            'xing' => '',
            'telegram' => '',
            'github' => '',
            'gitlab' => '',
            'mastodon' => '',
            'vk' => ''
        ]
    ];
    
    // 写入文件
    $content = "<?php defined('BLUDIT') or die('Bludit CMS.'); ?>\n";
    $content .= json_encode($userData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    $debugLog = PATH_ROOT . 'system-init-debug.log';
    file_put_contents($debugLog, "Attempting to write file: $usersFile\n", FILE_APPEND);
    file_put_contents($debugLog, "Content length: " . strlen($content) . " bytes\n", FILE_APPEND);
    
    error_log('[System-Init] Attempting to write file: ' . $usersFile);
    error_log('[System-Init] Content length: ' . strlen($content) . ' bytes');
    
    // 使用 LOCK_EX 防止并发写入
    $bytesWritten = file_put_contents($usersFile, $content, LOCK_EX);
    
    file_put_contents($debugLog, "Bytes written: " . ($bytesWritten === false ? 'FAILED' : $bytesWritten) . "\n", FILE_APPEND);
    
    error_log('[System-Init] Bytes written: ' . ($bytesWritten === false ? 'FAILED' : $bytesWritten));
    
    if ($bytesWritten === false) {
        $error = error_get_last();
        file_put_contents($debugLog, "Write error: " . json_encode($error) . "\n", FILE_APPEND);
        error_log('[System-Init] Write error: ' . json_encode($error));
        Log::set(__METHOD__ . LOG_SEP . 'Failed to create users file: ' . ($error['message'] ?? 'Unknown error'), LOG_TYPE_ERROR);
        Alert::set($pageL->get('create_failed') . ' (Path: ' . $usersFile . ')', ALERT_STATUS_FAIL);
        return false;
    }
    
    // 验证文件确实被创建
    if (!file_exists($usersFile)) {
        $debugLog = PATH_ROOT . 'system-init-debug.log';
        file_put_contents($debugLog, "FAIL: File verification - file not found after creation\n", FILE_APPEND);
        error_log('[System-Init] File verification failed - file not found after creation');
        Log::set(__METHOD__ . LOG_SEP . 'Users file not found after creation: ' . $usersFile, LOG_TYPE_ERROR);
        Alert::set('File creation verification failed!', ALERT_STATUS_FAIL);
        return false;
    }
    
    $debugLog = PATH_ROOT . 'system-init-debug.log';
    file_put_contents($debugLog, "SUCCESS: File verified\n", FILE_APPEND);
    file_put_contents($debugLog, "About to redirect to login\n", FILE_APPEND);
    
    error_log('[System-Init] File verified successfully');
    
    // 设置文件权限
    chmod($usersFile, 0644);
    
    Log::set(__METHOD__ . LOG_SEP . 'Users file created successfully: ' . $usersFile . ' (' . $bytesWritten . ' bytes)', LOG_TYPE_INFO);
    
    // ✅ 清除初始化状态缓存，确保系统能识别到新创建的 users.php
    SystemIntegrity::clearInitCache();
    
    error_log('[System-Init] Cache cleared, preparing redirect');
    
    // 记录日志
    Log::set(__METHOD__ . LOG_SEP . 'System initialized with admin user: ' . $username);
    
    // 成功提示
    Alert::set($pageL->get('init_success'), ALERT_STATUS_OK);
    
    // 重定向到登录页
    error_log('[System-Init] Redirecting to login page');
    file_put_contents($debugLog, "Calling Redirect::page('login')\n", FILE_APPEND);
    Redirect::page('login');
    
    return true;
}

// ============================================================================
// Main before POST
// ============================================================================

// 🔍 调试：记录每次请求
$debugLog = PATH_ROOT . 'system-init-debug.log';
$debugMsg = "\n" . date('Y-m-d H:i:s') . " - Controller loaded\n";
$debugMsg .= "Request Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
$debugMsg .= "users.php exists: " . (file_exists(PATH_AUTHZ . 'users.php') ? 'YES' : 'NO') . "\n";
file_put_contents($debugLog, $debugMsg, FILE_APPEND);

// 检查是否已经初始化
if (file_exists(PATH_AUTHZ . 'users.php')) {
    // 已初始化，重定向到登录页
    file_put_contents($debugLog, "REDIRECT: users.php exists, redirecting to login\n", FILE_APPEND);
    Redirect::page('login');
    exit;
}

file_put_contents($debugLog, "PASS: users.php does not exist, continuing...\n", FILE_APPEND);

// ============================================================================
// POST Method
// ============================================================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 系统初始化不需要 CSRF token 验证（首次访问）
    
    // 🔍 调试：记录 POST 请求
    $debugLog = PATH_ROOT . 'system-init-debug.log';
    $debugMsg = date('Y-m-d H:i:s') . " - POST request received\n";
    $debugMsg .= "POST data: " . json_encode($_POST) . "\n";
    $debugMsg .= "POST keys: " . implode(', ', array_keys($_POST)) . "\n";
    $debugMsg .= "POST count: " . count($_POST) . "\n";
    $debugMsg .= "REQUEST data: " . json_encode($_REQUEST) . "\n";
    $debugMsg .= "php://input: " . file_get_contents('php://input') . "\n";
    file_put_contents($debugLog, $debugMsg, FILE_APPEND);
    
    error_log('[System-Init] POST request received');
    error_log('[System-Init] POST data: ' . json_encode($_POST));
    
    $result = initializeSystem($_POST);
    
    // 🔍 调试：记录结果
    $debugMsg = date('Y-m-d H:i:s') . " - Result: " . ($result ? 'true' : 'false') . "\n";
    file_put_contents($debugLog, $debugMsg, FILE_APPEND);
    
    error_log('[System-Init] initializeSystem result: ' . ($result ? 'true' : 'false'));
}

// ============================================================================
// Main after POST
// ============================================================================

// Build page
$layout['title'] = $pageL->get('title');
$layout['view'] = 'system-init';
$layout['template'] = 'system-init.php'; // 使用独立模板
