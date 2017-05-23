<?php
/**
 * @copyright Copyright (c) 2017, Afterlogic Corp.
 * @license AGPL-3.0 or AfterLogic Software License
 *
 * This code is licensed under AGPLv3 license or AfterLogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace Aurora\Modules\MailSaveMessageAsPdfPlugin;

/**
 * @package Modules
 */
class Module extends \Aurora\System\Module\AbstractModule
{
	/**
	 * @param int $UserId
	 * @param string $FileName
	 * @param string $Html
	 * @return boolean
	 */
	public function GeneratePdfFile($UserId, $FileName, $Html)
	{
		\Aurora\System\Api::checkUserRoleIsAtLeast(\EUserRole::NormalUser);
		
		$sFileName = $FileName . '.pdf';

		$sUUID = \Aurora\System\Api::getUserUUIDById($UserId);
		$sTempName = md5($sUUID.$sFileName.microtime(true));

		$oCssToInlineStyles = new \TijsVerkoyen\CssToInlineStyles\CssToInlineStyles($Html);
		$oCssToInlineStyles->setEncoding('utf-8');
		$oCssToInlineStyles->setUseInlineStylesBlock(true);

		$sExec = \Aurora\System\Api::DataPath().'/system/wkhtmltopdf/linux/wkhtmltopdf';
		if (!\file_exists($sExec))
		{
			$sExec = \Aurora\System\Api::DataPath().'/system/wkhtmltopdf/win/wkhtmltopdf.exe';
			if (!\file_exists($sExec))
			{
				$sExec = '';
			}
		}
		
		if (0 < \strlen($sExec))
		{
			$oSnappy = new \Knp\Snappy\Pdf($sExec);
			$oSnappy->setOption('quiet', true);
			$oSnappy->setOption('disable-javascript', true);

			$oApiFileCache = \Aurora\System\Api::GetSystemManager('Filecache');
			
			$oSnappy->generateFromHtml($oCssToInlineStyles->convert(),
				$oApiFileCache->generateFullFilePath($sUUID, $sTempName), array(), true);
			
			return \Aurora\System\Utils::GetClientFileResponse($UserId, $sFileName, $sTempName, $oApiFileCache->fileSize($sUUID, $sTempName));
		}
		else
		{
			throw new \Aurora\System\Exceptions\ApiException(\Aurora\System\Notifications::LibraryNoFound);
		}

		return false;
	}
}
