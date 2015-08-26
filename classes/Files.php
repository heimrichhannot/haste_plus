<?php

namespace HeimrichHannot\HastePlus;

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   haste_plus
 * @author    d.patzer@heimrich-hannot.de
 * @license   GNU/LGPL
 * @copyright Heimrich & Hannot GmbH
 */

/**
 * helper class for offering file/dir-handling functionality
 */

class Files {

	/**
	 * Returns the file list for a given directory
	 *
	 * @param string $strDir - the absolute local path to the directory (e.g. /dir/mydir)
	 * @param string $baseUrl - the relative uri (e.g. /tl_files/mydir)
	 * @param string $protectedBaseUrl - domain + request uri -> absUrl will be domain + request uri + ?file=$baseUrl/filename.ext
	 *
	 * @return array file list containing file objects.
	 */
	public static function getFileList($strDir, $baseUrl, $protectedBaseUrl = null) {
		$arrResult = array();
		if (is_dir($strDir)) {
			if ($handler = opendir($strDir)) {
				while (($strFile = readdir($handler)) !== false)
				{
					if (substr($strFile, 0, 1) == '.') continue;
					$arrFile = array();
					$arrFile['filename'] = htmlentities($strFile);
					if ($protectedBaseUrl)
						$arrFile['absUrl'] = $protectedBaseUrl . (empty($_GET) ? '?' : '&') . 'file=' . urlencode($arrFile['absUrl']);
					else
						$arrFile['absUrl'] = str_replace('\\', '/', str_replace('//', '', $baseUrl . '/' . $strFile));
					$arrFile['path'] = str_replace($arrFile['filename'], '', $arrFile['absUrl']);
					$arrFile['filesize'] = self::formatSizeUnits(filesize(str_replace('\\', '/', str_replace('//', '', $strDir . '/' . $strFile))), true);

					$arrResult[] = $arrFile;
				}
				closedir($handler);
			}
		}
		Arrays::aasort($arrResult, 'filename');
		return $arrResult;
	}

	public static function formatSizeUnits($bytes, $keepTogether = false) {
		if ($bytes >= 1073741824) {
			$bytes = number_format($bytes / 1073741824, 2) . ($keepTogether ? '&nbsp;' : ' ') . 'GB';
		} elseif ($bytes >= 1048576) {
			$bytes = number_format($bytes / 1048576, 2) . ($keepTogether ? '&nbsp;' : ' ') . 'MB';
		} elseif ($bytes >= 1024) {
			$bytes = number_format($bytes / 1024, 2) . ($keepTogether ? '&nbsp;' : ' ') . 'KB';
		} elseif ($bytes > 1) {
			$bytes = $bytes . ($keepTogether ? '&nbsp;' : ' ') . 'Bytes';
		} elseif ($bytes == 1) {
			$bytes = $bytes . ($keepTogether ? '&nbsp;' : ' ') . 'Byte';
		} else {
			$bytes = '0' . ($keepTogether ? '&nbsp;' : ' ') . 'Bytes';
		}
		return $bytes;
	}
	
	public static function getFileExtension($strPath)
	{
		return pathinfo($strPath, PATHINFO_EXTENSION);
	}
	
	public static function getFileObjectByBinary($varBinary)
	{
		if (!$varBinary || !($intId = \String::binToUuid($varBinary)) || !($objDir = \FilesModel::findByUuid($intId)))
			return false;
		else
			return $objDir;
	}
	
	public static function getFileObjectById($intId)
	{
		if (!($objFile = \FilesModel::findByUuid($intId)))
			return false;
		else
			return $objFile;
	}
	
	public static function getPathFromUuid($strUuid)
	{
		if (($objFile = \FilesModel::findByUuid($strUuid)) !== null)
		{
			return $objFile->path;
		}
	}

}