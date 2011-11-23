<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_View_Helper_DataCell extends Zend_View_Helper_Abstract
{
	public function dataCell($column) {
		if(isset($column['record'])) {
			$record = $column['record'];			
		}
		// var_dump($record, $column); exit;
		$content = "";
		if(isset($column['content'])) {
			$content = $column['content'];
		}
		$class = '';
		if(isset($column['class'])) {
			$class = $column['class'];			
		}
		if(isset($column['helpers'])) {
			if(!$record) {
				throw new Exception("You must specify a record when using a dataCell helper.");
			}
			$content = '';
			foreach($column['helpers'] as $helper => $params) {
				$content .= $this->view->$helper($record, $params);
			}			
		}
		if(isset($column['hidden'])) {
			$content .= $this->view->htmlTag("span", array("class" => "hidden-data"), $column['hidden']);
		}
		return $content;
		// return $this->view->htmlTag("td", array("class" => $class), $content);
	}
} // END class EpicDb_View_Helper_DataCell extends Zend_View_Helper_Abstract