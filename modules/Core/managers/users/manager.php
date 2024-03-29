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
 * CApiUsersManager class summary
 * 
 * @package Users
 */
class CApiCoreUsersManager extends \Aurora\System\Managers\AbstractManager
{
	/**
	 * @var \Aurora\System\Managers\Eav\Manager
	 */
	public $oEavManager = null;
	
	/**
	 * @param \Aurora\System\Managers\GlobalManager &$oManager
	 */
	public function __construct(\Aurora\System\Managers\GlobalManager &$oManager, $sForcedStorage = '', \Aurora\System\Module\AbstractModule $oModule = null)
	{
		parent::__construct('users', $oManager, $oModule);
		
		$this->oEavManager = \Aurora\System\Api::GetSystemManager('eav', 'db');
	}

	/**
	 * Retrieves information on particular WebMail Pro user. 
	 * 
	 * @param int|string $mUserId User identifier or UUID.
	 * 
	 * @return CUser | false
	 */
	public function getUser($mUserId)
	{
		$oUser = false;
		try
		{
			$oUser = $this->oEavManager->getEntity($mUserId);
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$oUser = false;
			$this->setLastException($oException);
		}
		return $oUser;
	}

	public function getUserByPublicId($iUserPublicId)
	{
		$aUsers = $this->oEavManager->getEntities('CUser', [], 0, 0, ['PublicId' => [$iUserPublicId, '=']], 'Name', \ESortOrder::ASC);
		if (count($aUsers) > 0)
		{
			return $aUsers[0];
		}
		return null;
	}

	/**
	 * Obtains list of information about users.
	 * @param int $iOffset
	 * @param int $iLimit
	 * @param string $sOrderBy = 'Email'. Field by which to sort.
	 * @param int $iOrderType = 0
	 * @param string $sSearchDesc = ''. If specified, the search goes on by substring in the name and email of default account.
	 * @param array $aFilters = []
	 * @return array | false
	 */
	public function getUserList($iOffset = 0, $iLimit = 0, $sOrderBy = 'Name', $iOrderType = \ESortOrder::ASC, $sSearchDesc = '', $aFilters = [])
	{
		$aResult = false;
		try
		{
			if ($sSearchDesc !== '')
			{
				$aFilters['Name'] = ['%'.$sSearchDesc.'%', 'LIKE'];
				if (count($aFilters) > 1)
				{
					$aFilters = ['$AND' => $aFilters];
				}
			}
				
			$aResult = $this->oEavManager->getEntities(
				'CUser', 
				array(
					'IsDisabled', 'LastLogin', 'Name', 'IdTenant'
				),
				$iOffset,
				$iLimit,
				$aFilters,
				$sOrderBy,
				$iOrderType
			);

		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$aResult = false;
			$this->setLastException($oException);
		}
		return $aResult;
	}

	/**
	 * Determines how many users are in particular tenant. Tenant identifier is used for look up.
	 * 
	 * @param int $iTenantId Tenant identifier.
	 * 
	 * @return int | false
	 */
	public function getUsersCountForTenant($iTenantId)
	{
		$mResult = false;
		try
		{
			$mResult = $this->oStorage->getUsersCountForTenant($iTenantId);
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $mResult;
	}

	/**
	 * Calculates total number of users registered in WebMail Pro.
	 * 
	 * @return int
	 */
	public function getTotalUsersCount()
	{
		$iResult = 0;
		try
		{
			$iResult = $this->oStorage->getTotalUsersCount();
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$this->setLastException($oException);
		}
		return $iResult;
	}
	
	/**
	 * @param CChannel $oChannel
	 *
	 * @return bool
	 */
	public function isExists(CUser $oUser)
	{
		$bResult = false;
		
		$oResult = $this->oEavManager->getEntity($oUser->EntityId);
				
		if ($oResult instanceof \CUser)
		{
			$bResult = true;
		}
		
//		try
//		{
//			$aResults = $this->oEavManager->getObjects(
//				'CUser',
//				array('Name'),
//				0,
//				0,
//				array('Name' => $oUser->Name)
//			);
//
//			if ($aResults)
//			{
//				foreach($aResults as $oObject)
//				{
//					if ($oObject->EntityId !== $oUser->EntityId)
//					{
//						$bResult = true;
//						break;
//					}
//				}
//			}
//		}
//		catch (\Aurora\System\Exceptions\BaseException $oException)
//		{
//			$this->setLastException($oException);
//		}
		return $bResult;
	}

    /**
     * @param CUser $oUser
     * @return bool
     * @internal param CChannel $oChannel
     */
	public function createUser (CUser &$oUser)
	{
		$bResult = false;
		try
		{
			if ($oUser->validate())
			{
				if (!$this->isExists($oUser))
				{
//					$oChannel->Password = md5($oChannel->Login.mt_rand(1000, 9000).microtime(true));
					
					if (!$this->oEavManager->saveEntity($oUser))
					{
						throw new \Aurora\System\Exceptions\ManagerException(Errs::UsersManager_UserCreateFailed);
					}
				}
				else
				{
					throw new \Aurora\System\Exceptions\ManagerException(Errs::UsersManager_UserAlreadyExists);
				}
			}

			$bResult = true;
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$bResult = false;
			$this->setLastException($oException);
		}

		return $bResult;
	}
	
	/**
	 * @param CChannel $oChannel
	 *
	 * @return bool
	 */
	public function updateUser (CUser &$oUser)
	{
		$bResult = false;
		try
		{
			if ($oUser->validate())
			{
//				if ($this->isExists($oUser))
//				{
//					$oChannel->Password = md5($oChannel->Login.mt_rand(1000, 9000).microtime(true));
					
					if (!$this->oEavManager->saveEntity($oUser))
					{
						throw new \Aurora\System\Exceptions\ManagerException(Errs::UsersManager_UserCreateFailed);
					}
//				}
//				else
//				{
//					throw new \Aurora\System\Exceptions\ManagerException(Errs::UsersManager_UserAlreadyExists);
//				}
			}

			$bResult = true;
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$bResult = false;
			$this->setLastException($oException);
		}

		return $bResult;
	}
	
	/**
	 * @param CUser $oUser
	 *
	 * @return bool
	 */
	public function deleteUser (CUser &$oUser)
	{
		$bResult = false;
		try
		{
//			if ($oUser->validate())
//			{
				if (!$this->oEavManager->deleteEntity($oUser->EntityId))
				{
					throw new \Aurora\System\Exceptions\ManagerException(Errs::UsersManager_UserDeleteFailed);
				}
//			}

			$bResult = true;
		}
		catch (\Aurora\System\Exceptions\BaseException $oException)
		{
			$bResult = false;
			$this->setLastException($oException);
		}

		return $bResult;
	}
}
