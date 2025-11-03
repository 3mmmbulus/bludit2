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
 * 判断是否是私网/回环/链路本地等特殊IP
 * @param string $ip IP地址
 * @return bool true=私网/特殊IP, false=公网IP
 */
function isPrivateOrSpecial(string $ip): bool {
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        if (preg_match('/^127\./', $ip)) return true;                    // loopback
        if (preg_match('/^(10\.|172\.(1[6-9]|2[0-9]|3[0-1])\.|192\.168\.)/', $ip)) return true; // RFC1918
        if (preg_match('/^169\.254\./', $ip)) return true;               // link-local
    } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        if ($ip === '::' || $ip === '::1') return true;                  // unspecified/loopback
        if (preg_match('/^(fe8|fe9|fea|feb)/i', str_replace(':','',$ip))) return true; // fe80::/10
        if (preg_match('/^ff/i', $ip)) return true;                      // multicast
        if (preg_match('/^f[c-d]/i', $ip)) return true;                  // unique local fc00::/7
    }
    return false;
}

/**
 * 出网 IP 探测：对外"发起连接"后，用 socket_getsockname 拿到本机使用的源地址
 * @param string $remote 远程地址
 * @param int $port 远程端口
 * @param int $flag FILTER_FLAG_IPV4 或 FILTER_FLAG_IPV6
 * @return string|null 本地源IP
 */
function getOutboundIP(string $remote, int $port, int $flag): ?string {
    $proto = strpos($remote, ':') !== false ? "udp://[$remote]:$port" : "udp://$remote:$port";
    $errno = 0; $errstr = '';
    $ctx = stream_context_create(['socket' => ['so_reuseport' => 1]]);
    $fp = @stream_socket_client($proto, $errno, $errstr, 2.0, STREAM_CLIENT_CONNECT, $ctx);
    if (!$fp) return null;
    $name = @stream_socket_get_name($fp, false);
    fclose($fp);
    if (!$name) return null;
    // $name 形如 "192.0.2.10:xxxxx" 或 "[2001:db8::1]:xxxxx"
    if (strpos($name, '[') === 0) {
        if (preg_match('/^\[([0-9a-f:]+)\]:(\d+)$/i', $name, $m)) $ip = $m[1]; else return null;
    } else {
        $parts = explode(':', $name);
        $ip = $parts[0] ?? null;
    }
    return $ip && filter_var($ip, FILTER_VALIDATE_IP, $flag) ? $ip : null;
}

/**
 * 公网出口 IP（通过外部API获取）
 * @return string|null 公网IP
 */
function getPublicEgressIP(): ?string {
    $endpoints = ['https://api.ipify.org', 'https://ifconfig.me/ip', 'https://checkip.amazonaws.com'];
    foreach ($endpoints as $u) {
        $ip = @trim(@file_get_contents($u));
        if ($ip && filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
    }
    if (function_exists('curl_init')) {
        foreach ($endpoints as $u) {
            $ch = curl_init($u);
            curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_CONNECTTIMEOUT=>2, CURLOPT_TIMEOUT=>4]);
            $ip = trim((string)curl_exec($ch));
            curl_close($ch);
            if ($ip && filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
        }
    }
    return null;
}

/**
 * 收集本地IP列表
 * 来源：出网探测 / Swoole / DNS / 服务器变量
 * @param bool $includeIPv6 是否包含IPv6
 * @param bool $includePrivate 是否包含私网IP
 * @return array IP地址数组
 */
function getLocalIPs(bool $includeIPv6 = true, bool $includePrivate = true): array {
    $set = [];

    // 1) 出网探测（常用于"当前对外联网用哪张网卡/地址"）
    $v4 = getOutboundIP('8.8.8.8', 53, FILTER_FLAG_IPV4);
    if ($v4) $set[$v4] = true;
    if ($includeIPv6) {
        $v6 = getOutboundIP('2001:4860:4860::8888', 53, FILTER_FLAG_IPV6);
        if ($v6) $set[$v6] = true;
    }

    // 2) Swoole（如果装了，能拿到所有 IPv4）
    if (function_exists('swoole_get_local_ip')) {
        foreach (swoole_get_local_ip() as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) $set[$ip] = true;
        }
    }

    // 3) DNS：主机名解析 A / AAAA
    $host = @gethostname();
    if ($host) {
        $v4s = @gethostbynamel($host);
        if (is_array($v4s)) foreach ($v4s as $ip) if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) $set[$ip] = true;
        if ($includeIPv6 && function_exists('dns_get_record')) {
            $recs = @dns_get_record($host, DNS_AAAA);
            if (is_array($recs)) foreach ($recs as $r) {
                if (!empty($r['ipv6']) && filter_var($r['ipv6'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) $set[$r['ipv6']] = true;
            }
        }
    }

    // 4) 服务器变量（部分 Web 环境可用）
    foreach (['SERVER_ADDR', 'LOCAL_ADDR'] as $k) {
        if (!empty($_SERVER[$k]) && filter_var($_SERVER[$k], FILTER_VALIDATE_IP)) $set[$_SERVER[$k]] = true;
    }

    // 过滤 & 排序（先 v4 再 v6）
    $ips = array_values(array_filter(array_keys($set), function($ip) use ($includePrivate) {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) return false;
        if (!$includePrivate && isPrivateOrSpecial($ip)) return false;
        return true;
    }));
    usort($ips, function ($a, $b) {
        $av6 = strpos($a, ':') !== false; $bv6 = strpos($b, ':') !== false;
        if ($av6 !== $bv6) return $av6 <=> $bv6; // false<true (v4优先)
        return strcmp($a, $b);
    });
    return $ips;
}

// ===== 主逻辑 =====
try {
    $localIpsAll = getLocalIPs(includeIPv6: true, includePrivate: true);
    $localIpsPublic = getLocalIPs(includeIPv6: true, includePrivate: false);
    $outboundV4 = getOutboundIP('8.8.8.8', 53, FILTER_FLAG_IPV4);
    $outboundV6 = getOutboundIP('2001:4860:4860::8888', 53, FILTER_FLAG_IPV6);
    $publicEgress = getPublicEgressIP();

    // 主IP选择优先级：outbound_ipv4 > public_egress_ip > local_ips_public[0] > Unknown
    $mainIP = $outboundV4 
        ?? $publicEgress 
        ?? ($localIpsPublic[0] ?? 'Unknown');

    echo json_encode([
        'status' => 'success',
        'ip' => $mainIP,
        'details' => [
            'local_ips_all' => $localIpsAll,
            'local_ips_public' => $localIpsPublic,
            'outbound_ipv4' => $outboundV4,
            'outbound_ipv6' => $outboundV6,
            'public_egress_ip' => $publicEgress,
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to detect server IP: ' . $e->getMessage(),
        'ip' => 'Unknown'
    ]);
}
