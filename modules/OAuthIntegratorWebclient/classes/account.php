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
 * @property int $Id
 * @property int $IdUser
 * @property string $IdSocial
 * @property string $Type
 * @property string $Name
 * @property string $Email
 * @property string $AccessToken
 * @property string $RefreshToken
 * @property string $Scopes
 * @property bool $Disabled
 *
 * @package Classes
 * @subpackage Social
 */
class COAuthAccount extends \Aurora\System\EAV\Entity
{
	protected $aStaticMap = array(
		'IdUser'		=> array('int', 0),
		'IdSocial'		=> array('string', ''),
		'Type'			=> array('string', ''),
		'Name'			=> array('string', ''),
		'Email'			=> array('string', ''),
		'AccessToken'	=> array('text', ''),
		'RefreshToken'	=> array('string', ''),
		'Scopes'		=> array('string', ''),
		'Disabled'		=> array('bool', false)
	);	

	public function getScopesAsArray()
	{
		$aResult = array();
		if (!$this->Disabled)
		{
			$aResult = array_map(function($sValue) {
					return strtolower($sValue);
				}, explode(' ', $this->Scopes)	
			);	
		}
		
		return $aResult;
	}
	
	/**
	 * @param string $sScope
	 *
	 * @return bool
	 */
	public function issetScope($sScope)
	{
		return /*'' === $this->Scopes || */false !== strpos(strtolower($this->Scopes), strtolower($sScope));
	}	
	
	/**
	 * @param string $sScope
	 */
	public function setScope($sScope)
	{
		$aScopes = $this->getScopesAsArray();
		if (!array_search($sScope, array_unique($aScopes)))
		{
			$aScopes[] = $sScope;
			$this->Scopes = implode(' ', array_unique($aScopes));
		}
	}	
	
	/**
	 * @param array $aScopes
	 */
	public function setScopes($aScopes)
	{
		$this->Scopes = implode(' ', array_unique(array_merge($aScopes, $this->getScopesAsArray())));
	}	

	/**
	 * @param string $sScope
	 */
	public function unsetScope($sScope)
	{
		$aScopes = array_map(function($sValue) {
				return strtolower($sValue);
			}, explode(' ', $this->Scopes)	
		);
		$mResult = array_search($sScope, $aScopes);
		if ($mResult !== false)
		{
			unset($aScopes[$mResult]);
			$this->Scopes = implode(' ', $aScopes);
		}
	}	
	
	public function toResponseArray()
	{
		return $this->toArray();
	}
}
