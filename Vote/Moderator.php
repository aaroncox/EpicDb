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
		if (!$this->_post instanceOf EpicDb_Vote_Interface_Flagable) return "This object can't be voted on";
	}

	public function cast()
	{
		if (!$this->reason) {
			$this->_error = "Tell us why you want us to look at it!";
			return false;
		}
		return parent::cast();
	}

}