<?php
/**
 * EpicDb_Mongo_Record_Group_Website
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Profile_Group_Guild extends EpicDb_Mongo_Profile_Group
{
	public $summaryHelper = 'guildSummary';
	
	protected static $_documentType = "guild";
	protected static $_editForm = 'EpicDb_Form_Profile_Group_Guild';
	
	public function getParentResource() {
		return new EpicDb_Auth_Resource_Profile();
	}
	
	public function getIcon() {
		if($this->logo) {
			return $this->logo;
		}
		if($this->icon) {
			return $this->icon;
		}
		return "/images/icons/unknown.jpg";
	}
	
}