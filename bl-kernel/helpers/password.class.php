<?php defined('BLUDIT') or die('Bludit CMS.');

/**
 * Password Helper Class
 * Provides secure password hashing and verification with automatic migration from legacy SHA1
 */
class Password {

	/**
	 * Hash a password using modern bcrypt algorithm
	 * 
	 * @param string $password Plain text password
	 * @return string Hashed password
	 */
	public static function hash($password)
	{
		return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
	}

	/**
	 * Verify password and automatically upgrade from legacy SHA1 to bcrypt
	 * 
	 * @param string $password Plain text password to verify
	 * @param string $hash Stored password hash
	 * @param string $salt Legacy salt (for SHA1 compatibility)
	 * @param string $username Username for auto-upgrade
	 * @return bool True if password matches
	 */
	public static function verify($password, $hash, $salt = '', $username = null)
	{
		// Try modern bcrypt verification first
		if (password_verify($password, $hash)) {
			// Check if rehashing is needed (algorithm changed or cost increased)
			if (password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 10])) {
				self::autoUpgrade($password, $username);
			}
			return true;
		}

		// Legacy SHA1 verification (for backward compatibility)
		if (!empty($salt) && sha1($password . $salt) === $hash) {
			// Password is correct but using old SHA1, auto-upgrade to bcrypt
			self::autoUpgrade($password, $username);
			return true;
		}

		return false;
	}

	/**
	 * Automatically upgrade user password to modern hash
	 * 
	 * @param string $password Plain text password
	 * @param string $username Username to update
	 */
	private static function autoUpgrade($password, $username)
	{
		if (empty($username)) {
			return;
		}

		global $users;
		if (isset($users) && method_exists($users, 'setPassword')) {
			$users->setPassword([
				'username' => $username,
				'password' => $password
			]);
			Log::set(__METHOD__ . LOG_SEP . 'Auto-upgraded password hash for user: ' . $username);
		}
	}

	/**
	 * Check if a hash is using legacy SHA1 format
	 * 
	 * @param string $hash Password hash to check
	 * @return bool True if legacy SHA1 format
	 */
	public static function isLegacyHash($hash)
	{
		// SHA1 produces 40 character hex string
		return strlen($hash) === 40 && ctype_xdigit($hash);
	}
}
