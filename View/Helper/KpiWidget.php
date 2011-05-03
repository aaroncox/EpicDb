<?php
/**
 * EpicDb_View_Helper_KpiWidget
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_KpiWidget extends MW_View_Helper_HtmlTag
{
	public function kpiWidget($value, $params = array()) {
		// No value? Return nothin
		if($value === null || $value === 0) return '';
		
		// Some configs
		$class = "";
		$label = "";
		if(isset($params['class'])) $class = $params['class'];
		if(isset($params['label'])) $label = $params['label'];
		if(isset($params['description'])) $label .= $this->htmlTag("p", array("class" => "text-verysmall font-sans"), $params['description']);
		
		// Return the widget
		return $this->htmlTag("div", array("class" => "kpi-stat ".$class), 
			$this->htmlTag("div", array("class" => "kpi-value"), $value)."".
			$this->htmlTag("div", array("class" => "kpi-label"), $label)
		);
	}
} // END class EpicDb_View_Helper_KpiWidget