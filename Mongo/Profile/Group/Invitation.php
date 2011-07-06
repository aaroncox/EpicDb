<?php
/**
 * R2Db_Mongo_Record_Skill
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Profile_Group_Invitation extends MW_Auth_Mongo_Resource_Document
{
	protected static $_collectionName = 'invitations';
  protected static $_documentType = 'invitation';
  
	protected $_requirements = array(
    'inviter' => array('Document:EpicDb_Mongo_Profile_User', 'AsReference'),
    'invitee' => array('Document:EpicDb_Mongo_Profile_User', 'AsReference'),	
		'group' => array('Document:EpicDb_Mongo_Profile_Group', 'AsReference'),	
	);
	
	public function process($response) {
		$group = $this->group;
		$invitee = $this->invitee;
		switch($response) {
			case 'accept':
				$group->setMembership($invitee, 'member');
				break;
			case 'decline':
				$group->setMembership($invitee, '');
				break;
		}
		$this->delete();
	}
} // END class R2Db_Mongo_Record_Skill