<?php

namespace HeimrichHannot\Haste\Dca;


use NotificationCenter\Model\Message;

class Notification extends \Backend
{
	public static function getNotificationMessagesAsOptions($objDc, $strType = null)
	{
		$arrOptions = array();

		if (!$strType)
		{
			$objMessages = Message::findAll();

			if ($objMessages === null) {
				return $arrOptions;
			}

			while ($objMessages->next()) {
				if (($objNotification = $objMessages->getRelated('pid')) === null) {
					continue;
				}

				$arrOptions[$objNotification->title][$objMessages->id] = $objMessages->title;
			}
		}
		else
		{
			$objMessages = \Database::getInstance()->execute("SELECT m.id,m.title FROM tl_nc_message m INNER JOIN tl_nc_notification n ON m.pid=n.id WHERE n.type='$strType' ORDER BY m.title");

			if ($objMessages->numRows > 0)
			{
				while ($objMessages->next()) {
					$arrOptions[$objMessages->id] = $objMessages->title;
				}
			}
		}

		return $arrOptions;
	}

	public static function getNewNotificationTypeArray($blnIncludeNotificationCenterPlusTokens = false)
	{
		$arrType = array(
			'recipients'           => array('admin_email'),
			'email_subject'        => array('admin_email'),
			'email_text'           => array('admin_email'),
			'email_html'           => array('admin_email'),
			'file_name'            => array('admin_email'),
			'file_content'         => array('admin_email'),
			'email_sender_name'    => array('admin_email'),
			'email_sender_address' => array('admin_email'),
			'email_recipient_cc'   => array('admin_email'),
			'email_recipient_bcc'  => array('admin_email'),
			'email_replyTo'        => array('admin_email'),
			'attachment_tokens'    => array(),
		);

		if ($blnIncludeNotificationCenterPlusTokens)
		{
			foreach ($arrType as $strField => $arrTokens)
			{
				$arrType[$strField] = array_unique(array_merge(array(
					'env_*', 'page_*', 'user_*', 'date', 'last_update'
				), $arrTokens));
			}
		}

		return $arrType;
	}

	public static function addFormHybridStyleEntityTokens($strPrefix, $arrNotificationTypeArray)
	{
		// add ?_value_* and ?_plain_* to all fields
		foreach ($arrNotificationTypeArray as $strField => $arrTokens)
		{
			$arrNotificationTypeArray[$strField] = array_unique(array_merge(array(
				$strPrefix . '_value_*',
				$strPrefix . '_plain_*'
			), $arrTokens));
		}

		// add ?submission, ?submission_all and ?_submission_* to only some of the fields
		foreach (array('email_text', 'email_html') as $strField)
		{
			$arrNotificationTypeArray[$strField] = array_unique(array_merge(array(
				$strPrefix . 'submission',
				$strPrefix . 'submission_all',
				$strPrefix . '_submission_*',
			), $arrNotificationTypeArray[$strField]));
		}

		return $arrNotificationTypeArray;
	}

	public static function activateType($strGroup, $strType, $arrType)
	{
		$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE'] = array_merge_recursive(
			(array) $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE'],
			array(
				$strGroup => array(
					$strType => $arrType
				)
			)
		);
	}
}