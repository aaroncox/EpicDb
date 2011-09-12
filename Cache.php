<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Cache
{
	protected static $_cache = null;
	public function getCache() {
		if(self::$_cache) return self::$_cache;
		// echo "Generating Cache Layer."; 
		$frontendOpts = array(
			'caching' => true,
			'lifetime' => 300,
			'automatic_serialization' => true
		);
		$backendOpts = array(
			'servers' =>array(
				array(
					'host' => 'localhost',
					'port' => 11211
				)
			),
			'compression' => false
		);
		return self::$_cache = Zend_Cache::factory('Core', 'Memcached', $frontendOpts, $backendOpts);
	}
	public function load($record, $type) {
		$cache = self::getCache();
		$cacheId = $record->getCachePrefix()."_".$type;
		return $cache->load($cacheId); 
	}
	public function save($record, $type, $data) {
		$data = (string) $data;
		$cache = self::getCache();
		$cacheId = $record->getCachePrefix()."_".$type;
		// echo "Saving [".$cacheId."]<br/>";
		return $cache->save($data, $cacheId); 		
	}
	public function clean($record, $type) {
		$cache = self::getCache();
		$cacheId = $record->getCachePrefix()."_".$type;
		$cache->remove($cacheId);
	}
} // END class EpicDb_Cache