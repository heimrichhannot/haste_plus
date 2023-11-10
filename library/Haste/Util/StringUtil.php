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

use Soundasleep\Html2Text;

class StringUtil
{
    const CAPITAL_LETTERS              = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const CAPITAL_LETTERS_NONAMBIGUOUS = 'ABCDEFGHJKLMNPQRSTUVWX';
    const SMALL_LETTERS                = 'abcdefghijklmnopqrstuvwxyz';
    const SMALL_LETTERS_NONAMBIGUOUS   = 'abcdefghjkmnpqrstuvwx';
    const NUMBERS                      = '0123456789';
    const NUMBERS_NONAMBIGUOUS         = '23456789';


    /**
     * Recursively replace simple tokens and insert tags
     *
     * @param string $text
     * @param array  $tokens    Array of Tokens
     * @param int    $textFlags Filters the tokens and the text for a given set of options
     *
     * @return string
     */
    public static function recursiveReplaceTokensAndTags($text, $tokens, $textFlags = 0)
    {
        if (class_exists('HeimrichHannot\Haste\Util\StringUtil')) {
            $strBuffer = \Haste\Util\StringUtil::recursiveReplaceTokensAndTags($text, $tokens, $textFlags);
        } else {
            $container = \Contao\System::getContainer();
            $strBuffer = $container->get(\Codefog\HasteBundle\StringParser::class)->recursiveReplaceTokensAndTags($text, $tokens, $textFlags);
        }
        return $strBuffer;
    }

    /**
     * Convert the given array or string to plain text using given options
     *
     * @param mixed $value
     * @param int   $options
     *
     * @return mixed
     */
    public static function convertToText($value, $options)
    {
        if (class_exists('HeimrichHannot\Haste\Util\StringUtil')) {
            $value = \Haste\Util\StringUtil::convertToText($value, $options);
        } else {
            $container = \Contao\System::getContainer();
            $value = $container->get(\Codefog\HasteBundle\StringParser::class)->convertToText($value, $options);
        }
        return $value;
    }

    /**
     * Flatten input data, Simple Tokens can't handle arrays
     *
     * @param mixed  $value
     * @param string $key
     * @param array  $data
     * @param string $pattern
     */
    public static function flatten($value, $key, array & $data, $pattern = ', ')
    {
        if (class_exists('HeimrichHannot\Haste\Util\StringUtil')) {
            \Haste\Util\StringUtil::flatten($value, $key, $data, $pattern);
        } else {
            $container = \Contao\System::getContainer();
            $container->get(\Codefog\HasteBundle\StringParser::class)->flatten($value, $key, $data, $pattern);
        }
    }

    /**
     * Convert new line or br with <p> tags
     * @param $text
     * @return string
     */
    public static function nl2p($text)
    {
        $text = preg_replace('#<br\s*/?>#i', "\n", $text); // replace br with new line

        $paragraphs = '';

        foreach (explode("\n", $text) as $line) {
            if (trim($line)) {
                $paragraphs .= '<p>' . $line . '</p>';
            }
        }

        return $paragraphs;
    }

    /**
     * Convert html 2 text with Html2Text\Html2Text
     * @param string $strHtml The html
     * @param array $arrOptions Html2Text\Html2Text options
     *
     * @throws \Soundasleep\Html2TextException
     *
     * @return mixed The converted text, ready for mail delivery for example
     */
    public static function html2Text($strHtml, $arrOptions = [])
    {
        $strHtml = str_replace("\n", '', $strHtml); // remove white spaces from html
        $strHtml = str_replace('</p>', '<br /></p>', $strHtml); // interpret paragrah as block element
        $strHtml = str_replace('</div>', '<br /></div>', $strHtml); // interpret div as block element
        return Html2Text::convert($strHtml, $arrOptions);
    }

    /**
     * Create string like `John Smith <john.smith@example.org>` from email an name
     *
     * @param        $strEmail A valid email
     * @param string $strName A sender name
     *
     * @return string `John Smith <john.smith@example.org>.` or the email if no name was given. Use htmlentities() for frontend presentation!
     */
    public static function generateEmailWithName($strEmail, $strName = '')
    {
        if (!$strName) {
            return $strEmail;
        }

        return $strName . ' <' . $strEmail . '>';
    }

    /**
     * Strip tags from text and truncate if needed
     *
     * @param        $strText     The text
     * @param null $intLength Truncate length or null if not needed
     * @param string $allowedTags Allowed tags for strip_tags
     *
     * @return string The slim text
     */
    public static function slimText($strText, $intLength = null, $allowedTags = '<p><br><br/>')
    {
        $strText = strip_tags($strText, $allowedTags);

        if ($intLength !== null) {
            $strText = static::truncateHtml($strText, $intLength);
        }

        $strText = str_replace(['[-]', '&shy;', '[nbsp]', '&nbsp;'], ['', '', ' ', ' '], $strText);

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

    public static function camelCaseToDashed($strValue)
    {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $strValue));
    }

    public static function camelCaseToUnderscore($strValue)
    {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1_', $strValue));
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
     *
     * @param $haystack string The string to search in
     * @param $needle   string The needle
     *
     * @return bool
     */
    public static function startsWith($haystack, $needle)
    {
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }


    /**
     * Check for the occurrence at the end of the string
     *
     * @param $haystack The string to search in
     * @param $needle   The needle
     *
     * @return bool
     */
    public static function endsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }

    public static function truncateHtml($text, $length = 100, $ending = '&nbsp;&hellip;', $exact = false, $considerHtml = true)
    {
        if ($considerHtml) {
            // if the plain text is shorter than the maximum length, return the whole text
            if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }
            // splits all html-tags to scanable lines
            preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
            $total_length = strlen($ending);
            $open_tags    = [];
            $truncate     = '';
            foreach ($lines as $line_matchings) {
                // if there is any html-tag in this line, handle it and add it (uncounted) to the output
                if (!empty($line_matchings[1])) {
                    // if it's an "empty element" with or without xhtml-conform closing slash
                    if (preg_match(
                        '/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is',
                        $line_matchings[1]
                    )) {
                        // do nothing
                        // if tag is a closing tag
                    } else {
                        if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                            // delete tag from $open_tags list
                            $pos = array_search($tag_matchings[1], $open_tags);
                            if ($pos !== false) {
                                unset($open_tags[$pos]);
                            }
                            // if tag is an opening tag
                        } else {
                            if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                                // add tag to the beginning of $open_tags list
                                array_unshift($open_tags, strtolower($tag_matchings[1]));
                            }
                        }
                    }
                    // add html-tag to $truncate'd text
                    $truncate .= $line_matchings[1];
                }
                // calculate the length of the plain text part of the line; handle entities as one character
                $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
                if ($total_length + $content_length > $length) {
                    // the number of characters which are left
                    $left            = $length - $total_length;
                    $entities_length = 0;
                    // search for html entities
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                        // calculate the real length of all entities in the legal range
                        foreach ($entities[0] as $entity) {
                            if ($entity[1] + 1 - $entities_length <= $left) {
                                $left--;
                                $entities_length += strlen($entity[0]);
                            } else {
                                // no more characters left
                                break;
                            }
                        }
                    }
                    $truncate .= substr($line_matchings[2], 0, $left + $entities_length);
                    // maximum lenght is reached, so get off the loop
                    break;
                } else {
                    $truncate     .= $line_matchings[2];
                    $total_length += $content_length;
                }
                // if the maximum length is reached, get off the loop
                if ($total_length >= $length) {
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
        if ($considerHtml) {
            // close all unclosed html-tags
            foreach ($open_tags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }

        return $truncate;
    }

    public static function randomChar($includeAmbiguousChars = false)
    {
        if ($includeAmbiguousChars) {
            $arrChars = static::CAPITAL_LETTERS . static::SMALL_LETTERS . static::NUMBERS;
        } else {
            $arrChars = static::CAPITAL_LETTERS_NONAMBIGUOUS . static::SMALL_LETTERS_NONAMBIGUOUS . static::NUMBERS_NONAMBIGUOUS;
        }

        return $arrChars[rand(0, $includeAmbiguousChars ? 61 : 50)];
    }

    public static function randomLetter($includeAmbiguousChars = false)
    {
        if ($includeAmbiguousChars) {
            $arrChars = static::CAPITAL_LETTERS . static::SMALL_LETTERS;
        } else {
            $arrChars = static::CAPITAL_LETTERS_NONAMBIGUOUS . static::SMALL_LETTERS_NONAMBIGUOUS;
        }

        return $arrChars[rand(0, $includeAmbiguousChars ? 51 : 42)];
    }

    public static function randomNumber($includeAmbiguousChars = false)
    {
        if ($includeAmbiguousChars) {
            $arrChars = static::NUMBERS;
        } else {
            $arrChars = static::NUMBERS_NONAMBIGUOUS;
        }

        return $arrChars[rand(0, $includeAmbiguousChars ? 9 : 7)];
    }

    public static function random($strCharList)
    {
        return $strCharList[rand(0, strlen($strCharList) - 1)];
    }

    private static function str_replace_once($search, $replace, $text)
    {
        $pos = strpos($text, $search);

        return $pos !== false ? substr_replace($text, $replace, $pos, strlen($search)) : $text;
    }

    /**
     * Replace all script tags before processing with phpQuery
     * -> see http://stackoverflow.com/questions/11901364/phpquery-dom-parser-changing-the-contents-inside-the-script-tag
     *
     * @param $strHtml
     *
     * @return array
     */
    public static function replaceScripts($strHtml)
    {
        preg_match_all('/<script.*?>[\s\S]*?<\/script>/', $strHtml, $tmp);
        $arrScripts = $tmp[0];

        foreach ($arrScripts as $script_id => $script_item) {
            $strHtml = self::str_replace_once(
                $script_item,
                '<div class="script_item_num_' . $script_id . '"></div>',
                $strHtml
            );
        }

        return ['content' => $strHtml, 'scripts' => $arrScripts];
    }

    /**
     * Restore all script tags after processing with phpQuery
     * -> see http://stackoverflow.com/questions/11901364/phpquery-dom-parser-changing-the-contents-inside-the-script-tag
     *
     * @param $strHtml
     * @param $arrScripts
     *
     * @return mixed
     */
    public static function unreplaceScripts($strHtml, $arrScripts)
    {
        preg_match_all('/<div class="script_item_num_(.*?)"><\/div>/', $strHtml, $tmp);

        foreach ($tmp[1] as $script_num_item) {
            $strHtml = str_replace(
                '<div class="script_item_num_' . $script_num_item . '"></div>',
                $arrScripts[$script_num_item],
                $strHtml
            );
        }

        return $strHtml;
    }

    /**
     * Convert german special letters to webconform letters
     * Converts "ä", "ö", "ü", "ß", "Ä", "Ö", "Ü" to "ae", "oe", "ue", "ss", "Ae", "Oe", "Ue"
     * @param $str
     * @return mixed
     */
    public static function convertGermanSpecialLetters($str)
    {
        $search  = ["ä", "ö", "ü", "ß", "Ä", "Ö", "Ü"];
        $replace = ["ae", "oe", "ue", "ss", "Ae", "Oe", "Ue"];
        return str_replace($search, $replace, $str);
    }

    /**
     * Replaces non XML-Entities in XML-String
     *
     * @param $xml
     * @return mixed
     */
    public static function replaceNonXmlEntities($xml)
    {
        $search  = ["&nbsp;", "&mdash;"];
        $replace = ["&#xA0;", "&#x2014;"];
        return str_replace($search, $replace, $xml);
    }
}

