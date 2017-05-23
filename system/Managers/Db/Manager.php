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

namespace Aurora\System\Managers\Db;

/**
 * CApiDbManager class summary
 *
 * @package Db
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
		parent::__construct('Db', $oManager, $sForcedStorage);

		$this->inc('classes.enum');
		$this->inc('classes.sql');
	}

	/**
	 * @return bool
	 */
	public function testConnection()
	{
		$bResult = false;
		try
		{
			$bResult = $this->oStorage->testConnection();
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $bResult;
	}

	/**
	 * @return bool
	 */
	public function createDatabase(&$sError)
	{
		$bResult = false;
		try
		{
			$bResult = $this->oStorage->createDatabase();
		}
		catch (\Aurora\System\Exceptions\DbException $oException)
		{
			$sError = $oException->getMessage();
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$sError = $oException->getMessage();
			$this->setLastException($oException);
		}

		return $bResult;
	}

	/**
	 * @param mixed $fVerboseCallback Default value is **null**.
	 *
	 * @return bool
	 */
	public function syncTables($fVerboseCallback = null)
	{
		$fVerboseCallback = (null === $fVerboseCallback) ? 'fNullCallback' : $fVerboseCallback;

		$bResult = false;
		try
		{
			$bResult = $this->oStorage->syncTables($fVerboseCallback);
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $bResult;
	}

	/**
	 * @return bool
	 */
	public function isAUsersTableExists()
	{
		$bResult = false;
		try
		{
			$bResult = $this->oStorage->isAUsersTableExists();
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $bResult;
	}

	/**
	 * @return bool
	 */
	public function createTables()
	{
		$bResult = false;
		try
		{
			$bResult = $this->oStorage->createTables();
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $bResult;
	}

	/**
	 * @param bool $bAddDropTable Default value is **false**.
	 *
	 * @return string
	 */
	public function getSqlSchemaAsString($bAddDropTable = false)
	{
		$sResult = '';
		try
		{
			$sResult = $this->oStorage->getSqlSchemaAsString($bAddDropTable);
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $sResult;
	}

	/**
	 * @param bool $bAddDropTable Default value is **false**.
	 *
	 * @return array
	 */
	public function getSqlSchemaAsArray($bAddDropTable = false)
	{
		$aResult = array();
		try
		{
			$aResult = $this->oStorage->getSqlSchemaAsArray($bAddDropTable);
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $aResult;
	}

	/**
	 * @param bool $bAddDropFunction Default value is **false**.
	 *
	 * @return array
	 */
	public function getSqlFunctionsAsArray($bAddDropFunction = false)
	{
		$aResult = array();
		try
		{
			$aResult = $this->oStorage->getSqlFunctionsAsArray($bAddDropFunction);
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $aResult;
	}
}