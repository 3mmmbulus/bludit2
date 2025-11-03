<?php defined('BLUDIT') or die('Bludit CMS.');

// 授权设置页面特殊处理：临时禁用授权文件强制检查
// 因为这个页面就是用来管理授权的，删除授权时不应该被强制退出
SystemIntegrity::setPolicy(['require_license' => false]);

// SystemIntegrity check - 符合架构规范要求
// 注意: 授权页面是特殊情况,quickCheck() 会执行但 isAuthorized() 在 POST 时需要特殊处理
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
 * Validate authorization code via remote API
 * 
 * @param string $code License code
 * @param string $ip Server IP address
 * @param string $username Username (optional if email provided)
 * @param string $email Email (optional if username provided)
 * @return array Response array with 'success' and other fields
 */
function validateLicenseViaAPI($code, $ip, $username = '', $email = '') {
    $apiUrl = 'https://api.maigewan.com/api/validate-auth-code';
    
    // Prepare request data
    $requestData = [
        'code' => $code,
        'ip' => $ip
    ];
    
    // Add username or email (at least one required)
    if (!empty($username)) {
        $requestData['username'] = $username;
    }
    if (!empty($email)) {
        $requestData['email'] = $email;
    }
    
    // Initialize cURL
    $ch = curl_init($apiUrl);
    if ($ch === false) {
        return [
            'success' => false,
            'code' => 'CURL_INIT_FAILED',
            'errno' => 9997,
            'message' => 'Failed to initialize cURL'
        ];
    }
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    
    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    // Check for network errors
    if ($response === false || !empty($curlError)) {
        return [
            'success' => false,
            'code' => 'NETWORK_ERROR',
            'errno' => 9999,
            'message' => 'Network error: ' . $curlError
        ];
    }
    
    // Parse JSON response
    $data = json_decode($response, true);
    if (!is_array($data)) {
        return [
            'success' => false,
            'code' => 'INVALID_RESPONSE',
            'errno' => 9998,
            'message' => 'Invalid API response'
        ];
    }
    
    // Add HTTP code for reference
    $data['http_code'] = $httpCode;
    
    return $data;
}

/**
 * Map API error code to localized message key
 * 
 * @param string $errorCode API error code
 * @return string Translation key
 */
function getErrorMessageKey($errorCode) {
    $errorMap = [
        'MISSING_PARAMS' => 'error_missing_params',
        'USER_NOT_FOUND' => 'error_user_not_found',
        'CODE_NOT_FOUND' => 'error_code_not_found',
        'CODE_NOT_OWNED_BY_USER' => 'error_code_not_owned',
        'CODE_BANNED' => 'error_code_banned',
        'CODE_EXPIRED' => 'error_code_expired',
        'IP_MISMATCH' => 'error_ip_mismatch',
        'IP_OCCUPIED' => 'error_ip_occupied',
        'UPDATE_FAILED' => 'error_update_failed',
        'SERVER_ERROR' => 'error_server_error',
        'NETWORK_ERROR' => 'error_network',
        'CURL_INIT_FAILED' => 'error_network',
        'INVALID_RESPONSE' => 'error_server_error'
    ];
    
    return isset($errorMap[$errorCode]) ? $errorMap[$errorCode] : 'error_unknown';
}

// Get server IP
$serverIP = getServerIP();

// Initialize form values (for preserving input after failed submission)
$formUserIdentity = '';
$formLicenseCode = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 特殊处理: 授权页面创建授权文件时不能调用 isAuthorized()
    // 因为这会导致循环依赖,所以只检查管理员权限
    // 参考: SYSTEMINTEGRITY_REQUIREMENT_TEMPLATE.md 2.2.E 特殊处理
    
    if (!checkRole(['admin'], false)) {
        http_response_code(403);
        die('Forbidden');
    }
    
    // Handle license removal (change license action)
    if (isset($_POST['remove_license']) && $_POST['remove_license'] == '1') {
        $licenseFile = PATH_AUTHZ . 'license.json';
        if (file_exists($licenseFile)) {
            if (unlink($licenseFile)) {
                Log::set('Authorization - License removed successfully');
                // 清除缓存，让系统立即识别授权状态变化
                SystemIntegrity::clearCache();
            } else {
                Log::set('Authorization - Failed to remove license file');
            }
        }
        // Redirect to refresh page and show license entry form
        Redirect::page('authorization-settings');
        exit;
    }
    
    // Get form data
    $userIdentity = isset($_POST['user_identity']) ? trim($_POST['user_identity']) : '';
    $licenseCode = isset($_POST['license_code']) ? trim($_POST['license_code']) : '';

    // 保存表单值用于回显（只在验证失败时需要）
    $formUserIdentity = $userIdentity;
    $formLicenseCode = $licenseCode;
    
    // Debug: 记录接收到的POST数据
    Log::set('Authorization POST data - user_identity: [' . $userIdentity . '], license_code: [' . $licenseCode . ']');
    Log::set('Authorization POST raw - user_identity exists: ' . (isset($_POST['user_identity']) ? 'yes' : 'no') . ', license_code exists: ' . (isset($_POST['license_code']) ? 'yes' : 'no'));
    
    // Validate inputs
    if (empty($userIdentity) || empty($licenseCode)) {
        Log::set('Authorization validation failed - userIdentity empty: ' . (empty($userIdentity) ? 'yes' : 'no') . ', licenseCode empty: ' . (empty($licenseCode) ? 'yes' : 'no'));
        $message = $pageL->get('error_missing_params');
        $messageType = 'danger';
    } else {
        // 判断是邮箱还是用户名 (通过 @ 符号)
        $username = '';
        $email = '';
        if (strpos($userIdentity, '@') !== false) {
            $email = $userIdentity;
        } else {
            $username = $userIdentity;
        }
        
        // Call remote API to validate
        $apiResponse = validateLicenseViaAPI($licenseCode, $serverIP, $username, $email);
        
        if (isset($apiResponse['success']) && $apiResponse['success'] === true) {
            // Success - extract data from API response
            $user = isset($apiResponse['user']) ? $apiResponse['user'] : [];
            $license = isset($apiResponse['license']) ? $apiResponse['license'] : [];
            
            // Prepare license data for local storage
            $licenseData = [
                'server_ip' => $serverIP,
                'username' => isset($user['username']) ? $user['username'] : $username,
                'email' => isset($user['email']) ? $user['email'] : $email,
                'user_id' => isset($user['id']) ? $user['id'] : '',
                'license_code' => $licenseCode,
                'license_id' => isset($license['id']) ? $license['id'] : '',
                'status' => isset($license['status']) ? $license['status'] : 'active',
                'authorized_at' => isset($license['firstAuthorizedAt']) ? $license['firstAuthorizedAt'] : date('c'),
                'expires_at' => isset($license['expiresAt']) ? $license['expiresAt'] : date('c', strtotime('+1 year')),
                'bound_ip' => isset($license['macBound']) ? $license['macBound'] : $serverIP,
                'check_count' => isset($license['checkCount']) ? $license['checkCount'] : 0,
                'fail_count' => isset($license['failCount']) ? $license['failCount'] : 0,
                'last_checked' => date('c'),
                'server_time' => isset($apiResponse['server_time']) ? $apiResponse['server_time'] : date('c')
            ];
            
            // Ensure directory exists
            if (!is_dir(PATH_AUTHZ)) {
                mkdir(PATH_AUTHZ, 0755, true);
            }
            
            // Write license file
            $licenseFile = PATH_AUTHZ . 'license.json';
            $result = file_put_contents(
                $licenseFile, 
                json_encode($licenseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
            
            if ($result !== false) {
                chmod($licenseFile, 0644);
                
                // 清除 SystemIntegrity 进程级缓存，使授权立即生效
                SystemIntegrity::clearCache();
                
                // 如果配置了 require_license，现在可以启用了
                SystemIntegrity::setPolicy(['require_license' => true]);
                
                $message = $pageL->get('msg_success');
                $messageType = 'success';
                
                // 成功后清空表单值（防止重复提交）
                $formUserIdentity = '';
                $formLicenseCode = '';
            } else {
                $message = $pageL->get('error_update_failed');
                $messageType = 'danger';
            }
        } else {
            // API validation failed - map error code to message
            $errorCode = isset($apiResponse['code']) ? $apiResponse['code'] : 'UNKNOWN';
            
            // 隐藏敏感错误：USER_NOT_FOUND 和 CODE_NOT_FOUND
            // 这些错误会暴露系统中存在哪些用户/授权码，存在安全风险
            if ($errorCode === 'USER_NOT_FOUND' || $errorCode === 'CODE_NOT_FOUND') {
                // 使用通用错误提示，不暴露具体是用户不存在还是授权码不存在
                $message = $pageL->get('error_auth_failed');
                Log::set('Authorization failed - ' . $errorCode . ' (hidden from user for security)');
            } else {
                // 其他错误正常显示
                $messageKey = getErrorMessageKey($errorCode);
                $message = $pageL->get($messageKey);
                
                // Add errno for debugging if available
                if (isset($apiResponse['errno'])) {
                    $message .= ' (' . $pageL->get('error_code_label') . ': ' . $apiResponse['errno'] . ')';
                }
            }
            
            $messageType = 'danger';
        }
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
