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

namespace Aurora\System\Xml;

/**
 * @package Api
 */
class CXmlDomNode
{
	/**
	 * @var	string
	 */
	public $TagName;

	/**
	 * @var	string
	 */
	public $Value;

	/**
	 * @var	string
	 */
	public $Comment;

	/**
	 * @var	array
	 */
	public $Attributes;

	/**
	 * @var	array
	 */
	public $Children;

	/**
	 * @param string $sTagName
	 * @param string $sValue = null
	 * @param bool $bIsCDATA = false
	 * @param bool $bIsSimpleCharsCode = false
	 * @param string $sNodeComment = ''
	 */
	public function __construct($sTagName, $sValue = null, $bIsCDATA = false, $bIsSimpleCharsCode = false, $sNodeComment = '')
	{
		$this->Attributes = array();
		$this->Children = array();

		$this->TagName = $sTagName;
		$this->Value = ($bIsCDATA && null !== $sValue)
			? '<![CDATA['.
				(($bIsSimpleCharsCode) ?
					\Aurora\System\Utils::EncodeSimpleSpecialXmlChars($sValue) : \Aurora\System\Utils::EncodeSpecialXmlChars($sValue))
			.']]>' : $sValue;

		$this->Comment = $sNodeComment;
	}

	/**
	 * @param CXmlDomNode &$oNode
	 */
	public function AppendChild(&$oNode)
	{
		if ($oNode)
		{
			$this->Children[] =& $oNode;
		}
	}

	/**
	 * @param CXmlDomNode &$oNode
	 */
	public function PrependChild(&$oNode)
	{
		if ($oNode)
		{
			array_unshift($this->Children, $oNode);
		}
	}

	/**
	 * @param string $sName
	 * @param string $sValue
	 */
	public function AppendAttribute($sName, $sValue)
	{
		$this->Attributes[$sName] = $sValue;
	}

	/**
	 * @param string $sTagName
	 * @return &CXmlDomNode
	 */
	public function &GetChildNodeByTagName($sTagName)
	{
		$iNodeKey = null;
		$oCXmlDomNode = null;
		$aNodeKeys = array_keys($this->Children);
		foreach ($aNodeKeys as $iNodeKey)
		{
			if ($this->Children[$iNodeKey] && $this->Children[$iNodeKey]->TagName === $sTagName)
			{
				$oCXmlDomNode =& $this->Children[$iNodeKey];
				break;
			}
		}
		return $oCXmlDomNode;
	}

	/**
	 * @param string $sTagName
	 * @return string
	 */
	public function GetChildValueByTagName($sTagName)
	{
		$sResult = '';
		$oNode =& $this->GetChildNodeByTagName($sTagName);
		if (null !== $oNode)
		{
			$sResult = \Aurora\System\Utils::DecodeSpecialXmlChars($oNode->Value);
		}
		return $sResult;
	}

	/**
	 * @param bool $bSplitLines = false
	 * @return string
	 */
	public function ToString($bSplitLines = false)
	{
		$sAttributes = '';
		foreach ($this->Attributes as $sName => $sValue)
		{
			$sName = htmlspecialchars($sName);
			$sValue = htmlspecialchars($sValue);
			$sAttributes .= ' '.$sName.'="'.$sValue.'"';
		}

		$sChilds = '';
		$iKeyIndex = null;
		if (0 < count($this->Children))
		{
			foreach (array_keys($this->Children) as $iKeyIndex)
			{
				$sChilds .= $this->Children[$iKeyIndex]->ToString($bSplitLines);
				if ($bSplitLines)
				{
					$sChilds .= "\r\n";
				}
			}

			if ($bSplitLines)
			{
				$aLines = explode("\r\n", $sChilds);
				$sChilds = '';
				foreach ($aLines as $sLine)
				{
					$sChilds .= ($sLine !== '') ? sprintf("\t%s\r\n", $sLine) : '';
				}
			}
		}

		$sCommentPart = (empty($this->Comment)) ? '' : "<!-- ".$this->Comment." -->\r\n";

		if ($sChilds === '' && null === $this->Value)
		{
			$sOutStr = sprintf('<%s%s />', $this->TagName, $sAttributes);
			if ($bSplitLines)
			{
				$sOutStr .= "\r\n";
			}

			return $sCommentPart.$sOutStr;
		}

		$sValue = (null !== $this->Value) ? trim($this->Value) : '';

		if ($bSplitLines)
		{
			if ($sValue !== '' && $sChilds === '')
			{
				return $sCommentPart.sprintf('<%s%s>%s</%s>', $this->TagName, $sAttributes, $sValue, $this->TagName);
			}
			if ($sValue === '' && $sChilds === '' )
			{
				return $sCommentPart.sprintf('<%s%s />', $this->TagName, $sAttributes);
			}

			return $sCommentPart.sprintf("<%s%s>%s\r\n%s</%s>\r\n", $this->TagName, $sAttributes, $sValue, $sChilds, $this->TagName);
		}

		return $sCommentPart.sprintf('<%s%s>%s%s</%s>', $this->TagName, $sAttributes, $sValue, $sChilds, $this->TagName);
	}

	/**
	 * @param string $sName
	 * @param string $sDefault = null
	 * @return string
	 */
	public function GetAttribute($sName, $sDefault = null)
	{
		return isset($this->Attributes[$sName]) ? \Aurora\System\Utils::DecodeSpecialXmlChars($this->Attributes[$sName]) : $sDefault;
	}
}
