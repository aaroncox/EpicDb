<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Badge
{
	protected static $_badges = array();
	public static function init() {
		EpicDb_Badge_Listener::getInstance();
	}
	public static function getBadges() {
		if(static::$_badges) {
			return static::$_badges;
		}
		return static::$_badges = EpicDb_Mongo::db('badge')->fetchAll()->makeDocumentSet();
	}
	protected static $_badgeLoader = null;
	
	public static function getPluginLoader() {
		if(static::$_badgeLoader) return static::$_badgeLoader;
		return static::$_badgeLoader = new Zend_Loader_PluginLoader(array(
        'EpicDb_Badge_Helper_' => 'EpicDb/Badge/Helper/'
    ));
	}
	
	public static function helper(EpicDb_Badge_Interface $data) {
		$loader = static::getPluginLoader();
		$className = $loader->load($data->getBadgeHelper());
		return new $className($data);
	}
} // END class EpicDb_Badge