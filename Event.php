<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Event
{
	/**
	 * undocumented class variable
	 *
	 * @var string
	 **/
	protected static $_subscribers = array();
	
	protected static $_eventLoader = null;
	
	public static function getPluginLoader() {
		if(static::$_eventLoader) return static::$_eventLoader;
		return static::$_eventLoader = new Zend_Loader_PluginLoader(array(
        'EpicDb_Event_' => 'EpicDb/Event/'
    ));
	}
	
	public static function event($type) {
		$loader = static::getPluginLoader();
		$args = func_get_args();
		$className = $loader->load($type);
		return new $className($args);
	}
	
	/**
	 * Publish a vote event
	 *
	 * @return void
	 * @author Corey Frang
	 **/
	public static function publish(EpicDb_Event_Abstract $event)
	{
		$type = $event->getType();
		if (isset(self::$_subscribers[$type])) {
			foreach(self::$_subscribers[$type] as $method) {
				call_user_func($method, $event);
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

	
} // END class EpicDb_Event