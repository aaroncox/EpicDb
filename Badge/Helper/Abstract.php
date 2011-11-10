<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
abstract class EpicDb_Badge_Helper_Abstract
{
	protected $_badge = null;
	protected $_options = array();
	protected $_unique = false;
	public $event = '';
	
	public function setUnique($unique) {
		$this->_unique = $unique;
		return $this;
	}
	
	public function isUnique() {
		return $this->_unique;
	}
	
	public function hasBadge(EpicDb_Mongo_Profile $profile, $tagMeta = array()) {
		foreach($profile->badges as $badge) {
			if(!$badge) continue;
			if($badge->ref->createReference() == $this->_badge->createReference()) {
				if($this->isUnique()) {
					return true;
				} else {
					$matches = true;
					foreach($tagMeta as $key => $value) {
						if($badge->$key != $value) {
							$matches = false;
						}
					}
					if($matches) {
						return true;
					}
				}
			}
		}
		return false;
	}
	
	public function awardTo(EpicDb_Mongo_Profile $profile, $tagMeta = array()) {
		if($this->hasBadge($profile, $tagMeta)) {
			return;
		}
		$badge = $this->_badge;
		$profile->badges->tag($badge, $badge->getBadgeQuality(), $tagMeta+array("time" => time()));
		$profile->save();
	}
	
	public function __construct(EpicDb_Badge_Interface $badge) {
		$this->_badge = $badge;
		$options = $badge->getBadgeOptions();
		foreach($options as $key => $value) {
			$method = "set".ucfirst($key);
			if(method_exists($this, $method)) {
				$this->$method($value);
			} else {
				$this->_options[$key] = $value;
			}
		}
	}
	
	public function getBadge() {
		return $this->_badge;
	}
	
	public function event(EpicDb_Event_Abstract $event) {
	}
	
	public function getOption($key, $default = null) {
		if(isset($this->_options[$key])) {
			return $this->_options[$key];
		}
		return $default;
	}
} // END class EpicDb_Badge_Helper_Abstract