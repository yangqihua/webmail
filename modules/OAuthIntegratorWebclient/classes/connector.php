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
 *
 * @package Classes
 * @subpackage AuthIntegrator
 */
class COAuthIntegratorConnector
{
	protected $Name = 'connector';
	public static $Debug = false;
	public static $Scopes = array();
	
	public $oModule = null;

	public function __construct($oModule) 
	{
		$this->oModule = $oModule;
	}
	
	public function Init($sId, $sSecret, $sScope = '') 
	{
	}
}