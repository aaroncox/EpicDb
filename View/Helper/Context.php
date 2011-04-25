<?php
/**
 * EpicDb_View_Helper_Context
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_Context extends Zend_View_Helper_Placeholder_Container
{	
	protected $_widgetStart = "<div class='sidebar-widget rounded shadowy'>";
	protected $_widgetEnd = "</div>";

	public function context() {
		return $this;
	}
	
	public function widget($text, $type = 'APPEND') {
		if($type == 'PREPEND') {
			$this->prepend($this->_widgetStart.$text.$this->_widgetEnd);
		} else {
			$this->append($this->_widgetStart.$text.$this->_widgetEnd);
		}
		return $this;
	}
} // END class EpicDb_View_Helper_Context