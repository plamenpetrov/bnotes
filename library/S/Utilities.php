<?php

class S_Utilities
{
	
	public static function crypt($clear, $hash = null)
	{
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		
		if (null === $hash) {
			for ($salt = '', $x = 0; $x < 25; $x++) {
				$salt .= bin2hex(chr(mt_rand(0, 255)));
			}
		} else {
			$salt = substr($hash, 0, 25 * 2);
		}
		
		return $salt . hash('sha512', 'SOME VERY STRONG AND DIFFERENT STRING FOR SALT by Pafel' . $clear . $salt);
	}
	
	public static function utf8Encode($string, $encoding = false)
	{
	    if (!$encoding) {
	        $encoding = mb_detect_encoding($string);
	    }
	    if ($encoding && (strtoupper($encoding) != 'UTF-8')) {
	        $string = iconv(strtoupper($encoding), 'UTF-8//TRANSLIT', $string);
	    }
	    return $string;
	}
	
	public static function getCharsetFromContentType($string)
	{
	    $pattern = '/(?<=charset=)[^;]*/';
	    preg_match($pattern, $string, $matches);
	    if (!empty($matches)) {
	        return str_replace('"', '', $matches[0]);
	    }
	    return false;
	}
	
	public static function makeAbsoluteUrl($path, $base_url)
	{
		$host_url = $base_url;
		$abs_path = $path;
		
		// check if path is an absolute URL
		if (preg_match('/^[fhtps]+:\/\//', $path))
			return $path;
		
		// cut base_url to the last directory
		if (strrpos($base_url, '/') > 7) {
			$host_url = substr($base_url, 0, strpos($base_url, '/', 7));
			$base_url = substr($base_url, 0, strrpos($base_url, '/'));
		}
		
		// $path is absolute
		if ($path{0} == '/')
			$abs_path = $host_url . $path;
		else {
			// strip './' because its the same as ''
			$path = preg_replace('/^\.\//', '', $path);
			
			if (preg_match_all('/\.\.\//', $path, $matches, PREG_SET_ORDER))
				foreach ($matches as $a_match) {
					if (strrpos($base_url, '/'))
						$base_url = substr($base_url, 0, strrpos($base_url, '/'));
					
					$path = substr($path, 3);
				}
			
			$abs_path = $base_url . '/' . $path;
		}
		
		return $abs_path;
	}
	
	public static function sanitizeId($id)
	{
		// replace whitespaces with underscores
		$id = str_replace(' ', '_', $id);
		// replace dashes with underscores
		$id = str_replace('-', '_', $id);
		// replace dots with underscores
		$id = str_replace('.', '_', $id);
		// clean all unallowed characters
		$id = preg_replace('/[^a-zA-Z0-9_]/', '', $id);
		return $id;
	}
}
