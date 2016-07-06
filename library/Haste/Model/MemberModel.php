<?php

namespace HeimrichHannot\Haste\Model;

use HeimrichHannot\Haste\Dca\General;
use HeimrichHannot\Haste\Util\Files;

class MemberModel extends \MemberModel
{
	
	/**
	 * Find a member by e-mail-address
	 *
	 * @param string $strEmail    The e-mail address
	 * @param array  $arrOptions  An optional options array
	 *
	 * @return \Model|null The model or null if there is no member
	 */
	public static function findByEmail($strEmail, array $arrOptions=array())
	{
		$time = time();
		$t = static::$strTable;

		$arrColumns = array("LOWER($t.email)=? AND ($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time)");

		return static::findOneBy($arrColumns, array($strEmail), $arrOptions);
	}

	/**
	 * Tries to find a member with the given email address. If found, this member is returned, if not a new member with this email address is created.
	 * @param $strEmail
	 *
	 * @return \MemberModel
	 */
	public static function findOrCreate($strEmail)
	{
		$objMember = static::findByEmail($strEmail);

		if ($objMember === null)
		{
			$objMember = new \MemberModel();
			$objMember->dateAdded	= time();
			$objMember->tstamp		= time();
			$objMember->email = trim(strtolower($strEmail));
			$objMember->save();
		}

		return $objMember;
	}

	/**
	 * Adds a new home dir to a member. Therefore a folder named with the members's id is created in $varRootFolder
	 * @param            $varMember object|int The member as object or member id
	 * @param            $strBooleanPropertyName string The name of the boolean member property (e.g. "assignDir")
	 * @param            $strPropertyName string The name of the member property (e.g. "homeDir")
	 * @param            $varRootFolder string|object The base folder as instance of \FilesModel, path string or uuid
	 * @param bool|false $blnOverwrite bool Determines if an existing folder can be overridden
	 *
	 * @return bool|string Returns true, if a directory has already been linked with the member, the folders uuid if successfully added and false if errors occured.
	 */
	public static function addHomeDir($varMember, $strBooleanPropertyName = 'assignDir', $strPropertyName = 'homeDir',
		$varRootFolder = 'files/members', $blnOverwrite = false)
	{
		if (($objMember = is_numeric($varMember) ? \MemberModel::findByPk($varMember) : $varMember) === null)
			return false;

		// already set
		if ($objMember->{$strBooleanPropertyName} && $objMember->{$strPropertyName} && !$blnOverwrite)
			return true;

		if (!($varRootFolder instanceof \FilesModel))
		{
			if (\Validator::isUuid($varRootFolder))
			{
				$objFolderModel = \FilesModel::findByUuid($varRootFolder);
				$strPath = $objFolderModel->path;
			}
			else
			{
				$strPath = $varRootFolder;
			}
		}
		else
		{
			$strPath = $varRootFolder->path;
		}

		$strPath = str_replace(TL_ROOT, '', $strPath);

		if (!$strPath)
			return false;

		$objMember->{$strBooleanPropertyName} = true;
		$strPath = ltrim($strPath, '/') . '/' . $objMember->id;

		$objHomeDir = new \Folder($strPath);

		$objMember->{$strPropertyName} = $objHomeDir->getModel()->uuid;

		$objMember->save();

		return $objHomeDir->getModel()->uuid;
	}

	/**
	 * Returns a member home dir and creates one, if desired.
	 *
	 * @param            $varMember object|int The member as object or member id
	 * @param            $strBooleanPropertyName string The name of the boolean member property (e.g. "assignDir")
	 * @param            $strPropertyName string The name of the member property (e.g. "homeDir")
	 * @param            $varRootFolder string|object The base folder as instance of \FilesModel, path string or uuid
	 * @param bool|false $blnOverwrite bool Determines if an existing folder can be overridden
	 *
	 * @return bool|string Returns the home dir or false if an error occurred.
	 */
	public static function getHomeDir($varMember, $strBooleanPropertyName = 'assignDir', $strPropertyName = 'homeDir',
		$varRootFolder = 'files/members', $blnOverwrite = false)
	{
		if (($objMember = is_numeric($varMember) ? \MemberModel::findByPk($varMember) : $varMember) === null)
			return false;

		$varResult = static::addHomeDir($objMember, $strBooleanPropertyName, $strPropertyName, $varRootFolder, $blnOverwrite);

		if ($varResult === false)
			return false;
		else
			return Files::getPathFromUuid($objMember->{$strPropertyName});
	}

}