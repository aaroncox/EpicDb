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
class EpicDb_Auth_Resource_QAPost implements MW_Auth_Resource_Interface {
	protected $_community = false;
	public function __construct($community = false) {
		$this->_community = !!$community;
	}
	public function getResourceName()
	{
		return ($this->_community)?'qa-community':'qa';
	}

	public function getResourceDescription()
	{
		return "EpicDb Q&A Posts";
	}

	public function getParentResource()
	{
		return new EpicDb_Auth_Resource_Post;
	}

	public function getDefaultPrivileges()
	{
		if ( $this->_community ) {
			$privs = array(
				array(
					'mode' => true,
					'privilege' => 'edit',
					'role' => array(EpicDb_Auth_Group_CommunityEditors::getInstance())
				)
			);
		} else {
			$privs = array(
				array(
					'mode' => true,
					'privilege' => 'edit',
					'role' => array(EpicDb_Auth_Group_QAEditors::getInstance())
				)
			);
		}
		return $privs;
	}

	public function getRuntimePrivileges() {

	}
} // END class