<?php defined('BLUDIT') or die('Bludit CMS.');

class Cookie {

	public static function get($key)
	{
		if (isset($_COOKIE[$key])) {
			return $_COOKIE[$key];
		}
		return false;
	}

	public static function set($key, $value, $daysToExpire=30)
	{
		// The time the cookie expires.
		// This is a Unix timestamp so is in number of seconds since the epoch.
		// In other words, you'll most likely set this with the time() function plus the number of seconds before you want it to expire.
		// Or you might use mktime(). time()+60*60*24*30 will set the cookie to expire in 30 days.
		// If set to 0, or omitted, the cookie will expire at the end of the session (when the browser closes).
		$expire = time()+60*60*24*$daysToExpire;
		
		// Security-enhanced cookie settings (PHP 7.3+)
		setcookie($key, $value, [
			'expires' => $expire,
			'path' => '/',
			'domain' => '',
			'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
			'httponly' => true,
			'samesite' => 'Lax'  // Lax allows safe cross-site requests (e.g., links)
		]);
	}
	
	public static function remove($key)
	{
		unset($_COOKIE[$key]);
		setcookie($key, '', [
			'expires' => time()-3600,
			'path' => '/',
			'domain' => '',
			'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
			'httponly' => true,
			'samesite' => 'Lax'
		]);
	}

	public static function isEmpty($key)
	{
		return empty($_COOKIE[$key]);
	}
}