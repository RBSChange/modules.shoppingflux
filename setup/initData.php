<?php
/**
 * @package modules.shoppingflux.setup
 */
class shoppingflux_Setup extends object_InitDataSetup
{
	public function install()
	{
		$this->executeModuleScript('init.xml');
	}

	/**
	 * @return String[]
	 */
	public function getRequiredPackages()
	{
		return array('modules_productexporter');
	}
}