<?php defined('BLUDIT') or die('Bludit CMS.');

header('Content-Type: application/json');

// Check if user is logged in
if (!$login->isLogged()) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized'
    ]);
    exit;
}

// Check if user is admin
if (!checkRole(['admin'], false)) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Forbidden'
    ]);
    exit;
}

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

// Get server IP
$serverIP = getServerIP();

// Return JSON response
echo json_encode([
    'status' => 'success',
    'ip' => $serverIP
]);
