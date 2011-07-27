<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Form_Profile_Group_Application extends EpicDb_Form
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
		$this->addElement('textarea', 'reason', array(
			'label' => 'A message to the guild',
			'description' => 'This message will be attached to your request to join this group.'
		));
		$this->setButtons(array('join' => 'Apply to Join'));
		return $this;
	}
	public function save($data) {
		$app = EpicDb_Mongo::newDoc('application');
		$app->status = "open";
		$app->reason = $this->reason->getValue();
		$app->candidate = EpicDb_Auth::getInstance()->getUserProfile();
		$app->group = $this->getGroup();
		$app->save();
		return true;
	}
	public function process($data) {
		if($this->isValid($data)) {
			return $this->save($data);
		}
		return false;
	}
} // END class EpicDb_Form_Profile_Group_Application extends EpicDb_Form