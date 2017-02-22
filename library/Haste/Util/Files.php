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


class Files
{

    /**
     * Get a unique filename within given target folder, remove uniqid() suffix from file (optional, add $strPrefix) and append file count by name to
     * file if file with same name already exists in target folder
     *
     * @param string $strTarget The target file path
     * @param string $strPrefix A uniqid prefix from the given target file, that was added to the file before and should be removed again
     * @param        $i         integer Internal counter for recursion usage or if you want to add the number to the file
     *
     * @return string | false The filename with the target folder and unique id or false if something went wrong (e.g. target does not exist)
     */
    public static function getUniqueFileNameWithinTarget($strTarget, $strPrefix = null, $i = 0)
    {
        $objFile = new \File($strTarget, true);

        $strTarget = ltrim(str_replace(TL_ROOT, '', $strTarget), '/');
        $strPath   = str_replace('.' . $objFile->extension, '', $strTarget);

        if ($strPrefix && ($pos = strpos($strPath, $strPrefix)) !== false)
        {
            $strPath   = str_replace(substr($strPath, $pos, strlen($strPath)), '', $strPath);
            $strTarget = $strPath . '.' . $objFile->extension;
        }

        // Create the parent folder
        if (!file_exists($objFile->dirname))
        {
            $objFolder = new \Folder(ltrim(str_replace(TL_ROOT, '', $objFile->dirname), '/'));

            // something went wrong with folder creation
            if ($objFolder->getModel() === null)
            {
                return false;
            }
        }

        if (file_exists(TL_ROOT . '/' . $strTarget))
        {
            // remove suffix
            if ($i > 0 && StringUtil::endsWith($strPath, '_' . $i))
            {
                $strPath = rtrim($strPath, '_' . $i);
            }

            // increment counter & add extension again
            $i++;

            // for performance reasons, add new unique id to path to make recursion come to end after 100 iterations
            if ($i > 100)
            {
                return static::getUniqueFileNameWithinTarget(static::addUniqIdToFilename($strPath . '.' . $objFile->extension, null, false));
            }

            return static::getUniqueFileNameWithinTarget($strPath . '_' . $i . '.' . $objFile->extension, $strPrefix, $i);
        }

        return $strTarget;
    }

    /**
     * Returns the file list for a given directory
     *
     * @param string $strDir           - the absolute local path to the directory (e.g. /dir/mydir)
     * @param string $baseUrl          - the relative uri (e.g. /tl_files/mydir)
     * @param string $protectedBaseUrl - domain + request uri -> absUrl will be domain + request uri + ?file=$baseUrl/filename.ext
     *
     * @return array file list containing file objects.
     */
    public static function getFileList($strDir, $baseUrl, $protectedBaseUrl = null)
    {
        $arrResult = [];
        if (is_dir($strDir))
        {
            if ($handler = opendir($strDir))
            {
                while (($strFile = readdir($handler)) !== false)
                {
                    if (substr($strFile, 0, 1) == '.')
                    {
                        continue;
                    }
                    $arrFile             = [];
                    $arrFile['filename'] = htmlentities($strFile);
                    if ($protectedBaseUrl)
                    {
                        $arrFile['absUrl'] = $protectedBaseUrl . (empty($_GET) ? '?' : '&') . 'file=' . urlencode($arrFile['absUrl']);
                    }
                    else
                    {
                        $arrFile['absUrl'] = str_replace('\\', '/', str_replace('//', '', $baseUrl . '/' . $strFile));
                    }
                    $arrFile['path']     = str_replace($arrFile['filename'], '', $arrFile['absUrl']);
                    $arrFile['filesize'] =
                        self::formatSizeUnits(filesize(str_replace('\\', '/', str_replace('//', '', $strDir . '/' . $strFile))), true);

                    $arrResult[] = $arrFile;
                }
                closedir($handler);
            }
        }
        Arrays::aasort($arrResult, 'filename');

        return $arrResult;
    }

    public static function formatSizeUnits($bytes, $keepTogether = false)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ($keepTogether ? '&nbsp;' : ' ') . 'GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ($keepTogether ? '&nbsp;' : ' ') . 'MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ($keepTogether ? '&nbsp;' : ' ') . 'KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ($keepTogether ? '&nbsp;' : ' ') . 'Bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ($keepTogether ? '&nbsp;' : ' ') . 'Byte';
        }
        else
        {
            $bytes = '0' . ($keepTogether ? '&nbsp;' : ' ') . 'Bytes';
        }

        return $bytes;
    }

    public static function getPathWithoutFilename($strPathToFile)
    {
        $path = pathinfo($strPathToFile);

        return $path['dirname'];
    }

    public static function getFileExtension($strPath)
    {
        return pathinfo($strPath, PATHINFO_EXTENSION);
    }

    /**
     * @param      $varUuid
     * @param bool $blnCheckExists
     *
     * @return null|string Return the path of the file, or null if not exists
     */
    public static function getPathFromUuid($varUuid, $blnCheckExists = true)
    {
        if (($objFile = \FilesModel::findByUuid($varUuid)) !== null)
        {
            if (!$blnCheckExists)
            {
                return $objFile->path;
            }

            if (file_exists(TL_ROOT . '/' . $objFile->path))
            {
                return $objFile->path;
            }
        }

        return null;
    }

    /**
     * @param      $varUuid
     * @param bool $blnDoNotCreate
     *
     * @return \File|null Return the file object
     */
    public static function getFileFromUuid($varUuid, $blnDoNotCreate = true)
    {
        if ($strPath = static::getPathFromUuid($varUuid))
        {
	        if(is_dir(TL_ROOT . DIRECTORY_SEPARATOR . $strPath))
            {
                return null;
            }
        
            return new \File($strPath, $blnDoNotCreate);
        }
    }

    /**
     * @param      $varUuid
     * @param bool $blnDoNotCreate
     *
     * @return \Folder Return the folder object
     */
    public static function getFolderFromUuid($varUuid, $blnDoNotCreate = true)
    {
        if ($strPath = static::getPathFromUuid($varUuid))
        {
            return new \Folder($strPath, $blnDoNotCreate);
        }
    }

    /**
     * Add a unique identifier to a file name
     *
     * @param      $strFileName    The file name, can be with or without path
     * @param null $strPrefix      Add a prefix to the unique identifier, with an empty prefix, the returned string will be 13 characters long.
     * @param bool $blnMoreEntropy If set to TRUE, will add additional entropy (using the combined linear congruential generator) at the end of the
     *                             return value, which increases the likelihood that the result will be unique.
     *
     * @return string Filename with imestamp based unique identifier
     */
    public static function addUniqIdToFilename($strFileName, $strPrefix = null, $blnMoreEntropy = true)
    {
        $objFile = new \File($strFileName, true);

        $strDirectory = ltrim(str_replace(TL_ROOT, '', $objFile->dirname), '/');

        return ($strDirectory ? $strDirectory . '/' : '') . $objFile->filename . uniqid($strPrefix, $blnMoreEntropy) . ($objFile->extension ? '.'
                                                                                                                                              . $objFile->extension : '');
    }

    /**
     * Sanitize filename, and remove
     *
     * @param string  $strFileName          The file name, can be with or without path
     * @param int     $maxCount             Max filename length
     * @param boolean $blnPreserveUppercase Set to true if you want to lower case the file name
     *
     * @return string The sanitized filename
     */
    public static function sanitizeFileName($strFileName, $maxCount = 0, $blnPreserveUppercase = false)
    {
        $objFile = new \File($strFileName, true);

        $strName = $objFile->filename;

        $strName = standardize($strName, $blnPreserveUppercase);

        if ($maxCount > 0)
        {
            $strName = substr($strName, 0, $maxCount - 1);
        }

        $strDirectory = ltrim(str_replace(TL_ROOT, '', $objFile->dirname), '/');

        return ($strDirectory ? $strDirectory . '/' : '') . $strName . ($objFile->extension ? ('.' . strtolower($objFile->extension)) : '');
    }

    public static function sendTextAsFileToBrowser($strContent, $strFileName)
    {
        header('Content-Disposition: attachment; filename="' . $strFileName . '"');
        header('Content-Type: text/plain');
        header('Connection: close');
        echo $strContent;
        die();
    }

    /**
     * Get real folder from datacontainer attribute
     *
     * @param  mixed              $varFolder The folder as uuid, function, callback array('CLASS', 'method') or string (files/...)
     * @param \DataContainer|null $dc        Optional \DataContainer, required for function and callback
     *
     * @return mixed|null The folder path or null
     * @throws \Exception If ../ is part of the path
     */
    public static function getFolderFromDca($varFolder, \DataContainer $dc = null, $blnDoNotCreate = true)
    {

        // upload folder
        if (is_array($varFolder) && $dc !== null)
        {
            $arrCallback = $varFolder;
            $varFolder   = \System::importStatic($arrCallback[0])->$arrCallback[1]($dc);
        }
        elseif (is_callable($varFolder) && $dc !== null)
        {
            $strMethod = $varFolder;
            $varFolder = $strMethod($dc);
        }
        else
        {
            if (strpos($varFolder, '../') !== false)
            {
                throw new \Exception("Invalid target path $varFolder");
            }
        }

        if ($varFolder instanceof \File)
        {
            $varFolder = $varFolder->value;
        }
        else
        {
            if ($varFolder instanceof \FilesModel)
            {
                $varFolder = $varFolder->path;
            }
        }

        if (\Validator::isUuid($varFolder))
        {
            $objFolder = static::getFolderFromUuid($varFolder, $blnDoNotCreate);
            $varFolder = $objFolder->value;
        }

        return $varFolder;
    }

    public static function getFileLineCount($strFile)
    {
        $intCount = 0;
        $objHandle = fopen(TL_ROOT . '/' . ltrim(str_replace(TL_ROOT, '', $strFile), '/'), 'r');

        while (!feof($objHandle))
        {
            $line = fgets($objHandle);
            $intCount++;
        }

        fclose($objHandle);

        return $intCount;
    }
}
