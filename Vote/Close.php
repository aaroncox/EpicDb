<?php
/**
 * 
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 */

/**
 *  EpicDb_Vote_Close
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 * @version $Id:$
 */
class EpicDb_Vote_Close extends EpicDb_Vote_Abstract {
	protected $_type = EpicDb_Vote::CLOSE;
  // closable votes
	public function isDisabled()
	{
		if (!$this->_post instanceOf EpicDb_Vote_Interface_Votable) return "This object can't be voted on";
	}

}