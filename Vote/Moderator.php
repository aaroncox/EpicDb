<?php
/**
 * 
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 */

/**
 *  EpicDb_Vote_Moderator
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 * @version $Id:$
 */
class EpicDb_Vote_Moderator extends EpicDb_Vote_Abstract {
	protected $_type = EpicDb_Vote::MODERATOR;
	public function isDisabled()
	{
		if (!$this->_post instanceOf EpicDb_Vote_Interface_Flaggable) return "This object can't be voted on";
		if ( !EpicDb_Auth::getInstance()->hasPrivilege( new EpicDb_Auth_Resource_Vote, "flag" ) ) return "You aren't high enough level to flag";
	}

	public function cast()
	{
		if (!$this->reason) {
			$this->_error = "Tell us why you want us to look at it!";
			return false;
		}
		return parent::cast();
	}
	
	public function init() {
		$this->_data->addRequirements(array('acknowledgedBy' => array('Document:EpicDb_Mongo_Profile_User', 'AsReference')));
	}

}