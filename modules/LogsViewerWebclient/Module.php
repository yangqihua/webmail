<?php
/**
 * @copyright Copyright (c) 2017, Afterlogic Corp.
 * @license AGPL-3.0 or AfterLogic Software License
 *
 * This code is licensed under AGPLv3 license or AfterLogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace Aurora\Modules\LogsViewerWebclient;

/**
 * @package Modules
 */
class Module extends \Aurora\System\Module\AbstractWebclientModule
{
	public function GetUsersWithSeparateLog()
	{
		return \Aurora\System\Api::GetModuleDecorator('Core')->GetUsersWithSeparateLog();
	}
	
	public function TurnOffSeparateLogs()
	{
		return \Aurora\System\Api::GetModuleDecorator('Core')->TurnOffSeparateLogs();
	}
	
	public function ClearSeparateLogs()
	{
		return \Aurora\System\Api::GetModuleDecorator('Core')->ClearSeparateLogs();
	}

	public function GetLogFilesData()
	{
		return \Aurora\System\Api::GetModuleDecorator('Core')->GetLogFilesData();
	}
	
	public function GetLogFile($EventsLog = false, $PublicId = '')
	{
		return \Aurora\System\Api::GetModuleDecorator('Core')->GetLogFile($EventsLog, $PublicId);
	}
	
	public function GetLog($EventsLog, $PartSize = 10240)
	{
		return \Aurora\System\Api::GetModuleDecorator('Core')->GetLog($EventsLog, $PartSize);
	}
	
	public function ClearLog($EventsLog)
	{
		return \Aurora\System\Api::GetModuleDecorator('Core')->ClearLog($EventsLog);
	}
}
