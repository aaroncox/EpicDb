<?php
/**
 * R2Db_Mongo_Record_Skill
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Profile_Group_Application extends MW_Auth_Mongo_Resource_Document
{
	protected static $_collectionName = 'applications';
  protected static $_documentType = 'application';
  
	protected $_requirements = array(
    'candidate' => array('Document:EpicDb_Mongo_Profile_User', 'AsReference'),
		'group' => array('Document:EpicDb_Mongo_Profile_Group', 'AsReference'),	
	);
	
	public function getPropertyClass($property, $data) {
		if ($property == "group" && isset($data['_type'])) {
			return EpicDb_Mongo::dbClass($data['_type']);
		}
	}
	
	public function accept() {
		$this->status = "accepted";
		$this->save();
	}

	public function reject() {
		$this->status = "rejected";
		$this->save();
	}
	
} // END class R2Db_Mongo_Record_Skill