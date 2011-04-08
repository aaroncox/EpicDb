<?php
/**
 * 
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 */

/**
 *  EpicDb_Vote_Favorite
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 * @version $Id:$
 */
class EpicDb_Vote_Favorite extends EpicDb_Vote_Abstract {
	protected $_type = EpicDb_Vote::FAVORITE;
	// Stars!
	// Perhaps you could make it write a post here, or store a cached copy of the users favorites on their profile?
	
	public function isDisabled()
	{
		if (!$this->_post instanceOf EpicDb_Vote_Interface_Votable) return "This object can't be voted on";
	}
}