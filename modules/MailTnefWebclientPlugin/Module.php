<?php
/**
 * @copyright Copyright (c) 2017, Afterlogic Corp.
 * @license AGPL-3.0 or AfterLogic Software License
 *
 * This code is licensed under AGPLv3 license or AfterLogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace Aurora\Modules\MailTnefWebclientPlugin;

/**
 * @package Modules
 */
class Module extends \Aurora\System\Module\AbstractModule
{
	/* 
	 * @var $oApiFileCache \Aurora\System\Managers\Filecache\Manager 
	 */	
	public $oApiFileCache = null;
	
	public function init() 
	{
		$this->incClass('tnef');
		$this->oApiFileCache = \Aurora\System\Api::GetSystemManager('Filecache');
	}
	
	public function ExpandFile($UserId, $Hash)
	{
		$mResult = array();
		
		$sUUID = \Aurora\System\Api::getUserUUIDById($UserId);
		$aValues = \Aurora\System\Api::DecodeKeyValues($Hash);
		$oMailDecorator = \Aurora\System\Api::GetModuleDecorator('Mail');
		$aFiles = $oMailDecorator->SaveAttachmentsAsTempFiles($aValues['AccountID'], [$Hash]);
		foreach ($aFiles as $sTempName => $sHash)
		{
			if ($sHash === $Hash)
			{
				$rResource = $this->oApiFileCache->getFile($sUUID, $sTempName);
				$mResult = $this->expandTnefAttachment($sUUID, $rResource);
			}
		}
			
		return $mResult;
	}
	
	private function expandTnefAttachment($sUUID, $rResource)
	{
		$mResult = array();

		$oTNEF = new \TNEF();
		if ($oTNEF)
		{
			$aData = $oTNEF->Decode(\stream_get_contents($rResource));
			if (is_array($aData))
			{
				foreach ($aData as $aItem)
				{
					if (is_array($aItem) && isset($aItem['name'], $aItem['stream']))
					{
						$sFileName = \MailSo\Base\Utils::Utf8Clear(basename($aItem['name']));

						$sTempName = md5(\microtime(true).rand(1000, 9999));
						$rItemStream = fopen('php://memory','r+');
						fwrite($rItemStream, $aItem['stream']);
						rewind($rItemStream);
						if ($this->oApiFileCache->putFile($sUUID, $sTempName, $rItemStream))
						{
							$sFileName = str_replace("\0", '', $sFileName);
							$mResult[] = \Aurora\System\Utils::GetClientFileResponse(\Aurora\System\Api::getAuthenticatedUserId(), $sFileName, $sTempName, strlen($aItem['stream']));
						}
					}
				}
			}
		}

		return $mResult;
	}
}
