<?php
/**
 *
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 */

/**
 *  EpicDb_Vote_Spam
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 * @version $Id:$
 */
class EpicDb_Vote_Spam extends EpicDb_Vote_Abstract {
	protected $_type = EpicDb_Vote::SPAM;
	public function isDisabled()
	{
		if (!$this->_post instanceOf EpicDb_Vote_Interface_Flaggable) return "This object can't be voted on";
		if ( !EpicDb_Auth::getInstance()->hasPrivilege( new EpicDb_Auth_Resource_Vote, "flag" ) ) return "You aren't high enough level to flag";
	}

}