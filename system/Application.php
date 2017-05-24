<?php
/*
 * @copyright Copyright (c) 2017, Afterlogic Corp.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 * 
 */

namespace Aurora\System;

/**
 * @category Core
 */
class Application
{
	/**
	 * @type string
	 */
	const AUTH_TOKEN_KEY = 'AuthToken';
	
	/**
	 * @var \Aurora\System\Module\Manager
	 */
	protected $oModuleManager;

    /**
     */
	protected function __construct()
	{
		$this->oModuleManager = \Aurora\System\Api::GetModuleManager();

//		\MailSo\Config::$FixIconvByMbstring = false;
		\MailSo\Config::$SystemLogger = \Aurora\System\Api::MailSoLogger();
		$oSettings =& \Aurora\System\Api::GetSettings();
		\MailSo\Config::$PreferStartTlsIfAutoDetect = !!$oSettings->GetConf('PreferStarttls', true);
	}

	/**
	 * @return \Aurora\System\Application
	 */
	public static function NewInstance()
	{
		return new self();
	}
	
	/**
	 * @return \Aurora\System\Application
	 */
	public static function SingletonInstance()
	{
		static $oInstance = null;
		if (null === $oInstance)
		{
			$oInstance = self::NewInstance();
		}

		return $oInstance;
	}
	
	public static function Start($sDefaultEntry = 'default')
	{
		\Aurora\System\Api::Init();
		
		self::RedirectToHttps();
		self::GetVersion();

		self::SingletonInstance()->oModuleManager->RunEntry(
			\strtolower(self::GetPathItemByIndex(0, $sDefaultEntry))
		);
	}

	/**
	 * @return string
	 */
	public static function GetVersion()
	{
		$sVersion = @\file_get_contents(AURORA_APP_ROOT_PATH.'VERSION');
		\define('AURORA_APP_VERSION', $sVersion);
		return $sVersion;
	}
	
	public static function RedirectToHttps()
	{
		$oSettings =& \Aurora\System\Api::GetSettings();
		$bRedirectToHttps = $oSettings->GetConf('RedirectToHttps');
		
		$bHttps = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off") || 
				(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == "443"));
		if ($bRedirectToHttps && !$bHttps) 
		{
			\header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		}
	}
	
	/**
	 * @return array
	 */
	public static function GetPaths()
	{
		static $aResult = null;
		if ($aResult === null)
		{
			$aResult = array();
			$aQuery = array();

			$oHttp = \MailSo\Base\Http::SingletonInstance();
			$aPathInfo = \array_filter(
				\explode('/', \trim(\trim($oHttp->GetServer('PATH_INFO', ''), '/')))
			);
			if (0 < \count($aPathInfo)) 
			{
				$aQuery = $aPathInfo;
			} 
			else 
			{
				$sQuery = \trim(\trim($oHttp->GetQueryString()), ' /');

				$iPos = \strpos($sQuery, '&');
				if (0 < $iPos) 
				{
					$sQuery = \substr($sQuery, 0, $iPos);
				}
				$aQuery = \explode('/', $sQuery);
			}
			foreach ($aQuery as $sQueryItem) 
			{
				$iPos = \strpos($sQueryItem, '=');
				$aResult[] = (!$iPos) ? $sQueryItem : \substr($sQueryItem, 0, $iPos);
			}
		}
		
		return $aResult;
	}

    /**
     *
     * @param int $iIndex
     * @return mixed|null
     */
	public static function GetPathItemByIndex($iIndex, $mDefaultValue = null)
	{
		$aPath = self::GetPaths();
		
		return !empty($aPath[$iIndex]) ? $aPath[$iIndex] : $mDefaultValue;
	}
}
