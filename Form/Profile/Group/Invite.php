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
			$group->invite($user);
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