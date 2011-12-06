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
		$class = null;
		$image = null;
		if(isset($this->_params['iconClass'])) {
			$class = $this->_params['iconClass'];
		}
		if (!$record) return '';
		if($record instanceOf EpicDb_Mongo_Record) {
			$image = "<img src='".$record->getIcon()."' alt='".$this->view->escape($record->name)."' class=".$class."/>";
		} elseif($record instanceOf EpicDb_Mongo_Profile) {
			if($record->getIcon()) {
				$image = $this->view->htmlTag("img", array("alt" => $record->name, "src" => $record->getIcon(), "class" => $class))."";
			} elseif($record->_parent && $record->_parent->logo) {
				$image = $this->view->htmlTag("img", array("alt" => $record->name, "src" => $record->_parent->getIcon(), "class" => $class))."";
			} else {
				$image = $this->view->gravatar($record->email, array(), array("class" => $class));
			}
			// $html .= $this->view->profileLink($record, array("text" => $image));
		}
		// var_dump($image, $class); exit;
		if(!$image) {
			return '';
		}
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
		if(!isset($this->_params['class'])) $this->_params['class'] = "";
		if($record->quality) {
			$this->_params['class'] .= " border-quality-".$record->quality;
		}
		return $this;
	}
	
	public function render() {
		$record = $this->_record;
		if (!$record) return '';
		if(!isset($this->_params['div']) || $this->_params['div'] != false) {
			$html = "<div class='record-icon ".$this->_params['class']."'>";
			$html .= $this->view->recordLink($record, array("text" => $this->icon()));
			$html .= "</div>";
		} else {
			$html = $this->view->recordLink($record, array("text" => $this->icon(), 'class' => $this->_params['class']))."";
		}
		return $html;
	}
	
	public function __toString() {
		return $this->render();
	} 
} // END class EpicDb_View_Helper_IconLink