<?php
/**
 *
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @copyright Copyright (c) 2010 Momentum Workshop, Inc
 */

/**
 *  EpicDb_Vote_Down
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @copyright Copyright (c) 2010 Momentum Workshop, Inc
 * @version $Id: Down.php 422 2010-12-16 00:13:24Z root $
 */
class EpicDb_Vote_Down extends EpicDb_Vote_Abstract {
	protected $_type = EpicDb_Vote::DOWN;

	public function cast()
	{
		// check for an existing downvote first
		$upVote = new EpicDb_Vote_Up($this->_userProfile, $this->_post);
		if ($upVote->hasCast()) {
			if ($upVote->date < mktime() - 60*60*2) {
				$this->_error = "You can no longer change your vote";
				return false;
			}
			$upVote->uncast();
		}

		return parent::cast();
	}

	public function isDisabled()
	{
		if ((!$this->_post instanceOf EpicDb_Vote_Interface_Votable) ||
				($this->_post instanceOf EpicDb_Vote_Interface_UpOnly) ) return "This object can't be down-voted";

		if ( !EpicDb_Auth::getInstance()->hasPrivilege( new EpicDb_Auth_Resource_RepVotes(), "down" ) ) {
			return "You are not high enough level to vote this post down";
		}
			
		$author = $this->_post->tags->getTag('author');
		if ($author && $author->createReference() == $this->_userProfile->createReference()) {
			return "You can not vote on your own post";
		}
	}

	protected function _postCast()
	{
		if (!$this->_post->isReputationDisabled()) {
			$this->giveReputationToTarget(-5);
			$this->giveReputationToVoter(-2);			
		}
		parent::_postCast();
	}

}