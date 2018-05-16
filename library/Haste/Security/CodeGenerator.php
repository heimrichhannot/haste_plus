<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) Heimrich & Hannot GmbH
 *
 * @author Dennis Patzer
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Security;

use HeimrichHannot\Haste\Util\StringUtil;
use PWGen\PWGen;

class CodeGenerator extends \Controller
{
	const CAPITAL_LETTERS = 'capitalLetters';
	const SMALL_LETTERS = 'smallLetters';
	const NUMBERS = 'numbers';
	const SPECIAL_CHARS = 'specialChars';

	protected static $blnPreventAmbiguous = true;

	protected static $arrAlphabets = [
		CodeGenerator::CAPITAL_LETTERS,
		CodeGenerator::SMALL_LETTERS,
		CodeGenerator::NUMBERS
    ];

	protected static $arrRules = [
		CodeGenerator::CAPITAL_LETTERS,
		CodeGenerator::SMALL_LETTERS,
		CodeGenerator::NUMBERS
    ];

	protected static $strAllowedSpecialChars = '[=<>()#/]';

	public static function generate($intLength = 8, $blnPreventAmbiguous = true, $arrAlphabets = null, $arrRules = null, $strAllowedSpecialChars = null)
	{
		$arrAlphabets = is_array($arrAlphabets) ? $arrAlphabets : static::$arrAlphabets;
		$arrRules = is_array($arrRules) ? $arrRules : static::$arrRules;
		$strAllowedSpecialChars = $strAllowedSpecialChars !== null ? $strAllowedSpecialChars : static::$strAllowedSpecialChars;

		$pwGen = new PWGen(
			$intLength,
			false,
			in_array(CodeGenerator::NUMBERS, $arrAlphabets) && in_array(CodeGenerator::NUMBERS, $arrRules),
			in_array(CodeGenerator::CAPITAL_LETTERS, $arrAlphabets) && in_array(CodeGenerator::CAPITAL_LETTERS, $arrRules),
			$blnPreventAmbiguous,
			false,
			in_array(CodeGenerator::SPECIAL_CHARS, $arrAlphabets) && in_array(CodeGenerator::SPECIAL_CHARS, $arrRules)
		);

		$strCode = $pwGen->generate();

		// replace remaining ambiguous characters
		if ($blnPreventAmbiguous)
		{
			$arrCharReplacements = ['y', 'Y', 'z', 'Z', 'o', 'O', 'i', 'I', 'l'];

			foreach ($arrCharReplacements as $strChar)
			{
				$strCode = str_replace($strChar, StringUtil::randomChar(!$blnPreventAmbiguous), $strCode);
			}
		}

		// apply allowed alphabets
		$strForbiddenPattern = '';
		$strAllowedChars = '';

		if (!in_array(CodeGenerator::CAPITAL_LETTERS, $arrAlphabets))
		{
			$strForbiddenPattern .= 'A-Z';
		}
		else
		{
			$strAllowedChars .= ($blnPreventAmbiguous ? StringUtil::CAPITAL_LETTERS_NONAMBIGUOUS : StringUtil::CAPITAL_LETTERS);
		}

		if (!in_array(CodeGenerator::SMALL_LETTERS, $arrAlphabets))
		{
			$strForbiddenPattern .= 'a-z';
		}
		else
		{
			$strAllowedChars .= ($blnPreventAmbiguous ? StringUtil::SMALL_LETTERS_NONAMBIGUOUS : StringUtil::SMALL_LETTERS);
		}

		if (!in_array(CodeGenerator::NUMBERS, $arrAlphabets))
		{
			$strForbiddenPattern .= '0-9';
		}
		else
		{
			$strAllowedChars .= ($blnPreventAmbiguous ? StringUtil::NUMBERS_NONAMBIGUOUS : StringUtil::NUMBERS);
		}

		if ($strForbiddenPattern)
		{
			$strCode = preg_replace_callback('@[' . $strForbiddenPattern . ']{1}@', function() use ($strAllowedChars) {
				return StringUtil::random($strAllowedChars);
			}, $strCode);
		}

		// special chars
		if (!in_array(CodeGenerator::SPECIAL_CHARS, $arrAlphabets))
		{
			$strCode = preg_replace_callback('@[^' . $strAllowedChars . ']{1}@', function() use ($strAllowedChars) {
				return StringUtil::random($strAllowedChars);
			}, $strCode);
		}
		else
		{
			$strCode = preg_replace_callback('@[^' . $strAllowedChars . ']{1}@', function() use ($strAllowedSpecialChars) {
				return StringUtil::random($strAllowedSpecialChars);
			}, $strCode);
		}

		return $strCode;
	}
}
