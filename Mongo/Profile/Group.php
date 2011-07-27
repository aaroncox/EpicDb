<?php
/**
 * EpicDb_Mongo_Profile_Group
 *
 * undocumented class
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Profile_Group extends EpicDb_Mongo_Profile
{
	public $summaryHelper = 'groupSummary';
	public $contextHelper = 'groupContext';
	protected static $_documentType = 'group';
	protected static $_editForm = 'EpicDb_Form_Profile_Group';

	 /**
	 * The Privileges granted to group members
	 *
	 * @var array
	 **/
	protected $_memberPrivileges = array("view", "post");

	// override requirements
	public function __construct($data = array(), $config = array()) {
		$this->addRequirements(array(
			'_owner' => array('Document:MW_Auth_Mongo_Role', 'AsReference', 'Required'),
			'_memberRole' => array('Document:MW_Auth_Mongo_Role', 'AsReference'),
			'_adminRole' => array('Document:MW_Auth_Mongo_Role', 'AsReference'),
			'admins' => array('DocumentSet'),
			'admins.$' => array('Document:EpicDb_Mongo_Profile', 'AsReference'),
			'admins.$.user' => array('Document:MW_Auth_Mongo_Role', 'AsReference'),
			'members' => array('DocumentSet'),
			'members.$' => array('Document:EpicDb_Mongo_Profile', 'AsReference'),
			'members.$.user' => array('Document:MW_Auth_Mongo_Role', 'AsReference'),
		));
		return parent::__construct( $data, $config );
	}

	/**
	 * Helper functions - isAdmin() and isMember() check the currently logged in user
	 *
	 * @return boolean
	 * @author Corey Frang
	 **/
	public function isAdmin($user = null) {
		if( !$user ) {
			$user = MW_Auth::getInstance()->getUser();
		}
		if ( !$user ) {
			return false;
		}
		return $user->isMember( $this->getAdminRole() );
	}
	public function isMember($user = null) {
		if( !$user ) {
			$user = MW_Auth::getInstance()->getUser();
		}
		if( !$user ) {
			return false;
		}
		return $user->isMember( $this->getMemberRole() );
	}

	/**
	 * getAdminRole - Returns the MW_Auth_Mongo_Role that represents the "admins"
	 *
	 * @return MW_Auth_Mongo_Role
	 * @author Corey Frang
	 **/
	public function getAdminRole()
	{
		if ( isset( $this->_adminRole ) ) {
			return $this->_adminRole;
		}
		$data = array(
			'type' => 'group',
			'groupName' => $this->name." admins",
			'groupDescription' => "Group administrators for group ".$this->id.": ".$this->name,
		);
		$role = new MW_Auth_Mongo_Role();
		foreach ($data as $k=>$v) $role->$k = $v;

		// The Admin role is a member of the Members Role
		$role->roles->addDocument( $this->getMemberRole() );
		$role->save();

		// Grant all privileges on this item to the Admins
		$this->grant( $role );

		foreach( $this->admins as $admin ) {
			$admin = 
			$user = $admin->user;
			if ( $user ) {
				$user->addGroup( $role );
				$user->save();
			}
		}

		// Save this new role onto the record
		$this->_adminRole = $role;
		$this->save();
		return $role;
	}

	/**
	 * getMemberRole() - Returns the MW_Auth_Mongo_Role that represents the "members"
	 *
	 * @return void
	 * @author Corey Frang
	 **/
	public function getMemberRole() {
		if ( isset( $this->_memberRole ) ) {
			return $this->_memberRole;
		}
		$data = array(
			"type" => "group",
			"groupName" => $this->name." members",
			"groupDescription" => "Group Members for the group ".$this->name,
		);
		$role = new MW_Auth_Mongo_Role();
		foreach ($data as $k=>$v) $role->$k = $v;

		$role->save();
		// Grant the member role privileges on this object
		$this->grant($role, $this->_memberPrivileges);

		// Retroactively grant membership to the members in the members documentset
		foreach( $this->members as $member ) {
			$user = $member->user;
			if( $user ) {
				$user->addGroup($role);
				$user->save();
			}
		}

		// Save this new role onto the record
		$this->_memberRole = $role;
		$this->save();
		return $role;
	}

	public function setMembership(EpicDb_Mongo_Profile $profile, $type)
	{
		$validTypes = array('member', 'admin', '');
		if (!in_array($type, $validTypes)) {
			throw new Exception('Unknown User Type, must be "member", "admin", or "" to remove');
		}

		$user = $profile->user;
		$updateQuery = array();

		foreach ($validTypes as $checkType) {
			if (!$checkType) continue;
			$typeSetKey = $checkType.'s';
			$set = $this->$typeSetKey;

			if ($checkType == $type) {
				$updateQuery['$addToSet'][$typeSetKey] = $profile->createReference();
				$user->addGroup($this->getRole($checkType));
			} else {
				$updateQuery['$pull'][$typeSetKey] = $profile->createReference();
				$user->removeGroup($this->getRole($checkType));
			}
		}
		$user->save();
		self::update(array('_id' => $this->_id), $updateQuery);
		return true;
	}

	/**
	 * shortcut function to get admin/member role
	 *
	 * @return MW_Auth_Mongo_Role
	 * @author Corey Frang
	 **/
	public function getRole($type)
	{
		if ($type == 'admin') return $this->getAdminRole();
		if ($type == 'member') return $this->getMemberRole();
		throw new Exception('Unknown Role Type');
	}
	
	public function getApplicationForm() {
		return new EpicDb_Form_Profile_Group_Application(array("group" => $this));
	}
	
	public function hasApplied($profile) {
		$query = array(
			'group' => $this->createReference(),
			'candidate' => $profile->createReference(),
		);
		return (EpicDb_Mongo::db('application')->fetchOne($query))?true:false;
	}

} // END class EpicDb_Mongo_Profile_Group