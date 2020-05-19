<?php

namespace common\components\own\generate;

/**
 * Генерация хешей, кодов, etc
 *
 * Class Generate
 * @package common\components\own\generate
 */
class Generate
{

	const DEFAULT_LENGTH = 12;

	/**
	 * Генерирует числовой код
	 * @param int $maxLength
	 * @return string
	 */
	public static function generateIntCode($maxLength = self::DEFAULT_LENGTH) {
		$chars="0123456789";
		$max = $maxLength;
		$size = mb_strlen($chars)-1;
		$code = "";
		while($max--) {
			$code .= $chars[rand(0,$size)];
		}
		return $code;
	}

	/**
	 * Генерирует буквеный в нижнем регистре
	 * @param int $maxLength
	 * @return string
	 */
	public static function generateLowerCharCode($maxLength = self::DEFAULT_LENGTH) {
		$chars="qwertyuiopasdfghjklzxcvbnm";
		$max = $maxLength;
		$size = mb_strlen($chars)-1;
		$code = "";
		while($max--) {
			$code .= $chars[rand(0,$size)];
		}
		return $code;
	}

	/**
	 * Генерирует буквеный код вверхнем регистре
	 * @param int $maxLength
	 * @return string
	 */
	public static function generateUpperCharCode($maxLength = self::DEFAULT_LENGTH) {
		$chars="QWERTYUIOPASDFGHJKLZXCVBNM";
		$max = $maxLength;
		$size = mb_strlen($chars)-1;
		$code = "";
		while($max--) {
			$code .= $chars[rand(0,$size)];
		}
		return $code;
	}

	/**
	 * Генерирует буквеный код разных регистров
	 * @param int $maxLength
	 * @return string
	 */
	public static function generateMixCharCode($maxLength = self::DEFAULT_LENGTH) {
		$chars="qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
		$max = $maxLength;
		$size = mb_strlen($chars)-1;
		$code = "";
		while($max--) {
			$code .= $chars[rand(0,$size)];
		}
		return $code;
	}

	/**
	 * Генериреут буквено-числовой код в разных регистров
	 * @param int $maxLength
	 * @return string
	 */
	public static function generateMixCode($maxLength = self::DEFAULT_LENGTH) {
		$chars="0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
		$max = $maxLength;
		$size = mb_strlen($chars)-1;
		$code = "";
		while($max--) {
			$code .= $chars[rand(0,$size)];
		}
		return $code;
	}

}