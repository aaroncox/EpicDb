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
class EpicDb_Auth_Resource_Post implements MW_Auth_Resource_Interface {
	protected $_public = true;
	public function __construct($public = true) {
		$this->_public = !!$public;
	}
	public function getResourceName()
	{
		return ($this->_public)?'post':'post-private';
	}

	public function getResourceDescription()
	{
		return "EpicDb Posts";
	}

	public function getParentResource()
	{
		return null;
	}

	public function getDefaultPrivileges()
	{
		$privs = array(
			array(
				 'mode' => true,
				 'role' => array(MW_Auth_Group_Super::getInstance()),
			)
		);
		if($this->_public) {
			$privs[] = array(
				'mode' => true,
				'role' => array(EpicDb_Auth_Group_Moderators::getInstance()),
			);
			$privs[] = array(
				'mode' => true,
				'role' => array(MW_Auth_Group_Guest::getInstance(), MW_Auth_Group_User::getInstance()),
				'privilege' => 'view'
			);
			$privs[] = array(
				'mode' => true,
				'role' => array(MW_Auth_Group_User::getInstance()),
				'privilege' => array('use', 'create'),
			);
		}
		return $privs;
	}

	public function getRuntimePrivileges() {

	}
} // END class