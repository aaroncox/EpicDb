<?php
/**
 * 
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 */

/**
 *  EpicDb_Vote_Offensive
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 * @version $Id:$
 */
class EpicDb_Vote_Offensive extends EpicDb_Vote_Abstract {
	protected $_type = EpicDb_Vote::OFFENSIVE;
	// you could do something here like if the user is a moderator, it would flag it as offensive and result in a -100 rep like SE does....
	public function isDisabled()
	{
		if (!$this->_post instanceOf EpicDb_Vote_Interface_Flagable) return "This object can't be voted on";
	}
	
}