<?php

namespace HeimrichHannot\Haste\Cache;

use HeimrichHannot\Haste\Util\Curl;
use HeimrichHannot\Haste\Util\Files;

class RemoteImageCache
{
    public static function get($strIdentifier, $varFolder, $strRemoteUrl, $blnReturnUuid = false)
    {
        $strFilename  = $strIdentifier . '.jpg';

        if (\Validator::isUuid($varFolder))
        {
            $objFolder = Files::getFolderFromUuid($varFolder);
            $varFolder = $objFolder->value;
        }

        $objFile = new \File(rtrim($varFolder, '/') . '/' . $strFilename);

        if ($objFile->exists() && $objFile->size > 0)
        {
            return $blnReturnUuid ? $objFile->getModel()->uuid : $objFile->path;
        }

        $strContent = Curl::request($strRemoteUrl);

        if (!$strContent || !is_string($strContent))
        {
            return false;
        }

        $objFile->write($strContent);
        $objFile->close();

        return $blnReturnUuid ? $objFile->getModel()->uuid : $objFile->path;
    }
}