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

namespace Aurora\System\Net\Protocols;

/**
 * @package Api
 * @subpackage Net
 */

class Poppassd extends \Aurora\System\Net\AbstractProtocol
{
	/**
	 * @return bool
	 */
	public function Connect()
	{
		$bResult = false;
		if (parent::Connect())
		{
			$bResult = $this->CheckResponse($this->GetNextLine(), 0);
		}
		return $bResult;
	}

	/**
	 * @param string $sLogin
	 * @param string $sPassword
	 * @return bool
	 */
	public function Login($sLogin, $sPassword)
	{
		return $this->SendCommand('user '.$sLogin, array(), 1) && $this->SendCommand('pass '.$sPassword, array($sPassword), 1);
	}

	/**
	 * @param string $sLogin
	 * @param string $sPassword
	 * @return bool
	 */
	public function ConnectAndLogin($sLogin, $sPassword)
	{
		return $this->Connect() && $this->Login($sLogin, $sPassword);
	}

	/**
	 * @return bool
	 */
	public function Disconnect()
	{
		return parent::Disconnect();
	}

	/**
	 * @return bool
	 */
	public function Logout()
	{
		return $this->SendCommand('quit', array(), 0);
	}

	/**
	 * @return bool
	 */
	public function LogoutAndDisconnect()
	{
		return $this->Logout() && $this->Disconnect();
	}

	/**
	 * @param string $sNewPassword
	 * @return bool
	 */
	public function NewPass($sNewPassword)
	{
		return $this->SendCommand('newpass '.$sNewPassword, array($sNewPassword), 0);
	}

	/**
	 * @param string $sCmd
	 * @return bool
	 */
	public function SendLine($sCmd)
	{
		return $this->WriteLine($sCmd);
	}

	/**
	 * @param string $sCmd
	 * @param array $aHideValues = array()
	 * @param int $iCheckType = 0
	 * @return bool
	 */
	public function SendCommand($sCmd, $aHideValues = array(), $iCheckType = 0)
	{
		if ($this->WriteLine($sCmd, $aHideValues))
		{
			return $this->CheckResponse($this->GetNextLine(), $iCheckType);
		}

		return false;
	}

	/**
	 * @return string
	 */
	public function GetNextLine()
	{
		return $this->ReadLine();
	}

	/**
	 * @param string $sResponse
	 * @param int $iCheckType = 0
	 * @return bool
	 */
	public function CheckResponse($sResponse, $iCheckType = 0)
	{
		switch ($iCheckType)
		{
			case 0:
				return (bool) preg_match('/^2\d\d/', $sResponse);
				break;
			case 1:
				return (bool) preg_match('/^[23]\d\d/', $sResponse);
				break;
		}

		return false;
	}
}
