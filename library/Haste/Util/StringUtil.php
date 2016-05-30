<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @package haste_plus
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Haste\Util;


class StringUtil extends \Haste\Util\StringUtil
{
	const CAPITAL_LETTERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const CAPITAL_LETTERS_NONAMBIGUOUS = 'ABCDEFGHJKLMNPQRSTUVWX';
	const SMALL_LETTERS = 'abcdefghijklmnopqrstuvwxyz';
	const SMALL_LETTERS_NONAMBIGUOUS = 'abcdefghjkmnpqrstuvwx';
	const NUMBERS = '0123456789';
	const NUMBERS_NONAMBIGUOUS = '23456789';

	/**
	 * Strip tags from text and truncate if needed
	 * @param        $strText The text
	 * @param null   $intLength Truncate length or null if not needed
	 * @param string $allowedTags Allowed tags for strip_tags
	 *
	 * @return string The slim text
	 */
	public static function slimText($strText, $intLength = null, $allowedTags='<p><br><br/>')
	{
		$strText = strip_tags($strText, $allowedTags);

		if($intLength !== null)
		{
			$strText = static::truncateHtml($strText, $intLength);
		}

		$strText = str_replace(array('[-]', '&shy;', '[nbsp]', '&nbsp;'), array('', '', ' ', ' '), $strText);

		return $strText;
	}

	public static function underscoreToCamelCase($strValue, $blnFirstCharCapital = false)
	{
		if ($blnFirstCharCapital == true) {
			$strValue[0] = strtoupper($strValue[0]);
		}

		return preg_replace_callback(
			'/_([a-z])/',
			create_function('$c', 'return strtoupper($c[1]);'),
			$strValue
		);
	}

	public static function preg_replace_last($strRegExp, $strSubject)
	{
		if (!$strRegExp) {
			return $strSubject;
		}

		$strDelimiter = $strRegExp[0];
		$strRegExp    = rtrim(ltrim($strRegExp, $strDelimiter), $strDelimiter);

		return preg_replace("$strDelimiter$strRegExp(?!.*$strRegExp)$strDelimiter", '', $strSubject);
	}

	/**
	 * Check for the occurrence at the start of the string
	 * @param $haystack The string to search in
	 * @param $needle The needle
	 *
	 * @return bool
	 */
	public static function startsWith($haystack, $needle)
	{
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
	}


	/**
	 * Check for the occurrence at the end of the string
	 * @param $haystack The string to search in
	 * @param $needle The needle
	 *
	 * @return bool
	 */
	public static function endsWith($haystack, $needle)
	{
		// search forward starting from end minus needle length characters
		return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
	}

	public static function truncateHtml($text, $length = 100, $ending = '&nbsp;&hellip;', $exact = false, $considerHtml = true) {
		if ($considerHtml) {
			// if the plain text is shorter than the maximum length, return the whole text
			if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}
			// splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
			$total_length = strlen($ending);
			$open_tags = array();
			$truncate = '';
			foreach ($lines as $line_matchings) {
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (!empty($line_matchings[1])) {
					// if it's an "empty element" with or without xhtml-conform closing slash
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
						// do nothing
						// if tag is a closing tag
					} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
						// delete tag from $open_tags list
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false) {
							unset($open_tags[$pos]);
						}
						// if tag is an opening tag
					} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
						// add tag to the beginning of $open_tags list
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings[1];
				}
				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length+$content_length> $length) {
					// the number of characters which are left
					$left = $length - $total_length;
					$entities_length = 0;
					// search for html entities
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
						// calculate the real length of all entities in the legal range
						foreach ($entities[0] as $entity) {
							if ($entity[1]+1-$entities_length <= $left) {
								$left--;
								$entities_length += strlen($entity[0]);
							} else {
								// no more characters left
								break;
							}
						}
					}
					$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
					// maximum lenght is reached, so get off the loop
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}
				// if the maximum length is reached, get off the loop
				if($total_length>= $length) {
					break;
				}
			}
		} else {
			if (strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = substr($text, 0, $length - strlen($ending));
			}
		}
		// if the words shouldn't be cut in the middle...
		if (!$exact) {
			// ...search the last occurance of a space...
			$spacepos = strrpos($truncate, ' ');
			if (isset($spacepos)) {
				// ...and cut the text in this position
				$truncate = substr($truncate, 0, $spacepos);
			}
		}
		// add the defined ending to the text
		$truncate .= $ending;
		if($considerHtml) {
			// close all unclosed html-tags
			foreach ($open_tags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}
		return $truncate;
	}

	public static function randomChar($includeAmbiguousChars = false)
	{
		if ($includeAmbiguousChars)
			$arrChars = static::CAPITAL_LETTERS . static::SMALL_LETTERS . static::NUMBERS;
		else
			$arrChars = static::CAPITAL_LETTERS_NONAMBIGUOUS . static::SMALL_LETTERS_NONAMBIGUOUS .
				static::NUMBERS_NONAMBIGUOUS;

		return $arrChars[rand(0, $includeAmbiguousChars ? 61 : 50)];
	}

	public static function randomLetter($includeAmbiguousChars = false)
	{
		if ($includeAmbiguousChars)
			$arrChars = static::CAPITAL_LETTERS . static::SMALL_LETTERS;
		else
			$arrChars = static::CAPITAL_LETTERS_NONAMBIGUOUS . static::SMALL_LETTERS_NONAMBIGUOUS;

		return $arrChars[rand(0,$includeAmbiguousChars ? 51 : 42)];
	}

	public static function randomNumber($includeAmbiguousChars = false)
	{
		if ($includeAmbiguousChars)
			$arrChars = static::NUMBERS;
		else
			$arrChars = static::NUMBERS_NONAMBIGUOUS;

		return $arrChars[rand(0, $includeAmbiguousChars ? 9 : 7)];
	}

	public static function random($strCharList)
	{
		return $strCharList[rand(0, strlen($strCharList) - 1)];
	}
}
