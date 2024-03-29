<?php
/**
 * @copyright Copyright (c) 2017, Afterlogic Corp.
 * @license AGPL-3.0 or AfterLogic Software License
 *
 * This code is licensed under AGPLv3 license or AfterLogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace Aurora\Modules\StandardRegisterFormWebclient;

/**
 * @package Modules
 */
class Module extends \Aurora\System\Module\AbstractWebclientModule
{
	/***** public functions might be called with web API *****/
	/**
	 * Obtains list of module settings for authenticated user.
	 * 
	 * @return array
	 */
	public function GetSettings()
	{
		\Aurora\System\Api::checkUserRoleIsAtLeast(\EUserRole::Anonymous);
		
		return array(
			'ServerModuleName' => $this->getConfig('ServerModuleName', ''),
			'HashModuleName' => $this->getConfig('HashModuleName', ''),
			'CustomLogoUrl' => $this->getConfig('CustomLogoUrl', ''),
			'InfoText' => $this->getConfig('InfoText', ''),
		);
	}
	
	/**
	 * Broadcasts Register event to other modules to log in the system with specified parameters.
	 * 
	 * @param string $Name New name for user.
	 * @param string $Login Login for authentication.
	 * @param string $Password Password for authentication.
	 * @param int $UserId Identifier of user which will contain new account.
	 * @return array
	 * @throws \Aurora\System\Exceptions\ApiException
	 */
	public function Register($Login, $Password, $UserId)
	{
		\Aurora\System\Api::checkUserRoleIsAtLeast(\EUserRole::Anonymous);
		
		if (empty($UserId))
		{
			\Aurora\System\Api::skipCheckUserRole(true);

			$UserId = \Aurora\System\Api::GetModuleDecorator('Core')->CreateUser(0, $Login);
			
			\Aurora\System\Api::skipCheckUserRole(false);
		}

		if (empty($UserId))
		{
			throw new \Aurora\System\Exceptions\ApiException(\Aurora\System\Notifications::InvalidInputParameter);
		}
		
		$mResult = false;
		
		$refArgs = array (
			'Login' => $Login,
			'Password' => $Password,
			'UserId' => $UserId,
		);
		$this->broadcastEvent(
			'Register', 
			$refArgs,
			$mResult
		);
		
		if (!empty($mResult))
		{
			$oLoginDecorator = \Aurora\System\Api::GetModuleDecorator('StandardLoginFormWebclient');
			$mResult = $oLoginDecorator->Login($Login, $Password);
			\Aurora\System\Api::getAuthenticatedUserId($mResult['AuthToken']);
		}
		
		if (!empty($Name) && !empty($mResult) && !empty($UserId))
		{
			$oCoreDecorator = \Aurora\System\Api::GetModuleDecorator('Core');
			$oCoreDecorator->UpdateUser($UserId, $Name);
		}
		
		return $mResult;
	}
	/***** public functions might be called with web API *****/
}
