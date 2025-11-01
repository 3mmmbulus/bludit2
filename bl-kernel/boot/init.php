<?php defined('BLUDIT') or die('Bludit CMS.');
/**
 * SystemIntegrity（极简版，可扩展 / 定稿）
 *
 * 作用（不侵入）：
 * - 在“关键类构造函数”调用 quickCheck()：轻量检查登记的关键文件/目录是否存在、可读（可选：大小/mtime/哈希）。
 * - 在“关键方法开头”调用 isAuthorized()：当 require_license=true 时，要求授权文件存在且可读。
 * - 可选 verify()：比 quickCheck 更严格，可在安装/升级后或后台做一次“体检”。
 *
 * 设计原则：
 * - 默认无动作：只有主动调用本类方法才会检查，不改变你现有逻辑。
 * - 轻量：只做少量 IO 检查；带进程级软缓存，不影响性能。
 * - 易扩：以后只改这个类就能增强检查；外部调用点保持不变。
 *
 * 典型用法：
 *   // init.php 中（见第三节“初始化模板”）
 *   SystemIntegrity::registerCritical('init', __FILE__);
 *   SystemIntegrity::registerCritical('auth_users', PATH_AUTHZ.'users.php');
 *   SystemIntegrity::setPolicy(['require_license'=>true, 'license_file'=>PATH_AUTHZ.'license.json']);
 *   SystemIntegrity::quickCheck();
 *
 *   // 关键类构造函数
 *   SystemIntegrity::quickCheck();
 *
 *   // 关键方法开头（写配置/写库/保存授权）
 *   SystemIntegrity::isAuthorized();
 */
class SystemIntegrity
{
    /**
     * 关键路径注册表（随用随加）
     * 结构：
     * [
     *   '<name>' => [
     *     'path'     => '/abs/path',      // 绝对路径
     *     'required' => true,             // 是否必需（true 缺失即报错）
     *     'type'     => 'file'|'dir'|''   // 可选：指定类型；空字符串则不强制
     *     'algo'     => 'sha256',         // 可选：哈希算法
     *     'hash'     => '...',            // 可选：预期哈希
     *     'sizeMin'  => 0,                // 可选：最小字节
     *     'sizeMax'  => 0,                // 可选：最大字节
     *     'mtimeMin' => 0,                // 可选：最早 mtime (UNIX秒)
     *     'mtimeMax' => 0,                // 可选：最晚 mtime (UNIX秒)
     *   ],
     *   ...
     * ]
     * @var array
     */
    protected static $critical = [];

    /**
     * 运行策略（可 setPolicy 调整）
     * - cache_ttl：进程级软缓存秒数。注意：在 PHP-FPM 下，子进程会复用此静态属性；不同子进程互不影响。
     * - require_license：是否强制要求授权文件存在（true 时 quickCheck/isAuthorized 都会检查）。
     * - license_file：授权文件路径（默认 PATH_AUTHZ.'license.json'）。
     * - fail_http_500：违规时是否发送 500（默认 true），并输出简短错误码。
     * @var array
     */
    protected static $policy = [
        'cache_ttl'       => 60,
        'require_license' => false,
        'license_file'    => null,
        'fail_http_500'   => true,
    ];

    /** @var int|null 上次 quickCheck 通过的时间戳（进程级软缓存） */
    protected static $lastQuickOk = null;

    /** @var bool|null 系统是否已初始化的缓存（进程级静态缓存，只检测一次） */
    protected static $isInitialized = null;

    /** @var callable|null 日志回调：(string $level, string $message, array $context) => void */
    protected static $logger = null;

    /**
     * 惰性初始化：只负责补齐默认配置，不做任何检查。
     */
    protected static function boot()
    {
        if (self::$policy['license_file'] === null && defined('PATH_AUTHZ')) {
            self::$policy['license_file'] = rtrim(PATH_AUTHZ, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'license.json';
        }
    }

    // ========== 一、对外主方法（建议在关键处调用） ==========

    /**
     * 快速检查（建议放在“关键类构造函数”）
     * - 做已登记关键路径的存在性/可读性检查；
     * - 若 require_license=true，也检查授权文件存在可读；
     * - 命中“进程级软缓存”则直接返回（极低开销）。
     */
    public static function quickCheck()
    {
        self::boot();

        // 进程级软缓存：同一 FPM 子进程内，在 TTL 内直接通过
        if (self::$lastQuickOk !== null && (time() - self::$lastQuickOk) < (int)self::$policy['cache_ttl']) {
            return;
        }

        // 1) 关键路径轻量检查
        foreach (self::$critical as $name => $cfg) {
            $path     = isset($cfg['path']) ? (string)$cfg['path'] : '';
            $required = array_key_exists('required', $cfg) ? (bool)$cfg['required'] : true;

            if ($path === '') {
                self::reportViolation('CONFIG_BAD_ENTRY', ['name' => $name, 'reason' => 'path empty']);
                continue;
            }

            if (!$required) {
                // 非必需：若存在则需可读；不存在则跳过
                if (file_exists($path) && !is_readable($path)) {
                    self::reportViolation('NOT_READABLE', ['name' => $name, 'path' => $path]);
                }
                continue;
            }

            // 必需：必须存在且可读
            if (!file_exists($path)) {
                self::reportViolation('PATH_MISSING', ['name' => $name, 'path' => $path]);
            }
            if (!is_readable($path)) {
                self::reportViolation('NOT_READABLE', ['name' => $name, 'path' => $path]);
            }
        }

        // 2) 授权文件存在性（可选）
        if (!empty(self::$policy['require_license'])) {
            $lf = self::$policy['license_file'];
            if (empty($lf) || !is_readable($lf)) {
                self::reportViolation('LICENSE_MISSING', ['file' => (string)$lf]);
            }
        }

        // 通过：记录进程级时间戳
        self::$lastQuickOk = time();
    }

    /**
     * 授权检查（建议放在“关键方法开头”）
     * - 当 require_license=true：要求授权文件存在且可读；
     * - 未来若需要更严格的授权规则，可在此方法内扩展（例如读取 JSON 的到期时间/域名等）。
     */
    public static function isAuthorized()
    {
        self::boot();

        if (!empty(self::$policy['require_license'])) {
            $lf = self::$policy['license_file'];
            if (empty($lf) || !is_readable($lf)) {
                self::reportViolation('LICENSE_MISSING', ['file' => (string)$lf]);
            }
        }
    }

    /**
     * 完整检查（可在后台入口、安装/升级流程手动触发）
     * - 在 quickCheck 基础上，按每个已登记项的"可选规则"（大小/mtime/哈希）逐项校验。
     */
    public static function verify()
    {
        self::boot();
        clearstatcache(false);

        foreach (self::$critical as $name => $cfg) {
            self::checkOne($name, $cfg);
        }

        if (!empty(self::$policy['require_license'])) {
            $lf = self::$policy['license_file'];
            if (empty($lf) || !is_readable($lf)) {
                self::reportViolation('LICENSE_MISSING', ['file' => (string)$lf]);
            }
        }
    }

    /**
     * 检查系统是否已初始化（懒加载+缓存机制）
     * - 使用静态变量缓存，每个 PHP 进程只检测一次文件系统
     * - 性能开销：首次 ~0.05ms，后续 0ms（直接返回缓存值）
     * 
     * @return bool true=已初始化，false=需要初始化
     */
    public static function isSystemInitialized()
    {
        // 进程级静态缓存：只检测一次，结果永久缓存在当前进程
        if (self::$isInitialized !== null) {
            return self::$isInitialized;
        }

        self::boot();

        // 检查授权用户文件是否存在
        $usersFile = defined('PATH_AUTHZ') ? PATH_AUTHZ . 'users.php' : '';
        
        if (empty($usersFile)) {
            // PATH_AUTHZ 未定义，认为未初始化
            self::$isInitialized = false;
            return false;
        }

        // 检查文件是否存在且可读
        $exists = file_exists($usersFile) && is_readable($usersFile);
        
        // 可选：进一步验证文件有效性（文件大小 > 0）
        if ($exists) {
            $filesize = @filesize($usersFile);
            $exists = ($filesize !== false && $filesize > 0);
        }

        self::$isInitialized = $exists;

        return self::$isInitialized;
    }

    /**
     * 强制清除初始化状态缓存（用于测试或特殊场景）
     */
    public static function clearInitCache()
    {
        self::$isInitialized = null;
    }

    // ========== 二、对外辅助（注册/配置/日志） ==========

    /**
     * 注册一个关键路径（文件或目录）
     * @param string $name  唯一名
     * @param string $path  绝对路径
     * @param array  $opt   可选：required(bool)、type('file'|'dir'|'')、algo/hash、sizeMin/sizeMax、mtimeMin/mtimeMax
     */
    public static function registerCritical($name, $path, array $opt = [])
    {
        self::$critical[(string)$name] = array_merge([
            'path'     => (string)$path,
            'required' => true,
            'type'     => '',
        ], $opt);
    }

    /**
     * 批量注册关键路径
     * @param array $items 形如 [['name'=>'n1','path'=>'/abs/p1', 'required'=>true], ...]
     */
    public static function registerCriticalMany(array $items)
    {
        foreach ($items as $it) {
            if (!is_array($it)) continue;
            $name = isset($it['name']) ? $it['name'] : '';
            $path = isset($it['path']) ? $it['path'] : '';
            if ($name !== '' && $path !== '') {
                $opt = $it; unset($opt['name'], $opt['path']);
                self::registerCritical($name, $path, $opt);
            }
        }
    }

    /**
     * 为已注册项设置哈希（按需逐步加固）
     * @param string $name
     * @param string $algo sha1/sha256/md5 等
     * @param string $hash 预期哈希
     */
    public static function setChecksum($name, $algo, $hash)
    {
        if (isset(self::$critical[$name])) {
            self::$critical[$name]['algo'] = (string)$algo;
            self::$critical[$name]['hash'] = (string)$hash;
        }
    }

    /**
     * 设置运行策略（cache_ttl / require_license / license_file / fail_http_500）
     */
    public static function setPolicy(array $policy)
    {
        self::$policy = array_replace(self::$policy, $policy);
    }

    /**
     * 设置日志回调（可选）
     * @param callable $logger (string $level, string $message, array $context) => void
     */
    public static function setLogger($logger)
    {
        if (is_callable($logger)) {
            self::$logger = $logger;
        }
    }

    // ========== 三、内部实现（尽量简单直观） ==========

    /**
     * 对单个关键项做严格检查（verify 用；如果配置了哈希/大小/mtime，就会逐项检查）
     */
    protected static function checkOne($name, array $cfg)
    {
        $path     = isset($cfg['path']) ? (string)$cfg['path'] : '';
        $required = array_key_exists('required', $cfg) ? (bool)$cfg['required'] : true;
        $typeHint = isset($cfg['type']) ? (string)$cfg['type'] : '';

        if ($path === '') {
            self::reportViolation('CONFIG_BAD_ENTRY', ['name' => $name, 'reason' => 'path empty']);
            return;
        }

        // 存在性 & 可读性
        $exists = file_exists($path);
        if ($required && !$exists) {
            self::reportViolation('PATH_MISSING', ['name' => $name, 'path' => $path]);
        }
        if ($exists && !is_readable($path)) {
            self::reportViolation('NOT_READABLE', ['name' => $name, 'path' => $path]);
        }
        if (!$exists) return; // 非必需或缺失但不强制时，结束

        // 类型判断（可选）
        $isFile = is_file($path);
        $isDir  = is_dir($path);
        if ($typeHint === 'file' && !$isFile) {
            self::reportViolation('TYPE_MISMATCH', ['name' => $name, 'expect' => 'file', 'path' => $path]);
        } elseif ($typeHint === 'dir' && !$isDir) {
            self::reportViolation('TYPE_MISMATCH', ['name' => $name, 'expect' => 'dir', 'path' => $path]);
        }

        if ($isFile) {
            // 可选：大小区间
            if (isset($cfg['sizeMin']) || isset($cfg['sizeMax'])) {
                $sz = @filesize($path);
                if ($sz === false) {
                    self::reportViolation('SIZE_UNREADABLE', ['name' => $name, 'path' => $path]);
                } else {
                    if (isset($cfg['sizeMin']) && $sz < (int)$cfg['sizeMin']) {
                        self::reportViolation('SIZE_TOO_SMALL', ['name' => $name, 'size' => $sz]);
                    }
                    if (isset($cfg['sizeMax']) && $sz > (int)$cfg['sizeMax']) {
                        self::reportViolation('SIZE_TOO_LARGE', ['name' => $name, 'size' => $sz]);
                    }
                }
            }

            // 可选：mtime 区间
            if (isset($cfg['mtimeMin']) || isset($cfg['mtimeMax'])) {
                $mt = @filemtime($path);
                if ($mt === false) {
                    self::reportViolation('MTIME_UNREADABLE', ['name' => $name, 'path' => $path]);
                } else {
                    if (isset($cfg['mtimeMin']) && $mt < (int)$cfg['mtimeMin']) {
                        self::reportViolation('MTIME_TOO_OLD', ['name' => $name, 'mtime' => $mt]);
                    }
                    if (isset($cfg['mtimeMax']) && $mt > (int)$cfg['mtimeMax']) {
                        self::reportViolation('MTIME_TOO_NEW', ['name' => $name, 'mtime' => $mt]);
                    }
                }
            }

            // 可选：哈希校验
            if (!empty($cfg['algo']) && !empty($cfg['hash'])) {
                $algo   = (string)$cfg['algo'];
                $expect = (string)$cfg['hash'];
                $actual = @hash_file($algo, $path);
                if ($actual === false || $actual === '' || !self::hashEquals($expect, $actual)) {
                    self::reportViolation('HASH_MISMATCH', ['name' => $name, 'algo' => $algo]);
                }
            }
        }
        // 目录目前只做存在/可读/类型判断；需要更强可后续自行扩展
    }

    /**
     * 统一违规处理：写日志 + 返回 500（可配置）+ 输出简短错误码
     * @param string $code 错误码
     * @param array  $ctx  上下文
     * @param bool   $exit 是否立即退出（默认 true；满足“删除关键文件必须报错”）
     */
    protected static function reportViolation($code, array $ctx = [], $exit = true)
    {
        $message = "[SystemIntegrity] {$code}";
        if (self::$logger) {
            call_user_func(self::$logger, 'error', $message, $ctx);
        } elseif (class_exists('Log')) {
            Log::set('SystemIntegrity', 'violation', json_encode(['code' => $code, 'ctx' => $ctx]));
        } else {
            @error_log($message . ' ' . json_encode($ctx));
        }

        if ($exit) {
            if (!headers_sent() && !empty(self::$policy['fail_http_500'])) {
                http_response_code(500);
                header('Content-Type: text/plain; charset=UTF-8');
            }
            echo "System Integrity Violation: {$code}\n";
            exit(1);
        }
    }

    /**
     * 安全的哈希字符串比对（兼容旧版 PHP；若有 hash_equals 则使用之）
     */
    protected static function hashEquals($a, $b)
    {
        if (function_exists('hash_equals')) {
            return hash_equals((string)$a, (string)$b);
        }
        $a = (string)$a; $b = (string)$b;
        if (strlen($a) !== strlen($b)) return false;
        $res = 0;
        for ($i = 0, $n = strlen($a); $i < $n; $i++) {
            $res |= ord($a[$i]) ^ ord($b[$i]);
        }
        return $res === 0;
    }
}
// Bludit version
define('BLUDIT_VERSION',        '3.16.2');
define('BLUDIT_CODENAME',       'Valencia');
define('BLUDIT_RELEASE_DATE',   '2024-08-23');
define('BLUDIT_BUILD',          '20240806');

// Change to TRUE for debugging
define('DEBUG_MODE', TRUE);
define('DEBUG_TYPE', 'INFO'); // INFO, TRACE

//  This determines whether errors should be printed to the screen as part of the output or if they should be hidden from the user.
ini_set("display_errors", 0);

// Even when display_errors is on, errors that occur during PHP's startup sequence are not displayed.
// It's strongly recommended to keep display_startup_errors off, except for debugging.
ini_set('display_startup_errors', 0);

//  If disabled, error message will be solely plain text instead HTML code.
ini_set("html_errors", 0);

// Tells whether script error messages should be logged to the server's error log or error_log.
ini_set('log_errors', 1);

if (DEBUG_MODE) {
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
} else {
	error_reporting(E_ERROR);
}

// PHP paths
// PATH_ROOT and PATH_BOOT are defined in index.php
define('PATH_LANGUAGES',		PATH_ROOT . 'bl-languages' . DS);
define('PATH_THEMES',			PATH_ROOT . 'bl-themes' . DS);
define('PATH_PLUGINS',			PATH_ROOT . 'bl-plugins' . DS);
define('PATH_KERNEL',			PATH_ROOT . 'bl-kernel' . DS);
define('PATH_CONTENT',			PATH_ROOT . 'bl-content' . DS);

define('PATH_ABSTRACT',			PATH_KERNEL . 'abstract' . DS);
define('PATH_RULES',			PATH_KERNEL . 'boot' . DS . 'rules' . DS);
define('PATH_HELPERS',			PATH_KERNEL . 'helpers' . DS);
define('PATH_AJAX',			PATH_KERNEL . 'ajax' . DS);
define('PATH_CORE_JS',			PATH_KERNEL . 'js' . DS);

define('PATH_PAGES',			PATH_CONTENT . 'pages' . DS);
define('PATH_DATABASES',		PATH_CONTENT . 'databases' . DS);
define('PATH_PLUGINS_DATABASES',	PATH_CONTENT . 'databases' . DS . 'plugins' . DS);
define('PATH_TMP',			PATH_CONTENT . 'tmp' . DS);
define('PATH_UPLOADS',			PATH_CONTENT . 'uploads' . DS);
define('PATH_WORKSPACES',		PATH_CONTENT . 'workspaces' . DS);

define('PATH_UPLOADS_PAGES',		PATH_UPLOADS . 'pages' . DS);
define('PATH_UPLOADS_PROFILES',		PATH_UPLOADS . 'profiles' . DS);
define('PATH_UPLOADS_THUMBNAILS',	PATH_UPLOADS . 'thumbnails' . DS);
define('PATH_AUTHZ',			PATH_KERNEL . '_maigewan' . DS . 'authz' . DS);

define('PATH_ADMIN',			PATH_KERNEL . 'admin' . DS);
define('PATH_ADMIN_THEMES',		PATH_ADMIN . 'themes' . DS);
define('PATH_ADMIN_CONTROLLERS',	PATH_ADMIN . 'controllers' . DS);
define('PATH_ADMIN_VIEWS',		PATH_ADMIN . 'views' . DS);

define('DEBUG_FILE',			PATH_CONTENT . 'debug.txt');

// PAGES DATABASE
define('DB_PAGES', PATH_DATABASES . 'pages.php');
define('DB_SITE', PATH_DATABASES . 'site.php');
define('DB_CATEGORIES', PATH_DATABASES . 'categories.php');
define('DB_TAGS', PATH_DATABASES . 'tags.php');
define('DB_SYSLOG', PATH_DATABASES . 'syslog.php');
define('DB_USERS', PATH_AUTHZ . 'users.php'); // 共享用户库
define('DB_SECURITY', PATH_DATABASES . 'security.php');

// User environment variables
include(PATH_KERNEL . 'boot' . DS . 'variables.php');

// Set internal character encoding
mb_internal_encoding(CHARSET);

// Set HTTP output character encoding
mb_http_output(CHARSET);

// Inclde Abstract Classes
include(PATH_ABSTRACT . 'dbjson.class.php');
include(PATH_ABSTRACT . 'dblist.class.php');
include(PATH_ABSTRACT . 'plugin.class.php');

// Inclde Classes
include(PATH_KERNEL . 'pages.class.php');
include(PATH_KERNEL . 'users.class.php');
include(PATH_KERNEL . 'tags.class.php');
include(PATH_KERNEL . 'language.class.php');
include(PATH_KERNEL . 'site.class.php');
include(PATH_KERNEL . 'categories.class.php');
include(PATH_KERNEL . 'syslog.class.php');
include(PATH_KERNEL . 'pagex.class.php');
include(PATH_KERNEL . 'category.class.php');
include(PATH_KERNEL . 'tag.class.php');
include(PATH_KERNEL . 'user.class.php');
include(PATH_KERNEL . 'url.class.php');
include(PATH_KERNEL . 'login.class.php');
include(PATH_KERNEL . 'parsedown.class.php');
include(PATH_KERNEL . 'security.class.php');

// Include functions
include(PATH_KERNEL . 'functions.php');

// Include Helpers Classes
include(PATH_HELPERS . 'text.class.php');
include(PATH_HELPERS . 'log.class.php');
include(PATH_HELPERS . 'date.class.php');
include(PATH_HELPERS . 'theme.class.php');
include(PATH_HELPERS . 'session.class.php');
include(PATH_HELPERS . 'redirect.class.php');
include(PATH_HELPERS . 'sanitize.class.php');
include(PATH_HELPERS . 'valid.class.php');
include(PATH_HELPERS . 'email.class.php');
include(PATH_HELPERS . 'filesystem.class.php');
include(PATH_HELPERS . 'alert.class.php');
include(PATH_HELPERS . 'paginator.class.php');
include(PATH_HELPERS . 'image.class.php');
include(PATH_HELPERS . 'tcp.class.php');
include(PATH_HELPERS . 'dom.class.php');
include(PATH_HELPERS . 'cookie.class.php');
include(PATH_HELPERS . 'password.class.php');

if (file_exists(PATH_KERNEL . 'bludit.pro.php')) {
	include(PATH_KERNEL . 'bludit.pro.php');
}




// === SystemIntegrity 接线区（init.php） ===

// 1) 登记关键文件/目录（不要登记 license.json 为 required）
SystemIntegrity::registerCritical('init', __FILE__, ['type' => 'file']);
SystemIntegrity::registerCritical('authz_dir', rtrim(PATH_AUTHZ, DS), ['required' => true, 'type' => 'dir']);
// authz_users 设为非必需，因为首次访问时可能不存在（需要通过 system-init 初始化）
SystemIntegrity::registerCritical('authz_users', PATH_AUTHZ.'users.php', ['required' => false, 'type' => 'file']);
SystemIntegrity::registerCritical('authz_license', PATH_AUTHZ.'license.json', ['required' => false, 'type' => 'file']);
SystemIntegrity::registerCritical('sites_root', PATH_ROOT.'sites', ['required' => true, 'type' => 'dir']);

// 安全增强类文件登记
SystemIntegrity::registerCritical('helper_password', PATH_HELPERS.'password.class.php', ['required' => true, 'type' => 'file']);
SystemIntegrity::registerCritical('helper_cookie', PATH_HELPERS.'cookie.class.php', ['required' => true, 'type' => 'file']);
SystemIntegrity::registerCritical('helper_session', PATH_HELPERS.'session.class.php', ['required' => true, 'type' => 'file']);

// 导航页面关键路径登记
$navPages = [
    'dashboard-home', 'sites-overview', 'site-new', 'seo-settings', 'content-management',
    'media-images', 'brand-logo', 'ads-settings', 'spider-logs', 'spider-settings',
    'plugins', 'themes', 'cache-settings', 'cache-list', 'security-system',
    'security-general', 'audit-logs', 'system-repair-upgrade', 'authorization-settings',
    'profile', 'about-maigewan', 'site-bootstrap', 'system-init'
];

foreach ($navPages as $navKey) {
    SystemIntegrity::registerCritical("lang_pages_{$navKey}_dir", PATH_LANGUAGES."pages/{$navKey}", ['required' => true, 'type' => 'dir']);
    SystemIntegrity::registerCritical("lang_pages_{$navKey}_en", PATH_LANGUAGES."pages/{$navKey}/en.json", ['required' => true, 'type' => 'file']);
    SystemIntegrity::registerCritical("lang_pages_{$navKey}_zh", PATH_LANGUAGES."pages/{$navKey}/zh_CN.json", ['required' => true, 'type' => 'file']);
    
    // site-bootstrap 不需要 CSS/JS 文件
    if ($navKey !== 'site-bootstrap') {
        SystemIntegrity::registerCritical("css_{$navKey}", PATH_KERNEL."css/{$navKey}.css", ['required' => true, 'type' => 'file']);
        SystemIntegrity::registerCritical("js_{$navKey}", PATH_CORE_JS."{$navKey}.js", ['required' => true, 'type' => 'file']);
    }
}

// 2) 策略（初始化阶段不强制授权文件）
SystemIntegrity::setPolicy([
  'require_license' => false,                       // ★ 这里关闭全局授权强制
  'license_file'    => PATH_AUTHZ.'license.json',   // 仍然保留路径，后面要用
  'cache_ttl'       => 60,
  'fail_http_500'   => true,
]);

// 3) 早期做一次轻量检查（不会触发授权拦截）
SystemIntegrity::quickCheck();

// === License Bootstrap Guard（未授权时的后台白名单） ===
$licenseFile  = PATH_AUTHZ . 'license.json';
$reqPath      = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$adminPrefix  = '/' . trim(ADMIN_URI_FILTER, '/') . '/';          // 例如 /admin/
$isAdminReq   = (strpos($reqPath, $adminPrefix) === 0);

// 初始化阶段 license.json 可能不存在：只放行白名单后台路由
if (!is_readable($licenseFile) && $isAdminReq) {

    // 取出 admin 前缀后的第一个段作为控制器名（如 'dashboard', 'users', 'authorization-settings'）
    $after     = substr($reqPath, strlen($adminPrefix));
    $firstSeg  = strtolower(trim(strtok($after, '/')));

    // ★ 白名单（按你的需求可增减）
    $whitelist = [
        '',                     // 空路径（/admin/ 默认跳转到授权页）
        'login',                // 登录页
        'logout',               // 允许退出
        'authorization-settings',// 授权页（配置/上传 license.json）
        'new-user',             // 创建首个管理员（如有）
        'user-password',        // 设置/修改密码
    ];

    if (!in_array($firstSeg, $whitelist, true)) {
        // 友好跳转到授权页，并带上 reason=missing，让授权页显示品牌提示
        // 直接构建路径，不依赖 HTML_PATH_ADMIN_ROOT（此时尚未定义）
        $dest = $adminPrefix . 'authorization-settings?reason=missing';

        if (!headers_sent()) {
            header('Cache-Control: no-store');
            header('Location: ' . $dest, true, 302);
            exit;
        } else {
            echo '<meta http-equiv="refresh" content="0;url=' . htmlspecialchars($dest, ENT_QUOTES, 'UTF-8') . '">';
            exit;
        }
    }
    
    // 如果访问的是空路径（/admin/），也重定向到授权页
    if ($firstSeg === '') {
        $dest = $adminPrefix . 'authorization-settings?reason=missing';
        if (!headers_sent()) {
            header('Cache-Control: no-store');
            header('Location: ' . $dest, true, 302);
            exit;
        } else {
            echo '<meta http-equiv="refresh" content="0;url=' . htmlspecialchars($dest, ENT_QUOTES, 'UTF-8') . '">';
            exit;
        }
    }
}
// 在 renderMenu($menu, $plugins) 调用之前或内部：
// 未授权就只保留登录、授权管理相关菜单
$licenseMissing = !is_readable(PATH_AUTHZ.'license.json');
if ($licenseMissing) {
    // 保留“授权管理/登录/关于/注销”等；你可以按需裁剪 $menu
    // 简单做法：在遍历时跳过非白名单（和上面的 $whitelist 对齐）
}

// Objects
$pages 		= new Pages();
$users 		= new Users();
$tags 		= new Tags();
$categories = new Categories();
$site  		= new Site();
$url		= new Url();
$security	= new Security();
$syslog 	= new Syslog();

// --- Relative paths ---
// This paths are relative for the user / web browsing.

// Base URL
// The user can define the base URL.
// Left empty if you want to Bludit try to detect the base URL.
$base = '';

if (!empty($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['SCRIPT_NAME']) && empty($base)) {
	$base = str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_NAME']);
	$base = dirname($base);
} elseif (empty($base)) {
	$base = empty($_SERVER['SCRIPT_NAME']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	$base = dirname($base);
}

if (strpos($_SERVER['REQUEST_URI'], $base) !== 0) {
	$base = '/';
} elseif ($base != DS) {
	$base = trim($base, '/');
	$base = '/' . $base . '/';
} else {
	// Workaround for Windows Web Servers
	$base = '/';
}

define('HTML_PATH_ROOT', 		$base);
define('HTML_PATH_THEMES',		HTML_PATH_ROOT . 'bl-themes/');
define('HTML_PATH_THEME',		HTML_PATH_THEMES . $site->theme() . '/');
define('HTML_PATH_THEME_CSS',		HTML_PATH_THEME . 'css/');
define('HTML_PATH_THEME_JS',		HTML_PATH_THEME . 'js/');
define('HTML_PATH_THEME_IMG',		HTML_PATH_THEME . 'img/');
define('HTML_PATH_ADMIN_ROOT',		HTML_PATH_ROOT . ADMIN_URI_FILTER . '/');
define('HTML_PATH_ADMIN_THEME',		HTML_PATH_ROOT . 'bl-kernel/admin/themes/' . $site->adminTheme() . '/');
define('HTML_PATH_ADMIN_THEME_JS',	HTML_PATH_ADMIN_THEME . 'js/');
define('HTML_PATH_ADMIN_THEME_CSS',	HTML_PATH_ADMIN_THEME . 'css/');
define('HTML_PATH_CORE_JS',		HTML_PATH_ROOT . 'bl-kernel/js/');
define('HTML_PATH_CORE_CSS',		HTML_PATH_ROOT . 'bl-kernel/css/');
define('HTML_PATH_CORE_IMG',		HTML_PATH_ROOT . 'bl-kernel/img/');
define('HTML_PATH_CONTENT',		HTML_PATH_ROOT . 'bl-content/');
define('HTML_PATH_UPLOADS',		HTML_PATH_ROOT . 'bl-content/uploads/');
define('HTML_PATH_UPLOADS_PAGES',	HTML_PATH_UPLOADS . 'pages/');
define('HTML_PATH_UPLOADS_PROFILES',	HTML_PATH_UPLOADS . 'profiles/');
define('HTML_PATH_UPLOADS_THUMBNAILS',	HTML_PATH_UPLOADS . 'thumbnails/');
define('HTML_PATH_PLUGINS',		HTML_PATH_ROOT . 'bl-plugins/');

// --- Objects with dependency ---
$language = new Language($site->language());
$url->checkFilters($site->uriFilters());

// --- CONSTANTS with dependency ---

// Tag URI filter
define('TAG_URI_FILTER', $url->filters('tag'));

// Category URI filter
define('CATEGORY_URI_FILTER', $url->filters('category'));

// Page URI filter
define('PAGE_URI_FILTER', $url->filters('page'));

// Content order by: date / position
define('ORDER_BY', $site->orderBy());

// Allow unicode characters in the URL
define('EXTREME_FRIENDLY_URL', $site->extremeFriendly());

// Minutes to execute the autosave function
define('AUTOSAVE_INTERVAL', $site->autosaveInterval());

// TRUE for upload images restric to a pages, FALSE to upload images in common
define('IMAGE_RESTRICT', $site->imageRestrict());

// TRUE to convert relatives images to absoultes, FALSE No changes apply
define('IMAGE_RELATIVE_TO_ABSOLUTE', $site->imageRelativeToAbsolute());

// TRUE if the markdown parser is enabled
define('MARKDOWN_PARSER', $site->markdownParser());

// --- PHP paths with dependency ---
// This paths are absolutes for the OS
define('THEME_DIR',			PATH_ROOT . 'bl-themes' . DS . $site->theme() . DS);
define('THEME_DIR_PHP',			THEME_DIR . 'php' . DS);
define('THEME_DIR_CSS',			THEME_DIR . 'css' . DS);
define('THEME_DIR_JS',			THEME_DIR . 'js' . DS);
define('THEME_DIR_IMG',			THEME_DIR . 'img' . DS);
define('THEME_DIR_LANG',		THEME_DIR . 'languages' . DS);

// --- Absolute paths with domain ---
// This paths are absolutes for the user / web browsing.
define('DOMAIN',			$site->domain());
define('DOMAIN_BASE',			DOMAIN . HTML_PATH_ROOT);
define('DOMAIN_CORE_JS',		DOMAIN . HTML_PATH_CORE_JS);
define('DOMAIN_CORE_CSS',		DOMAIN . HTML_PATH_CORE_CSS);
define('DOMAIN_THEME',			DOMAIN . HTML_PATH_THEME);
define('DOMAIN_THEME_CSS',		DOMAIN . HTML_PATH_THEME_CSS);
define('DOMAIN_THEME_JS',		DOMAIN . HTML_PATH_THEME_JS);
define('DOMAIN_THEME_IMG',		DOMAIN . HTML_PATH_THEME_IMG);
define('DOMAIN_ADMIN_THEME',		DOMAIN . HTML_PATH_ADMIN_THEME);
define('DOMAIN_ADMIN_THEME_CSS',	DOMAIN . HTML_PATH_ADMIN_THEME_CSS);
define('DOMAIN_ADMIN_THEME_JS',		DOMAIN . HTML_PATH_ADMIN_THEME_JS);
define('DOMAIN_UPLOADS',		DOMAIN . HTML_PATH_UPLOADS);
define('DOMAIN_UPLOADS_PAGES',		DOMAIN . HTML_PATH_UPLOADS_PAGES);
define('DOMAIN_UPLOADS_PROFILES',	DOMAIN . HTML_PATH_UPLOADS_PROFILES);
define('DOMAIN_UPLOADS_THUMBNAILS',	DOMAIN . HTML_PATH_UPLOADS_THUMBNAILS);
define('DOMAIN_PLUGINS',		DOMAIN . HTML_PATH_PLUGINS);
define('DOMAIN_CONTENT',		DOMAIN . HTML_PATH_CONTENT);

define('DOMAIN_ADMIN',			DOMAIN_BASE . ADMIN_URI_FILTER . '/');

define('DOMAIN_TAGS',			Text::addSlashes(DOMAIN_BASE . TAG_URI_FILTER, false, true));
define('DOMAIN_CATEGORIES',		Text::addSlashes(DOMAIN_BASE . CATEGORY_URI_FILTER, false, true));
define('DOMAIN_PAGES',			Text::addSlashes(DOMAIN_BASE . PAGE_URI_FILTER, false, true));

$ADMIN_CONTROLLER = '';
$ADMIN_VIEW = '';
$ID_EXECUTION = uniqid(); // string 13 characters long
$WHERE_AM_I = $url->whereAmI();

// --- Objects shortcuts ---
$L = $language;

// DEBUG: Print constants
// $arr = array_filter(get_defined_constants(), 'is_string');
// echo json_encode($arr);
// exit;
