<?php

/**
 * @param string $sClassName
 *
 * @return mixed
 */

spl_autoload_register(function ($sClassName) {
	
	$aClassesTree = array(
		'system' => array(
			'Aurora\\System'
		)
	);
	//system目录下的类
	foreach ($aClassesTree as $sFolder => $aClasses)
	{
		foreach ($aClasses as $sClass)
		{
			if (0 === strpos($sClassName, $sClass) && false !== strpos($sClassName, '\\'))
			{
			    //获取system目录下的类的路径
				$sFileName = dirname(__DIR__) . '/' .$sFolder.'/'.str_replace('\\', '/', substr($sClassName, strlen($sClass) + 1)).'.php';
				if (file_exists($sFileName))
				{
					include_once $sFileName;
				}
			}
		}
	}

	//modules下面的类，include_once相应modules下面的类
	if (strpos($sClassName, 'Aurora\\Modules') !== false)
	{
		$sModuleClassName = substr($sClassName, strlen('Aurora\\Modules\\'));
		$sModuleName = substr($sModuleClassName, 0, -7);
		$sFileName = dirname(__DIR__) . '/modules/'.$sModuleName.'/Module.php';
		if (file_exists($sFileName))
		{
			include_once $sFileName;
		}
	}
});
