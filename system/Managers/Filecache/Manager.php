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

namespace Aurora\System\Managers\Filecache;

/**
 * \Aurora\System\Managers\Filecache\Manager class summary
 *
 * @package Filecache
 */
class Manager extends \Aurora\System\Managers\AbstractManagerWithStorage
{
	/**
	 * Creates a new instance of the object.
	 *
	 * @param \Aurora\System\Managers\GlobalManager &$oManager
	 */
	public function __construct(\Aurora\System\Managers\GlobalManager &$oManager, $sForcedStorage = '')
	{
		parent::__construct('Filecache', $oManager, $sForcedStorage);
	}

	/**
	 * @param CAccount|CHelpdeskUser $oAccount
	 * @param string $sKey
	 * @param string $sValue
	 * @param string $sFileSuffix Default value is empty string.
	 * @param string $sFolder Default value is empty string.
	 *
	 * @return bool
	 */
	public function put($oAccount, $sKey, $sValue, $sFileSuffix = '', $sFolder = '')
	{
		$bResult = false;
		try
		{
			$bResult = $this->oStorage->put($oAccount, $sKey, $sValue, $sFileSuffix, $sFolder);
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $bResult;
	}

	/**
	 * @param CAccount|CHelpdeskUser $oAccount
	 * @param string $sKey
	 * @param resource $rSource
	 * @param string $sFileSuffix Default value is empty string.
	 * @param string $sFolder Default value is empty string.
	 *
	 * @return bool
	 */
	public function putFile($oAccount, $sKey, $rSource, $sFileSuffix = '', $sFolder = '')
	{
		$bResult = false;
		try
		{
			$bResult = $this->oStorage->putFile($oAccount, $sKey, $rSource, $sFileSuffix, $sFolder);
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $bResult;
	}

	/**
	 * @param string $sUUID
	 * @param string $sKey
	 * @param string $sSource
	 * @param string $sFileSuffix Default value is empty string.
	 * @param string $sFolder Default value is empty string.
	 *
	 * @return bool
	 */
	public function moveUploadedFile($sUUID, $sKey, $sSource, $sFileSuffix = '', $sFolder = '')
	{
		$bResult = false;
		try
		{
			$bResult = $this->oStorage->moveUploadedFile($sUUID, $sKey, $sSource, $sFileSuffix, $sFolder);
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $bResult;
	}

	/**
	 * @param CAccount|CHelpdeskUser $oAccount
	 * @param string $sKey
	 * @param string $sFileSuffix Default value is empty string.
	 * @param string $sFolder Default value is empty string.
	 *
	 * @return string|bool
	 */
	public function get($oAccount, $sKey, $sFileSuffix = '', $sFolder = '')
	{
		$mResult = false;
		try
		{
			$mResult = $this->oStorage->get($oAccount, $sKey, $sFileSuffix, $sFolder);
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $mResult;
	}

	/**
	 * @param CAccount|CHelpdeskUser $oAccount
	 * @param string $sKey
	 * @param string $sFileSuffix Default value is empty string.
	 * @param string $sFolder Default value is empty string.
	 *
	 * @return resource|bool
	 */
	public function getFile($oAccount, $sKey, $sFileSuffix = '', $sFolder = '')
	{
		$mResult = false;
		try
		{
			$mResult = $this->oStorage->getFile($oAccount, $sKey, $sFileSuffix, $sFolder);
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $mResult;
	}
	
	/**
	 * @param CAccount|CHelpdeskUser $oAccount
	 * @param string $sTempName
	 * @param string $sMode Default value is empty string.
	 *
	 * @return resource|bool
	 */
	public function getTempFile($oAccount, $sTempName, $sMode = '')
	{
		$mResult = false;
		try
		{
			$mResult = $this->oStorage->getTempFile($oAccount, $sTempName, $sMode);
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $mResult;
	}	

	/**
	 * @param string $sUUID
	 * @param string $sKey
	 * @param string $sFileSuffix Default value is empty string.
	 * @param string $sFolder Default value is empty string.
	 *
	 * @return bool
	 */
	public function clear($sUUID, $sKey, $sFileSuffix = '', $sFolder = '')
	{
		$bResult = false;
		try
		{
			$bResult = $this->oStorage->clear($sUUID, $sKey, $sFileSuffix, $sFolder);
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $bResult;
	}

	/**
	 * @param CAccount|CHelpdeskUser $oAccount
	 * @param string $sKey
	 * @param string $sFileSuffix Default value is empty string.
	 * @param string $sFolder Default value is empty string.
	 *
	 * @return int|bool
	 */
	public function fileSize($oAccount, $sKey, $sFileSuffix = '', $sFolder = '')
	{
		$mResult = false;
		try
		{
			$mResult = $this->oStorage->fileSize($oAccount, $sKey, $sFileSuffix, $sFolder);
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $mResult;
	}

	/**
	 * @param CAccount|CHelpdeskUser $oAccount
	 * @param string $sKey
	 * @param string $sFileSuffix Default value is empty string.
	 * @param string $sFolder Default value is empty string.
	 *
	 * @return bool
	 */
	public function isFileExists($oAccount, $sKey, $sFileSuffix = '', $sFolder = '')
	{
		$bResult = false;
		try
		{
			$bResult = $this->oStorage->isFileExists($oAccount, $sKey, $sFileSuffix, $sFolder);
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $bResult;
	}

	/**
	 * @param string $sUUID
	 * @param string $sKey
	 * @param string $sFileSuffix Default value is empty string.
	 * @param string $sFolder Default value is empty string.
	 *
	 * @return bool|string
	 */
	public function generateFullFilePath($sUUID, $sKey, $sFileSuffix = '', $sFolder = '')
	{
		$mResult = false;
		try
		{
			$mResult = $this->oStorage->generateFullFilePath($sUUID, $sKey, $sFileSuffix, $sFolder);
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $mResult;
	}

	/**
	 * @return bool
	 */
	public function gc()
	{
		$bResult = false;
		try
		{
			$bResult = $this->oStorage->gc();
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $bResult;
	}
}
