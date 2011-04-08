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
		if (!$this->_post instanceOf EpicDb_Interface_Votable) return "This object can't be voted on";
	}

}