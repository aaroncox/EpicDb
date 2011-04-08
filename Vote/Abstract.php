<?php
/**
 *
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @copyright Copyright (c) 2010 Momentum Workshop, Inc
 */

/**
 *  EpicDb_Vote_Abstract
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @copyright Copyright (c) 2010 Momentum Workshop, Inc
 * @version $Id: Abstract.php 427 2010-12-16 02:15:55Z root $
 */
abstract class EpicDb_Vote_Abstract {
	/**
	 * Stores the error state
	 *
	 * @var string
	 **/
	protected $_error;

	/**
	 * The actual mongo data for the vote
	 *
	 * @var EpicDb_Mongo_Vote
	 **/
	protected $_data;

	/**
	 * The users profile - recored as the "vote caster"
	 *
	 * @var EpicDb_Mongo_Profile_User
	 **/
	protected $_userProfile;

	/**
	 * The post - The thing being voted on - could eventually be changed to a more
	 * generic "target" that is a EpicDb_Vote_Votable maybe?
	 *
	 * @var EpicDb_Mongo_Post
	 **/
	protected $_post;

	/**
	 * when set to true - all normal calculations are done, but user reputation is NOT updated
	 *
	 * @var boolean
	 **/
	protected $_importMode = false;

	/**
	 * The type of Vote - Override in the class.
	 *
	 * @var string
	 **/
	protected $_type = false;

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Corey Frang
	 **/
	public function __construct(EpicDb_Mongo_Profile_User $user, EpicDb_Mongo_Post $post, EpicDb_Mongo_Vote $data = null)
	{
		if (!$this->_type) throw new Exception("Undefined Vote Type");
		$this->_userProfile = $user;
		$this->_post = $post;
		if ($data) {
			$this->_data = $data;
		} else {
			/// getVote should return an existing record in the db - OR - make one
			$this->_data = EpicDb_Mongo::db('vote')->getVote($user, $post, $this->_type);
		}
		$this->init();
	}

	/**
	 * override for your own dastardly purposes - called as last operation in constructor
	 *
	 * @return void
	 * @author Corey Frang
	 **/
	public function init()
	{
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Corey Frang
	 **/
	public function setImportMode($bool)
	{
		$this->_importMode = $bool;
	}

	/**
	 * Causes the user to cast this vote.  Returns true if succesful, false if otherwise
	 *
	 * @return boolean
	 * @author Corey Frang
	 **/
	public function cast()
	{
		if ($this->_error = $this->isDisabled()) return false;
		if ($this->hasCast()) {
			$this->_error = "You have already cast this vote";
			return false;
		}

		$this->_data->date = $this->_importMode ?: mktime();
		$result = $this->_data->save();
		if (!$result) {
			$this->_error = "Couldn't Save Vote";
			return false;
		}
		$this->_postCast();
		return true;
	}

	/**
	 * undoes the vote
	 *
	 * @return void
	 * @author Corey Frang
	 **/
	public function uncast()
	{
		if ($this->_data->targetChange) {
			$this->giveReputationToTarget( 0 - $this->_data->targetChange );
		}
		if ($this->_data->voterChange) {
			$this->giveReputationToVoter( 0 - $this->_data->voterChange );
		}
		$this->_data->delete();

		if (!$this->_importMode) $this->_post->votes = EpicDb_Vote::countVotes($this->_post);
		$this->_post->save();

	}

	/**
	 * After the vote has been cast, handle these things
	 *
	 * @return void
	 * @author Corey Frang
	 **/
	protected function _postCast()
	{
		if (!$this->_importMode) {
			$this->_post->votes = EpicDb_Vote::countVotes($this->_post);
			$this->_post->save();
			EpicDb_Vote::publish('vote', array('target' => $this->_post, 'voter' => $this->_userProfile, 'value' => $this->_type, 'vote' => $this->_data));
		}

	}

	/**
	 * Returns a reason for the vote to be disabled, or false if the user should be allowed to cast vote
	 *
	 * @return false / string
	 * @author Corey Frang
	 **/
	abstract public function isDisabled();

	/**
	 * Returns a timestamp if the user has cast this vote or false if not
	 *
	 * @return void
	 * @author Corey Frang
	 **/
	public function hasCast()
	{
		if($this->_data->isNewDocument()) return false;
		return $this->_data->date;
	}

	/**
	 * returns error state
	 *
	 * @return false/string describing error
	 * @author Corey Frang
	 **/
	public function getError()
	{
		return $this->_error;
	}

	/**
	 * Magic Properties - Pass along to the underlying mongo data
	 *
	 * @return mixed
	 * @author Corey Frang
	 **/
	public function __get($columnName)
	{
		return $this->_data->$columName;
	}

	/**
	 * Magic Properties - Pass along to the underlying mongo data
	 *
	 * @return mixed
	 * @author Corey Frang
	 **/
	public function __set($columnName, $value)
	{
		return $this->_data->$columName = $value;
	}

	/**
	 * Gives given amount of reputation to voter
	 *
	 * @return the amount of reputation given
	 * @author Corey Frang
	 **/
	public function giveReputationToVoter($amount)
	{
		if (!$this->hasCast()) return 0;
		$this->_data->voterChange += $amount;
		$this->_data->save();
		$this->_giveReputation($this->_userProfile, $amount);

	}

	/**
	 * Gives given amount of reputation to poster
	 *
	 * @return the amount of reputation given
	 * @author Corey Frang
	 **/
	public function giveReputationToTarget($amount)
	{
		if (!$this->hasCast()) return 0;
		$this->_data->targetChange += $amount;
		$this->_data->save();
		if ($this->_data->target instanceOf EpicDb_Mongo_Profile_User)
		{
			$this->_giveReputation($this->_data->target, $amount);
		}
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Corey Frang
	 **/
	protected function _giveReputation(EpicDb_Mongo_Profile_User $profile, $amount)
	{
		if ( !$this->_importMode ) {
			$profiles = EpicDb_Mongo_Profile_User::getMongoCollection();
			$query = array(
				'_id' => $profile->_id
			);
			$update = array(
				'$inc' => array(
					'reputation' => $amount
				)
			);
			$profiles->update($query, $update);
		}

	}


}