<?php
/**
 * EpicDb_View_Helper_Summary
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_Summary extends Zend_View_Helper_Placeholder_Container
{	
	protected $_widgetStart = "<div class='sidebar-widget rounded'>";
	protected $_widgetEnd = "</div>";

	public function summary() {
		return $this;
	}
	
	public function widget($text) {
		$this->append($this->_widgetStart.$text.$this->_widgetEnd);
	}
} // END class EpicDb_View_Helper_Summary