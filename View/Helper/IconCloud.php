<?php

class EpicDb_View_Helper_IconCloud extends Zend_View_Helper_Abstract {

  public function iconCloud($records)
  {
		$html = "";
		if($records instanceOf EpicDb_Mongo_Tags) {
			foreach($records as $record) {
				$html .= $this->view->iconLink($record->ref);
			}			
		} else {
			foreach($records as $record) {
				$html .= $this->view->iconLink($record);
			}			
		}
		return $html;
  }
} // END class LQA_View_Helper_PlayerScorecard