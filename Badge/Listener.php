<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Badge_Listener
{
	protected $_subscribers = array();
	
	/**
	 * Class Instance - Singleton Pattern
	 *
	 * @var self
	 **/
	static protected $_instance = NULL;

	/**
	 * private constructor - singleton pattern
	 *
	 * @return void
	 * @author Corey Frang
	 **/
	private function __construct()
	{
		$this->init();
	}

	/**
	 * Returns (or creates) the Instance - Singleton Pattern
	 *
	 * @return self
	 * @author Corey Frang
	 **/
	static public function getInstance()
	{
		if (self::$_instance === NULL) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function init() {
		$badges = EpicDb_Badge::getBadges();
		foreach($badges as $badge) {
			if($badge->event) {
				$this->addEventListener($badge->event, $badge);
			}
		}
		EpicDb_Event::subscribe(implode(" ", array_keys($this->_subscribers)), array($this, 'event'));
	}
	
	public function event(EpicDb_Event_Abstract $event) {
		$type = $event->getType();
		if(isset($this->_subscribers[$type])) {
			foreach($this->_subscribers[$type] as $badge) {
				$badge->event($event);
			}
		}
	}
	
	public function addEventListener($events, $badge) {
		if (is_string($events)) {
			$events = preg_split("/\s+/", $events);
		}
		foreach ($events as $event) {
			if (!isset($this->_subscribers[$event])) {
				$this->_subscribers[$event] = array();
			}
			$this->_subscribers[$event][] = EpicDb_Badge::helper($badge);
		}
	}
} // END class EpicDb_Badge_Listener