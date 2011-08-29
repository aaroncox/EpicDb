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
	
	protected $_requirements = array(
		'faction' => array('Document:R2Db_Mongo_Record_Faction', 'AsReference'),
	);
	
	public function getParentResource() {
		return new EpicDb_Auth_Resource_Profile();
	}
	
	public function getIcon() {
		if($this->icon) {
			return $this->icon;
		}
		return "http://s3.r2-db.com/unknown.jpg";
	}
	
}