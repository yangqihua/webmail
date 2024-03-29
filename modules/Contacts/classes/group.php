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
 * @property int $IdUser
 * @property string $Name
 * @property bool $IsOrganization
 * @property string $Email
 * @property string $Company
 * @property string $Street
 * @property string $City
 * @property string $State
 * @property string $Zip
 * @property string $Country
 * @property string $Phone
 * @property string $Fax
 * @property string $Web
 * @property array $Events
 *
 * @ignore
 * @package Contactsmain
 * @subpackage Classes
 */
class CGroup extends \Aurora\System\EAV\Entity
{
	public $Events = array();
	
	public $GroupContacts = array();
	
	protected $aStaticMap = array(
		'IdUser'			=> array('int', 0),

		'Name'				=> array('string', ''),
		'IsOrganization'	=> array('bool', false),

		'Email'				=> array('string', ''),
		'Company'			=> array('string', ''),
		'Street'			=> array('string', ''),
		'City'				=> array('string', ''),
		'State'				=> array('string', ''),
		'Zip'				=> array('string', ''),
		'Country'			=> array('string', ''),
		'Phone'				=> array('string', ''),
		'Fax'				=> array('string', ''),
		'Web'				=> array('string', ''),
		'Events'			=> array('string', ''),
	);

	public function populate($aGroup)
	{
		parent::populate($aGroup);
		
		$this->GroupContacts = array();
		if (isset($aGroup['Contacts']) && is_array($aGroup['Contacts']))
		{
			$aContactUUIDs = $aGroup['Contacts'];
			foreach ($aContactUUIDs as $sContactUUID)
			{
				$oGroupContact = \CGroupContact::createInstance('CGroupContact', $this->getModule());
				$oGroupContact->ContactUUID = $sContactUUID;
				$this->GroupContacts[] = $oGroupContact;
			}
		}
	}

	public function toResponseArray()
	{
		$aRes = parent::toResponseArray();

		$oContactsModule = \Aurora\System\Api::GetModule('Contacts');
		if ($oContactsModule)
		{
			$aContacts = $oContactsModule->oApiContactsManager->getContacts(
				\EContactSortField::Name, \ESortOrder::ASC, 0, 299, [], $this->UUID
			);

			$aRes['Contacts'] = \Aurora\System\Managers\Response::GetResponseObject($aContacts);
		}

		return $aRes;
	}
}
