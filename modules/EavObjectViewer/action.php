<?php
/**
 * @copyright Copyright (c) 2017, Afterlogic Corp.
 * @license AGPL-3.0 or AfterLogic Software License
 *
 * This code is licensed under AGPLv3 license or AfterLogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

require_once "../../system/autoload.php";
\Aurora\System\Api::Init(true);

$response = array(
	'error' => true,
	'message' => 'Unknown error occours',
	'result' => array()
);

$oManagerApi = \Aurora\System\Api::GetSystemManager('eav', 'db');

if (isset($_POST['action']))
{
	switch ($_POST['action'])
	{
		case 'types':
			$response = [
				'error' => false,
				'message'=> '',
				'result' => $oManagerApi->getTypes()
			];			
			break;
		case 'edit':
			if ($_POST['ObjectName'])
			{
				$sObjectType = $_POST['ObjectName'];
				
				$oObject = \Aurora\System\EAV\Entity::createInstance($sObjectType);
				
				$aMap = $oObject->getMap();
				
				$aViewProperties = array_keys($aMap);

				if ($_POST['EntityId'])
				{
					$oObject->EntityId = (int)$_POST['EntityId'];

					foreach ($aViewProperties as $property)
					{
						if ($_POST[$property])
						{
							$oObject->{$property} = $_POST[$property];
						}
					}
				}
				
				if (isset($_POST['@DisabledModules'])) 
				{
					$oObject->{'@DisabledModules'} = $_POST['@DisabledModules'];
				}

				$result = $oManagerApi->saveEntity($oObject);

				if ($result) 
				{
					$response['error'] = false;
					$response['message'] = '';
				}
			}
			break;

		case 'delete':
			$oManagerApi->deleteEntity($_POST['EntityId']);
			break;
		
		case 'delete_multiple':
			if ($_POST['ids'])
			{
				$aIds = explode(',', $_POST['ids']);
			}
			foreach ($aIds as $id) 
			{
				if (!$oManagerApi->deleteEntity((int)$id))
				{
					$result = false;
				}
			}

			if ($result) 
			{
				$response['error'] = false;
				$response['message'] = '';
			}
			break;
			
		case 'list':
			if (isset($_POST['ObjectName']))
			{
				$aResultItems = array();

				$aFilters = array();
				if (isset($_POST['searchField'], $_POST['searchText']))
				{
					$aFilters = [$_POST['searchField'] => ['%'.$_POST['searchText'].'%', 'LIKE']];
				}
				
				$aItems = $oManagerApi->getEntities($_POST['ObjectName'], array(), 0, 0, $aFilters);
				if (is_array($aItems))
				{
					foreach ($aItems as $oItem)
					{
						$itemData = $oItem->toArray();
						foreach ($itemData as $sKey => $mValue)
						{
							$sType = 'string';
							$oAttribute = $oItem->getAttribute($sKey);
							if ($oAttribute)
							{
								$sType = $oAttribute->Type;
							}
							$aResultItems['Fields'][$sKey] = $sType;
						}
						if ($_POST['ObjectName'] == 'CAccount') 
						{
							$itemData['Password'] = htmlspecialchars($itemData['Password']);
						}
						$aResultItems['Values'][] = $itemData;
					}

					$response = [
						'error' => false,
						'message'=> '',
						'result' => $aResultItems
					];
				}
			}
			break;
	}
}


echo json_encode($response, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
