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

	public function isDisabled()
	{
		if (!$this->_post instanceOf EpicDb_Vote_Interface_Closable) return "This object can't be voted on";
		if ($this->_post->closed) return "Post already closed";
		if (!MW_Auth::getInstance()->hasPrivilege(new EpicDb_Auth_Resource_Moderator)) return "Only moderators can close";
	}

	public function setDupeOf( $post )
	{
		if ( $post ) {
			$this->dupeOf = $post->createReference();
		} else {
			unset($this->dupeOf);
		}
	}

	public function getDupeOf()
	{
		if ( $this->dupeOf ) {
			return EpicDb_Mongo::resolveReference( $this->dupeOf );
		}
	}

	public function cast()
	{
		if (!$this->reason) {
			$this->_error = "Closing requires a reason";
			return false;
		}
		if ($this->reason == "exact duplicate") {
			if ( !$this->getDupeOf() ) {
				$this->_error = "Must reference a post";
				return false;
			}
		}
		return parent::cast();
	}
	protected function _postCast()
	{
		$this->_post->close( array( $this->voter ), $this->reason, $this->getDupeOf() );
		parent::_postCast();
	}

}