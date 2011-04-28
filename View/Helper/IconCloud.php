<?php

class EpicDb_View_Helper_IconCloud extends MW_View_Helper_HtmlTag {

  public function iconCloud($records)
  {
		$html = "";
		if($records instanceOf EpicDb_Mongo_Tags) {
			foreach($records as $record) {
				$html .= $this->htmlTag("div", array("class" => "inline-flow icon-cloud icon"), $this->view->iconLink($record->ref));
			}			
		} else {
			foreach($records as $record) {
				// I thought the auto-loading mongo stuff would do this, but it's not apparently... FIX TODO NYI
				// if($record->_type == 'user') {
				// 	$record = EpicDb_Mongo::db('user')->find($record->_id);
				// }
				$html .= $this->htmlTag("div", array("class" => "inline-flow icon-cloud icon"), $this->view->iconLink($record));
			}			
		}
		return $html;
  }
} // END class LQA_View_Helper_PlayerScorecard