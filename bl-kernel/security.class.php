<?php defined('BLUDIT') or die('Bludit Badass CMS.');

class Security extends dbJSON
{
	protected $dbFields = array(
		'minutesBlocked'=>15,           // 默认封禁15分钟 (从5分钟增强)
		'numberFailuresAllowed'=>5,     // 允许5次失败 (从10次增强)
		'enableExponentialBackoff'=>true, // 启用指数退避
		'blackList'=>array()
	);

	function __construct()
	{
		parent::__construct(DB_SECURITY);
	}

	// ====================================================
	// TOKEN FOR CSRF
	// ====================================================

	// Generate and save the token in Session
	public function generateTokenCSRF()
	{
		$token = bin2hex( openssl_random_pseudo_bytes(64) );
		Session::set('tokenCSRF', $token);
		Log::set(__METHOD__.LOG_SEP.'New Token CSRF ['.$token.']');
	}

	// Validate the token
	public function validateTokenCSRF($token)
	{
		$sessionToken = $this->getTokenCSRF();
		return ( !empty($sessionToken) && ($sessionToken===$token) );
	}

	// Returns the token
	public function getTokenCSRF()
	{
		return Session::get('tokenCSRF');
	}

	// ====================================================
	// BRUTE FORCE PROTECTION
	// ====================================================

	public function isBlocked()
	{
		$ip = $this->getUserIp();

		if (!isset($this->db['blackList'][$ip])) {
			return false;
		}

		$currentTime = time();
		$userBlack = $this->db['blackList'][$ip];
		$numberFailures = $userBlack['numberFailures'];
		$lastFailure = $userBlack['lastFailure'];

		// 计算实际封禁时间 (使用指数退避)
		$blockDuration = $this->calculateBlockDuration($numberFailures);

		// Check if the IP is expired, then is not blocked
		if ($currentTime > $lastFailure + ($blockDuration * 60)) {
			return false;
		}

		// The IP has more failures than number of failures, then the IP is blocked
		if ($numberFailures >= $this->db['numberFailuresAllowed']) {
			Log::set(__METHOD__.LOG_SEP.'IP Blocked:'.$ip.' for '.$blockDuration.' minutes (failures: '.$numberFailures.')');
			return true;
		}

		// Otherwise the IP is not blocked
		return false;
	}

	/**
	 * 计算封禁时长 (指数退避算法)
	 * 1-2次失败: 1分钟
	 * 3-4次失败: 5分钟
	 * 5次失败: 15分钟
	 * 6次失败: 30分钟
	 * 7次及以上: 60分钟
	 */
	private function calculateBlockDuration($numberFailures)
	{
		// 如果未启用指数退避,使用默认封禁时间
		if (!isset($this->db['enableExponentialBackoff']) || !$this->db['enableExponentialBackoff']) {
			return $this->db['minutesBlocked'];
		}

		// 指数退避策略
		if ($numberFailures <= 2) {
			return 1;   // 1-2次失败: 1分钟
		} elseif ($numberFailures <= 4) {
			return 5;   // 3-4次失败: 5分钟
		} elseif ($numberFailures == 5) {
			return 15;  // 5次失败: 15分钟
		} elseif ($numberFailures == 6) {
			return 30;  // 6次失败: 30分钟
		} else {
			return 60;  // 7次及以上: 60分钟
		}
	}

	// Add or update the current client IP on the blacklist
	public function addToBlacklist()
	{
		$ip = $this->getUserIp();
		$currentTime = time();
		$numberFailures = 1;

		if (isset($this->db['blackList'][$ip])) {
			$userBlack = $this->db['blackList'][$ip];
			$lastFailure = $userBlack['lastFailure'];
			$previousFailures = $userBlack['numberFailures'];

			// 计算上次失败的封禁时长
			$blockDuration = $this->calculateBlockDuration($previousFailures);

			// Check if the IP is expired, then renew the number of failures
			if($currentTime <= $lastFailure + ($blockDuration * 60)) {
				$numberFailures = $previousFailures + 1;
			}
		}

		$this->db['blackList'][$ip] = array('lastFailure'=>$currentTime, 'numberFailures'=>$numberFailures);
		
		// 计算当前封禁时长用于日志
		$currentBlockDuration = $this->calculateBlockDuration($numberFailures);
		Log::set(__METHOD__.LOG_SEP.'Blacklist, IP:'.$ip.', Number of failures:'.$numberFailures.', Block duration:'.$currentBlockDuration.' minutes');
		
		return $this->save();
	}

	public function getNumberFailures($ip=null)
	{
		if(empty($ip)) {
			$ip = $this->getUserIp();
		}

		if(isset($this->db['blackList'][$ip])) {
			$userBlack = $this->db['blackList'][$ip];
			return $userBlack['numberFailures'];
		}
	}

	public function getUserIp()
	{
		return getenv('REMOTE_ADDR');
	}
}
