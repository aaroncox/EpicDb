<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
abstract class EpicDb_Event_Abstract
{	
	protected $_type = null;
	public $data = null;
	
	public function __construct($args) {
		$this->_type = array_shift($args);
		if(count($args) == 1 && is_array($args[0])) {
			$this->data = $args[0];
		} else {
			$this->data = $args;			
		}
		$this->init();
		return $this;
	}
	
	public function init() {
	}
	public function getType() {
		return $this->_type;
	}
	public function publish() {
		EpicDb_Event::publish($this);
		return $this;		
	}

	
} // END class EpicDb_Event_Abstract