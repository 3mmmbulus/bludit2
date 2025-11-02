<?php defined('BLUDIT') or die('Bludit CMS.');

// SystemIntegrity check
SystemIntegrity::quickCheck();

// 使用全局语言对象
global $L;

// 获取当前语言代码（从 users.php 读取，由 init.php 设置）
// 如果 users.php 不存在，使用默认语言
$currentLang = 'zh_CN';
$usersFile = PATH_AUTHZ . 'users.php';
if (file_exists($usersFile) && is_readable($usersFile)) {
    $usersContent = file_get_contents($usersFile);
    $usersContent = str_replace("<?php defined('BLUDIT') or die('Bludit CMS.'); ?>", '', $usersContent);
    $usersData = json_decode(trim($usersContent), true);
    if (is_array($usersData) && isset($usersData['language'])) {
        $currentLang = $usersData['language'];
    }
}

// 创建页面语言对象（传入语言代码）
$pageL = new Language($currentLang);

// 手动加载页面专用翻译文件并合并
$pageTransFile = PATH_LANGUAGES . 'pages/authorization-settings/' . $currentLang . '.json';
if (file_exists($pageTransFile)) {
    $pageTransData = json_decode(file_get_contents($pageTransFile), true);
    if (is_array($pageTransData)) {
        // 将页面翻译合并到 pageL 对象
        foreach ($pageTransData as $key => $value) {
            $pageL->db[$key] = $value;
        }
    }
}

// Initialize variables
$message = '';
$messageType = '';
$serverIP = '';

/**
 * Get server IP address
 * Priority: External IP > Internal IP
 */
function getServerIP() {
    // Method 1: Try $_SERVER['SERVER_ADDR']
    if (!empty($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] !== '127.0.0.1') {
        return $_SERVER['SERVER_ADDR'];
    }
    
    // Method 2: Try gethostbyname
    $hostname = gethostname();
    if ($hostname !== false) {
        $ip = gethostbyname($hostname);
        if ($ip !== $hostname && filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }
    
    // Method 3: Try hostname -I command (Linux)
    if (function_exists('shell_exec')) {
        $output = shell_exec('hostname -I 2>/dev/null');
        if (!empty($output)) {
            $ips = preg_split('/\s+/', trim($output));
            foreach ($ips as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    return $ip;
                }
            }
        }
    }
    
    // Method 4: Try ip addr command (Linux)
    if (function_exists('shell_exec')) {
        $output = shell_exec("ip addr show 2>/dev/null | grep 'inet ' | grep -v '127.0.0.1' | awk '{print $2}' | cut -d/ -f1 | head -n1");
        if (!empty($output)) {
            $ip = trim($output);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    
    // Fallback
    return 'Unknown';
}

/**
 * Validate authorization credentials
 * This is a simple example - in production, this should connect to a remote
 * authorization server or use cryptographic validation
 */
function validateCredentials($username, $password, $licenseCode) {
    // Example validation logic
    // In production, implement proper validation:
    // 1. Connect to remote authorization API
    // 2. Verify against local encrypted database
    // 3. Use cryptographic signature validation
    
    // For now, just check that all fields are filled
    if (empty($username) || empty($password) || empty($licenseCode)) {
        return false;
    }
    
    // Simple validation: license code should be at least 16 characters
    if (strlen($licenseCode) < 16) {
        return false;
    }
    
    return true;
}

// Get server IP
$serverIP = getServerIP();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Note: This is a special case - we're creating the license file itself
    // So we check if user is admin instead of calling isAuthorized()
    // to avoid circular dependency
    
    if (!checkRole(['admin'], false)) {
        http_response_code(403);
        die('Forbidden');
    }
    
    // Get form data
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $licenseCode = isset($_POST['license_code']) ? trim($_POST['license_code']) : '';
    
    // Validate credentials
    if (validateCredentials($username, $password, $licenseCode)) {
        // Prepare license data
        $licenseData = [
            'server_ip' => $serverIP,
            'username' => $username,
            'license_code' => $licenseCode,
            'authorized_at' => date('Y-m-d H:i:s'),
            'status' => 'active',
            'expires_at' => date('Y-m-d H:i:s', strtotime('+1 year'))
        ];
        
        // Ensure directory exists
        if (!is_dir(PATH_AUTHZ)) {
            mkdir(PATH_AUTHZ, 0755, true);
        }
        
        // Write license file
        $licenseFile = PATH_AUTHZ . 'license.json';
        $result = file_put_contents(
            $licenseFile, 
            json_encode($licenseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
        
        if ($result !== false) {
            chmod($licenseFile, 0644);
            $message = $pageL->get('msg_success');
            $messageType = 'success';
        } else {
            $message = $pageL->get('msg_failed');
            $messageType = 'danger';
        }
    } else {
        $message = $pageL->get('msg_invalid_credentials');
        $messageType = 'danger';
    }
}

// Check if license exists
$licenseExists = is_readable(PATH_AUTHZ . 'license.json');
$licenseData = null;
if ($licenseExists) {
    $licenseContent = file_get_contents(PATH_AUTHZ . 'license.json');
    $licenseData = json_decode($licenseContent, true);
}

// Build page
$layout['title'] = $pageL->get('title');
$layout['view'] = 'authorization-settings';
