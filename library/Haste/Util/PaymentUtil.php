<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\Haste\Util;


use HeimrichHannot\Haste\Exception\InputRangeException;
use HeimrichHannot\Haste\Exception\InvalidArgumentException;

class PaymentUtil
{
	const MANDATE_REFERENCE_ALLOWED_SPECIAL_CHARACTERS = "+?/-:().,'";

	/**
	 * Generate a SEPA mandate reference
	 *
	 * @param string $prefix Will be prepended to the mandate reference. Counted into length.
	 * @param string $suffix Will be appended to the mandate reference. Counted into length.
	 * @param int $length Max length is 35. Higher length will throw InputRangeException. Length shorted or same as count of prefix and suffix will throw InputRangeException.
	 * @param bool $useSpecialCharacters Activate usage of special characters for reference generation. Default: false
	 * @param bool $useLetters Activate usage of letters (A-Z) for reference generation. Default: true
	 * @param bool $useNumbers Activate usage of numbers (0-9) for reference generation. Default: true
	 * @return string An uppercase SEPA mandate reference of given length.
	 * @throws InputRangeException Thrown if length is to long or to short and if all character types are set to false.
	 * @throws InvalidArgumentException Will be throws if called with wrong file types.
	 */
	public static function generateSEPAMandateReference($prefix = '', $suffix = '', $length = 35, $useSpecialCharacters = false, $useLetters = true, $useNumbers = true)
	{
		if (!is_string($prefix) || !is_string($suffix) || !is_int($length))
		{
			throw new InvalidArgumentException();
		}
		if ($length > 35)
		{
			throw new InputRangeException("SEPA mandate reference number must not be greater thant 35!");
		}
		$length -= strlen($prefix);
		$length -= strlen($suffix);
		if ($length <= 0)
		{
			throw new InputRangeException("Length argument to small to have an random part in SEPA mandate reference.");
		}

		if (!$useSpecialCharacters && !$useLetters && !$useNumbers) {
			throw new InputRangeException("You need at least one allowed character type to generate a random string.");
		}

		$allowedCharacters = [];
		if ($useSpecialCharacters) {
			$allowedCharacters = array_merge($allowedCharacters, str_split(static::MANDATE_REFERENCE_ALLOWED_SPECIAL_CHARACTERS));
		}
		if ($useLetters) {
			$allowedCharacters = array_merge($allowedCharacters, range('A','Z'));
		}
		if ($useNumbers) {
			$allowedCharacters = array_merge($allowedCharacters, range('0','9'));
		}
		$max = count($allowedCharacters) - 1;
		$reference = '';
		for ($i = 0; $i < $length; $i++) {
			$rand = mt_rand(0, $max);
			$reference .= $allowedCharacters[$rand];
		}
		return strtoupper($prefix).$reference.strtoupper($suffix);
	}
}