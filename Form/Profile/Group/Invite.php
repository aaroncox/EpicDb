<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Form_Profile_Group_Invite extends EpicDb_Form
{
	private $_group = null;
	public function setGroup($group) {
		if(!$group instanceOf EpicDb_Mongo_Profile_Group) throw new Exception("This profile isn't an instance of a group.");
		$this->_group = $group;
	}
	public function getGroup() {
		if($this->_group) return $this->_group;
		throw new Exception("No Group has been set on this form.");
	}
	
	public function init() {
		parent::init();
		$group = $this->getGroup();
		$this->addElement('tags', 'users', array(
			'label' => 'User Search',
			'recordType' => 'user',
		));
		$this->setButtons(array('invite' => 'Invite'));
		return $this;
	}
	public function save($data) {
		$filter = new EpicDb_Filter_TagJSON();
		$users = $filter->toArray($data['users']);
		$group = $this->getGroup();
		foreach($users as $user) {
			$query = array(
				'profile' => $user->createReference(),
				'group' => $group->createReference(),
			);
			// Check to see if they are a member...
			if(in_array($user->createReference(), $group->members->export())) throw new Exception("User is already a member, aborting.");
			if(in_array($user->createReference(), $group->admins->export())) throw new Exception("User is already an admin, aborting.");
			// Check for an existing invite...
			$invite = EpicDb_Mongo::db('invitation')->fetchOne($query);
			if($invite) throw new Exception("User already has an outstanding invite.");
			// Create new invitation
			$invite = EpicDb_Mongo::newDoc('invitation');
			$invite->group = $group;
			$invite->invitee = $user;
			$invite->inviter = EpicDb_Auth::getInstance()->getUserProfile();
			$invite->save();
		}
		return true;
	}
	public function process($data) {
		if($this->isValid($data)) {
			return $this->save($data);
		}
		return false;
	}
} // END class EpicDb_Form_Profile_Group_Invite extends EpicDb_Form