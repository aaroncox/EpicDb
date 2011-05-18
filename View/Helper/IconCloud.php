<?php

class EpicDb_View_Helper_IconCloud extends MW_View_Helper_HtmlTag {

  public function iconCloud($records, $limit = null)
  {
		$html = "";
		$count = 1;
		if($records instanceOf EpicDb_Mongo_Tags) {
			foreach($records as $idx => $record) {
				if($limit) {
					if($count > $limit) {
						continue;
					}
					$count++;					
				}
				$html .= $this->htmlTag("div", array("class" => "inline-flow icon-cloud icon"), $this->view->iconLink($record->ref));
			}			
		} else {
			foreach($records as $idx => $record) {
				if($limit) {
					if($count > $limit) {
						continue;
					}
					$count++;					
				}
				$html .= $this->htmlTag("div", array("class" => "inline-flow icon-cloud icon"), $this->view->iconLink($record));
			}			
		}
		return $html;
  }
} // END class LQA_View_Helper_PlayerScorecard