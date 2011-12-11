<?php
/**
 *
 *
 * undocumented class
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Auth_Resource_Tag implements MW_Auth_Resource_Interface {
	public function getResourceName()
	{
		return 'tag';
	}

	public function getResourceDescription()
	{
		return "EpicDb Tags";
	}

	public function getParentResource()
	{
		return new EpicDb_Auth_Resource_Record;
	}

	public function getDefaultPrivileges()
	{
		return array(
			array(
				'mode' => true,
				'role' => array( EpicDb_Auth_Group_TagCreators::getInstance() ),
				'privilege' => 'create',
			),
		);
	}

	public function getRuntimePrivileges() {

	}
} // END class