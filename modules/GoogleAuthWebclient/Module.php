<?php
/**
 * @copyright Copyright (c) 2017, Afterlogic Corp.
 * @license AGPL-3.0 or AfterLogic Software License
 *
 * This code is licensed under AGPLv3 license or AfterLogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace Aurora\Modules\GoogleAuthWebclient;

/**
 * @package Modules
 */
class Module extends \Aurora\System\Module\AbstractWebclientModule
{
	protected $sService = 'google';
	
	protected $aRequireModules = array(
		'OAuthIntegratorWebclient', 
		'Google'
	);
	
	/***** private functions *****/
	protected function issetScope($sScope)
	{
		return in_array($sScope, explode(' ', $this->getConfig('Scopes')));
	}
	
	/**
	 * Initializes FacebookAuthWebclient Module.
	 * 
	 * @ignore
	 */
	public function init()
	{
		$this->incClass('connector');
		$this->subscribeEvent('OAuthIntegratorWebclient::GetServices::after', array($this, 'onAfterGetServices'));
		$this->subscribeEvent('OAuthIntegratorAction', array($this, 'onOAuthIntegratorAction'));
		$this->subscribeEvent('Google::GetSettings', array($this, 'onGetSettings'));
		$this->subscribeEvent('Google::UpdateSettings::after', array($this, 'onAfterUpdateSettings'));
	}
	
	/**
	 * Adds service name to array passed by reference.
	 * 
	 * @ignore
	 * @param array $aArgs
	 * @param array $aServices Array with services names passed by reference.
	 */
	public function onAfterGetServices($aArgs, &$aServices)
	{
		$oModule = \Aurora\System\Api::GetModule('Google'); 
		
		if ($oModule->getConfig('EnableModule', false) && $this->issetScope('auth') &&
			!empty($oModule->getConfig('Id', '')) && !empty($oModule->getConfig('Secret', '')))
		{
			$aServices[] = $this->sService;
		}
	}	
	
	/**
	 * Passes data to connect to service.
	 * 
	 * @ignore
	 * @param string $aArgs Service type to verify if data should be passed.
	 * @param boolean|array $mResult variable passed by reference to take the result.
	 */
	public function onOAuthIntegratorAction($aArgs, &$mResult)
	{
		if ($aArgs['Service'] === $this->sService)
		{
			$sScopes = isset($_COOKIE['oauth-scopes']) ? $_COOKIE['oauth-scopes'] : '';
			$mResult = false;
			$oConnector = new \COAuthIntegratorConnectorGoogle($this);
			if ($oConnector)
			{
				$mResult = $oConnector->Init(
					\Aurora\System\Api::GetModule('Google')->getConfig('Id'), 
					\Aurora\System\Api::GetModule('Google')->getConfig('Secret'),
					$sScopes
				);
			}
			return true;
		}
	}
	
	/**
	 * Passes data to connect to service.
	 * 
	 * @ignore
	 * @param string $aArgs Service type to verify if data should be passed.
	 * @param boolean|array $mResult variable passed by reference to take the result.
	 */
	public function onGetSettings($aArgs, &$mResult)
	{
		$oUser = \Aurora\System\Api::getAuthenticatedUser();
		
		if (!empty($oUser))
		{
			$aScope = array(
				'Name' => 'auth',
				'Description' => $this->i18N('SCOPE_AUTH', $oUser->EntityId),
				'Value' => false
			);
			if ($oUser->Role === \EUserRole::SuperAdmin)
			{
				$aScope['Value'] = $this->issetScope('auth');
				$mResult['Scopes'][] = $aScope;
			}
			if ($oUser->Role === \EUserRole::NormalUser)
			{
				if ($aArgs['OAuthAccount'] instanceof \COAuthAccount)
				{
					$aScope['Value'] = $aArgs['OAuthAccount']->issetScope('auth');
				}
				if ($this->issetScope('auth'))
				{
					$mResult['Scopes'][] = $aScope;
				}
			}
		}
	}
	
	public function onAfterUpdateSettings($aArgs, &$mResult)
	{
		$sScope = '';
		if (isset($aArgs['Scopes']) && is_array($aArgs['Scopes']))
		{
			foreach($aArgs['Scopes'] as $aScope)
			{
				if ($aScope['Name'] === 'auth')
				{
					if ($aScope['Value'])
					{
						$sScope = 'auth';
						break;
					}
				}
			}
		}
		$this->setConfig('Scopes', $sScope);
		$this->saveModuleConfig();
	}
}
