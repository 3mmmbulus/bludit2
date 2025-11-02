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
    
    $username = isset($args['username']) ? trim($args['username']) : '';
    $password = isset($args['password']) ? $args['password'] : '';
    $confirmPassword = isset($args['confirm_password']) ? $args['confirm_password'] : '';
    
    // 验证用户名
    if (empty($username)) {
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
    
    // 确定初始语言（单一真源：users.php 顶层）
    // 优先级：POST['language'] > GET['language'] > 默认 zh_CN
    $initLang = 'zh_CN'; // 默认中文
    
    // 检查 POST 数据（表单提交时）
    if (isset($args['language'])) {
        $requestedLang = Sanitize::html($args['language']);
        // 验证语言文件是否存在
        if (file_exists(PATH_LANGUAGES . $requestedLang . '.json')) {
            $initLang = $requestedLang;
        }
    }
    // 检查 GET 参数（URL 切换时）
    elseif (isset($_GET['language'])) {
        $requestedLang = Sanitize::html($_GET['language']);
        // 验证语言文件是否存在
        if (file_exists(PATH_LANGUAGES . $requestedLang . '.json')) {
            $initLang = $requestedLang;
        }
    }
    
    // 构建用户数据（顶层添加 language 键）
    $userData = [
        'language' => $initLang,  // ★ 全局语言设置（单一真源）
        $username => [
            'nickname'      => ucfirst($username),
            'firstName'     => '',
            'lastName'      => '',
            'role'          => 'admin',
            'password'      => $passwordHash,
            'salt'          => $salt,
            'email'         => '',
            'registered'    => Date::current(DB_DATE_FORMAT),
            'tokenRemember' => '',
            'tokenAuth'     => $tokenAuth,
            'tokenAuthTTL'  => '2009-03-15 14:00',
            'twitter'       => '',
            'facebook'      => '',
            'instagram'     => '',
            'codepen'       => '',
            'linkedin'      => '',
            'xing'          => '',
            'telegram'      => '',
            'github'        => '',
            'gitlab'        => '',
            'mastodon'      => '',
            'vk'            => '',
            'description'   => ''
        ]
    ];    // 写入文件
    $content = "<?php defined('BLUDIT') or die('Bludit CMS.'); ?>\n";
    $content .= json_encode($userData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    // 使用 LOCK_EX 防止并发写入
    $bytesWritten = file_put_contents($usersFile, $content, LOCK_EX);
    
    if ($bytesWritten === false) {
        Log::set(__METHOD__ . LOG_SEP . 'Failed to create users file', LOG_TYPE_ERROR);
        Alert::set($pageL->get('create_failed') . ' (Path: ' . $usersFile . ')', ALERT_STATUS_FAIL);
        return false;
    }
    
    // 验证文件确实被创建
    if (!file_exists($usersFile)) {
        Log::set(__METHOD__ . LOG_SEP . 'Users file not found after creation: ' . $usersFile, LOG_TYPE_ERROR);
        Alert::set('File creation verification failed!', ALERT_STATUS_FAIL);
        return false;
    }
    
    // 设置文件权限
    chmod($usersFile, 0644);
    
    Log::set(__METHOD__ . LOG_SEP . 'Users file created successfully: ' . $usersFile . ' (' . $bytesWritten . ' bytes)', LOG_TYPE_INFO);
    
    // ✅ 清除初始化状态缓存，确保系统能识别到新创建的 users.php
    SystemIntegrity::clearInitCache();
    
    // 记录日志
    Log::set(__METHOD__ . LOG_SEP . 'System initialized with admin user: ' . $username);
    
    // 成功提示
    Alert::set($pageL->get('init_success'), ALERT_STATUS_OK);
    
    // 重定向到登录页
    Redirect::page('login');
    
    return true;
}

// ============================================================================
// Main before POST
// ============================================================================

// 检查是否已经初始化
if (file_exists(PATH_AUTHZ . 'users.php')) {
    // 已初始化，重定向到登录页
    Redirect::page('login');
    exit;
}

// ============================================================================
// POST Method
// ============================================================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 系统初始化不需要 CSRF token 验证（首次访问）
    initializeSystem($_POST);
}

// ============================================================================
// Main after POST
// ============================================================================

// 获取可用的语言列表（动态扫描 bl-languages 目录）
$availableLanguages = [];
if (isset($Language)) {
    $availableLanguages = $Language->getLanguageList();
} else {
    // 如果 Language 对象不存在，手动扫描
    $langFiles = Filesystem::listFiles(PATH_LANGUAGES, '*', 'json');
    foreach ($langFiles as $file) {
        $locale = basename($file, '.json');
        $langData = json_decode(file_get_contents($file), true);
        if (isset($langData['language-data']['native'])) {
            $availableLanguages[$locale] = $langData['language-data']['native'];
        }
    }
}

// 将语言列表传递给模板
$layout['availableLanguages'] = $availableLanguages;

// Build page
$layout['title'] = $pageL->get('title');
$layout['view'] = 'system-init';
$layout['template'] = 'system-init.php'; // 使用独立模板
