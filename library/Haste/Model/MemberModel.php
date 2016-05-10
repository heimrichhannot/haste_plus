<?php

namespace HeimrichHannot\Haste\Model;

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

}