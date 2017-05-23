<?php
/**
 * @copyright Copyright (c) 2017, Afterlogic Corp.
 * @license AGPL-3.0 or AfterLogic Software License
 *
 * This code is licensed under AGPLv3 license or AfterLogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace Aurora\Modules\AdminPanelWebclient;

/**
 * @internal
 */
class Module extends \Aurora\System\Module\AbstractWebclientModule
{
	public function TestDbConnection($DbLogin, $DbName, $DbHost, $DbPassword = null)
	{
		return \Aurora\System\Api::GetModuleDecorator('Core')->TestDbConnection($DbLogin, $DbName, $DbHost, $DbPassword);
	}
	
	public function CreateTables()
	{
		return \Aurora\System\Api::GetModuleDecorator('Core')->CreateTables();
	}
	
	public function GetEntityList($Type)
	{
		return \Aurora\System\Api::GetModuleDecorator('Core')->GetEntityList($Type);
	}
	
	public function GetEntity($Type, $Id)
	{
		return \Aurora\System\Api::GetModuleDecorator('Core')->GetEntity($Type, $Id);
	}
	
	public function CreateTenant($ChannelId = 0, $Name = '', $Description = '')
	{
		return \Aurora\System\Api::GetModuleDecorator('Core')->CreateTenant($ChannelId, $Name, $Description);
	}
	
	public function CreateUser($TenantId = 0, $PublicId = '', $Role = \EUserRole::NormalUser, $WriteSeparateLog = false)
	{
		return \Aurora\System\Api::GetModuleDecorator('Core')->CreateUser($TenantId, $PublicId, $Role, $WriteSeparateLog);
	}
	
	public function UpdateEntity($Type, $Data)
	{
		return \Aurora\System\Api::GetModuleDecorator('Core')->UpdateEntity($Type, $Data);
	}
	
	public function DeleteEntity($Type, $Id)
	{
		return \Aurora\System\Api::GetModuleDecorator('Core')->DeleteEntity($Type, $Id);
	}
	
	public function UpdateSettings($LicenseKey = null, $DbLogin = null,
			$DbPassword = null, $DbName = null, $DbHost = null,
			$AdminLogin = null, $Password = null, $NewPassword = null,
			$Language = null, $TimeFormat = null, $EnableLogging = null,
			$EnableEventLogging = null, $LoggingLevel = null)
	{
		return \Aurora\System\Api::GetModuleDecorator('Core')->UpdateSettings($LicenseKey, $DbLogin,
			$DbPassword, $DbName, $DbHost,
			$AdminLogin, $Password, $NewPassword,
			$Language, $TimeFormat, $EnableLogging,
			$EnableEventLogging, $LoggingLevel);
	}
}
