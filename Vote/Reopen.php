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
class EpicDb_Vote_Reopen extends EpicDb_Vote_Abstract {
	protected $_type = EpicDb_Vote::CLOSE;

	public function isDisabled()
	{
		if (!$this->_post instanceOf EpicDb_Vote_Interface_Closable) return "This object can't be voted on";
		if (!$this->_post->closed) return "Post not closed";
		if (!MW_Auth::getInstance()->hasPrivilege(new EpicDb_Auth_Resource_Moderator)) return "Only moderators can close";
	}

	protected function _postCast()
	{
		$this->_post->reopen( array( $this->voter ) );
		parent::_postCast();
	}

}