<?php
/**
 * @copyright Copyright (c) 2017, Afterlogic Corp.
 * @license AGPL-3.0 or AfterLogic Software License
 *
 * This code is licensed under AGPLv3 license or AfterLogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace Aurora\Modules\Dropbox;

/**
 * @package Modules
 */
class Module extends \Aurora\System\Module\AbstractModule
{
	protected $sService = 'dropbox';
	
	protected $aRequireModules = array(
		'OAuthIntegratorWebclient'
	);
	
	/***** private functions *****/
	/**
	 * Initializes Dropbox Module.
	 * 
	 * @ignore
	 */
	public function init()
	{
		$this->subscribeEvent('GetServicesSettings', array($this, 'onGetServicesSettings'));
		$this->subscribeEvent('UpdateServicesSettings', array($this, 'onUpdateServicesSettings'));
	}
	
	/**
	 * Adds service settings to array passed by reference.
	 * 
	 * @ignore
	 * @param array $aServices Array with services settings passed by reference.
	 */
	public function onGetServicesSettings(&$aServices)
	{
		$aSettings = $this->GetSettings();
		if (!empty($aSettings))
		{
			$aServices[] = $aSettings;
		}
	}
	
	/**
	 * Updates service settings.
	 * 
	 * @ignore
	 * @param array $aServices Array with new values for service settings.
	 * 
	 * @throws \Aurora\System\Exceptions\ApiException
	 */
	public function onUpdateServicesSettings($aServices)
	{
		$aSettings = $aServices[$this->sService];
		
		if (\is_array($aSettings))
		{
			$this->UpdateSettings($aSettings['EnableModule'], $aSettings['Id'], $aSettings['Secret']);
		}
	}
	/***** private functions *****/
	
	/***** public functions might be called with web API *****/
	/**
	 * Obtains list of module settings for authenticated user.
	 * 
	 * @return array
	 */
	public function GetSettings()
	{
		$aResult = array();
		\Aurora\System\Api::checkUserRoleIsAtLeast(\EUserRole::Anonymous);
		
		$oUser = \Aurora\System\Api::getAuthenticatedUser();
		if (!empty($oUser) && $oUser->Role === \EUserRole::SuperAdmin)
		{
			$aResult = array(
				'Name' => $this->sService,
				'DisplayName' => $this->GetName(),
				'EnableModule' => $this->getConfig('EnableModule', false),
				'Id' => $this->getConfig('Id', ''),
				'Secret' => $this->getConfig('Secret', '')
			);
		}
		
		if (!empty($oUser) && $oUser->Role === \EUserRole::NormalUser)
		{
			$oAccount = null;
			$oOAuthIntegratorWebclientDecorator = \Aurora\System\Api::GetModuleDecorator('OAuthIntegratorWebclient');
			if ($oOAuthIntegratorWebclientDecorator)
			{
				$oAccount = $oOAuthIntegratorWebclientDecorator->GetAccount($this->sService);
			}
			$aResult = array(
				'EnableModule' => $this->getConfig('EnableModule', false),
				'Connected' => $oAccount ? true : false
			);
			$aArgs = array(
				'OAuthAccount' => $oAccount
			);
		}
		$this->broadcastEvent('GetSettings', $aArgs, $aResult);
		
		return $aResult;
	}
	
	/**
	 * Updates service settings.
	 * 
	 * @param boolean $EnableModule **true** if module should be enabled.
	 * @param string $Id Service app identifier.
	 * @param string $Secret Service app secret.
	 * 
	 * @throws \Aurora\System\Exceptions\ApiException
	 */
	public function UpdateSettings($EnableModule, $Id, $Secret)
	{
		\Aurora\System\Api::checkUserRoleIsAtLeast(\EUserRole::TenantAdmin);
		
		try
		{
			$this->setConfig('EnableModule', $EnableModule);
			$this->setConfig('Id', $Id);
			$this->setConfig('Secret', $Secret);
			$this->saveModuleConfig();
		}
		catch (\Exception $ex)
		{
			throw new \Aurora\System\Exceptions\ApiException(\Aurora\System\Notifications::CanNotSaveSettings);
		}
		
		return true;
	}
	
	/**
	 * Deletes DropBox account.
	 * 
	 * @return boolean
	 */
	public function DeleteAccount()
	{
		\Aurora\System\Api::checkUserRoleIsAtLeast(\EUserRole::NormalUser);
		
		$bResult = false;
		$oOAuthIntegratorWebclientDecorator = \Aurora\System\Api::GetModuleDecorator('OAuthIntegratorWebclient');
		if ($oOAuthIntegratorWebclientDecorator)
		{
			$bResult = $oOAuthIntegratorWebclientDecorator->DeleteAccount($this->sService);
		}
		
		return $bResult;
	}
	/***** public functions might be called with web API *****/
}
