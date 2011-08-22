<?php
/**
 * EpicDb_View_Helper_IconLink
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_IconLink extends Zend_View_Helper_Abstract
{	
	protected $_params = array();
	protected $_record = null;
	public function icon() {
		$record = $this->_record;
		if (!$record) return '';
		if($record instanceOf EpicDb_Mongo_Record) {
			$image = "<img src='".$record->getIcon()."' alt='".$this->view->escape($record->name)."'/>";
		} elseif($record instanceOf EpicDb_Mongo_Profile) {
			if($record->logo) {
				$image = $this->view->htmlTag("img", array("alt" => $record->name, "src" => $record->getIcon()))."";
			} elseif($record->_parent && $record->_parent->logo) {
				$image = $this->view->htmlTag("img", array("alt" => $record->name, "src" => $record->_parent->getIcon()))."";
			} else {
				$image = $this->view->gravatar($record->email);
			}
			// $html .= $this->view->profileLink($record, array("text" => $image));
		}
		// var_dump($image); exit;
		return $image;
	}
	
	/**
	 * iconLink - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function iconLink($record, $params = array())
	{
    $this->_record = false;
		if(!$record) return $this;
		$this->_record = $record;	
		$this->_params = $params;
		return $this;
	}
	
	public function render() {
		$record = $this->_record;
		if (!$record) return '';
		$html = "<div class='record-icon ".$this->_params['class']."'>";
		if($record instanceOf EpicDb_Mongo_Profile) {
			$html .= $this->view->profileLink($record, array("text" => $this->icon()));
		} elseif($record instanceOf EpicDb_Mongo_Record) {
			$html .= $this->view->recordLink($record, array("text" => $this->icon()));			
		}
		$html .= "</div>";
		return $html;
	}
	
	public function __toString() {
		return $this->render();
	} 
} // END class EpicDb_View_Helper_IconLink