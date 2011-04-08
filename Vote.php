<?php
/**
 *
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 */

/**
 *  EpicDb_Vote
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 * @version $Id:$
 */
class EpicDb_Vote {

	/**
	 * Class Contants - Vote Types
	 */
	const UP        = "up";
	const DOWN      = "down";
	const SPAM      = "spam";
	const CLOSE     = "close";
	const REOPEN    = "open";
	const ACCEPT    = "accept";
	const OFFENSIVE = "offensive";
	const FAVORITE  = "favorite";
	const MODERATOR = "moderator";

	/**
	 * undocumented class variable
	 *
	 * @var string
	 **/
	static protected $_types = array(
		self::ACCEPT => 'EpicDb_Vote_Accept',
		self::CLOSE => 'EpicDb_Vote_Close',
		self::DOWN => 'EpicDb_Vote_Down',
		self::FAVORITE => 'EpicDb_Vote_Favorite',
		self::MODERATOR => 'EpicDb_Vote_Moderator',
		self::OFFENSIVE => 'EpicDb_Vote_Offensive',
		self::REOPEN => 'EpicDb_Vote_Open',
		self::SPAM => 'EpicDb_Vote_Spam',
		self::UP => 'EpicDb_Vote_Up',
	);

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Corey Frang
	 **/
	public static function factory(EpicDb_Mongo_Post $post, $type, EpicDb_Mongo_Profile_User $user = null)
	{
		if (!$user) $user = self::_getUserProfile();
		if (!$user) return null;
		if (isset(self::$_types[$type])) {
			$className = self::$_types[$type];
			return new $className($user, $post);
		}

		throw new Exception('Unknown Vote Type');
	}

	/**
	 * Gets an array containing the votes of each type, and a total score
	 * If $type is defined, it will return a count of only those votes.
	 *
	 * @return array / int
	 * @author Corey Frang
	 **/
	public static function countVotes(EpicDb_Mongo_Post $post, $type = false)
	{
		return EpicDb_Mongo::db('vote')->getVoteSummary($post, $type);
	}


	/**
	 * Returns the currently logged in user
	 * only meant to be called by this class
	 * just a helper
	 *
	 * @return void
	 * @author Corey Frang
	 **/
	private static function _getUserProfile()
	{
		return EpicDb_Auth::getInstance()->getUserProfile();
	}
	
	/**
	 * undocumented class variable
	 *
	 * @var string
	 **/
	protected static $_subscribers = array();
	
	/**
	 * Publish a vote event
	 *
	 * @return void
	 * @author Corey Frang
	 **/
	public static function publish($event, $data)
	{
		if (isset(self::$_subscribers[$event])) {
			foreach(self::$_subscribers[$event] as $method) {
				call_user_func_array($method, array($event, $data));
			}
		}
	}
	
	/**
	 * Call a method when a subscribed event fires
	 *
	 * @return void
	 * @author Corey Frang
	 **/
	public static function subscribe($events, $method)
	{
		if (is_string($events)) {
			$events = preg_split("/\s+/", $events);
		}
		foreach ($events as $event) {
			if (!isset(self::$_subscribers[$event])) {
				self::$_subscribers[$event] = array();
			}
			self::$_subscribers[$event][] = $method;
		}
	}

}