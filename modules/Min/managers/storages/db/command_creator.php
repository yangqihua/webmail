<?php
/**
 * @copyright Copyright (c) 2017, Afterlogic Corp.
 * @license AGPL-3.0 or AfterLogic Software License
 *
 * This code is licensed under AGPLv3 license or AfterLogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

/**
 * @package Min
 * @subpackage Storages
 */
class CApiMinCommandCreator extends \Aurora\System\Db\AbstractCommandCreator
{
	/**
	 * @param string $sHash
	 *
	 * @return string
	 */
	public function getMinByHash($sHash)
	{
		$sSql = 'SELECT hash_id, hash, data FROM %smin_hashes WHERE hash = %s';
		
		return sprintf($sSql, $this->prefix(), $this->escapeString($sHash));
	}
	
	/**
	 * @param string $sHashID
	 *
	 * @return string
	 */
	public function getMinByID($sHashID)
	{
		$sSql = 'SELECT hash_id, hash, data FROM %smin_hashes WHERE hash_id = %s';

		return sprintf($sSql, $this->prefix(), $this->escapeString($sHashID));
	}
	
	/**
	 * @param string $sHash
	 *
	 * @return string
	 */
	public function deleteMinByHash($sHash)
	{
		$sSql = 'DELETE FROM %smin_hashes WHERE hash = %s';

		return sprintf($sSql, $this->prefix(), $this->escapeString($sHash));
	}

	/**
	 * @param string $sHashID
	 *
	 * @return string
	 */
	public function deleteMinByID($sHashID)
	{
		$sSql = 'DELETE FROM %smin_hashes WHERE hash_id = %s';

		return sprintf($sSql, $this->prefix(), $this->escapeString($sHashID));
	}

	/**
	 * @param string $sHash
	 * @param string $sHashID
	 * @param string $sEncodedParams
	 *
	 * @return string
	 */
	public function createMin($sHash, $sHashID, $sEncodedParams)
	{
		$sSql = 'INSERT INTO %smin_hashes ( hash_id, hash, data ) VALUES ( %s, %s, %s )';

		return sprintf($sSql, $this->prefix(), $this->escapeString($sHashID), $this->escapeString($sHash),
			$this->escapeString($sEncodedParams));
	}

	/**
	 * @param string $sHashID
	 * @param string $sEncodedParams
	 * @param string $sNewHashID Default value is **null**
	 *
	 * @return string
	 */
	public function updateMinByID($sHashID, $sEncodedParams, $sNewHashID = null)
	{
		$sAdd = '';
		if (!empty($sNewHashID))
		{
			$sAdd = sprintf(', hash_id = %s', $this->escapeString($sNewHashID));
		}

		$sSql = 'UPDATE %smin_hashes SET data = %s%s WHERE hash_id = %s';

		return sprintf($sSql, $this->prefix(), $this->escapeString($sEncodedParams), $sAdd, $this->escapeString($sHashID));
	}
	
	/**
	 * @param string $sHash
	 * @param string $sEncodedParams
	 * @param string $sNewHashID Default value is **null**
	 *
	 * @return string
	 */
	public function updateMinByHash($sHash, $sEncodedParams, $sNewHashID = null)
	{
		$sAdd = '';
		if (!empty($sNewHashID))
		{
			$sAdd = sprintf(', hash_id = %s', $this->escapeString($sNewHashID));
		}
		
		$sSql = 'UPDATE %smin_hashes SET data = %s%s WHERE hash = %s';

		return sprintf($sSql, $this->prefix(), $this->escapeString($sEncodedParams), $sAdd, $this->escapeString($sHash));
	}
}

/**
 * @package Min
 * @subpackage Storages
 */
class CApiMinCommandCreatorMySQL extends CApiMinCommandCreator
{
	
}

/**
 * @package Min
 * @subpackage Storages
 */
class CApiMinCommandCreatorPostgreSQL extends CApiMinCommandCreator
{

}
