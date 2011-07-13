<?php
/**
 * EpicDb_View_Helper_RecordSummary
 *
 * undocumented class
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_RecordSummary extends MW_View_Helper_HtmlTag
{
	public function recordSummary(EpicDb_Mongo_Record $record) {
		// $this->card($this->profile, array("class" => "wide"))
		$placeholder = $this->view->summary();
		$placeholder->append($this->view->tooltip($record)."");
		$buttons = "";
		if(EpicDb_Auth::getInstance()->getUserProfile()) {
			$buttons .= $this->view->followButton($record);
		}
		if (MW_Auth::getInstance()->hasPrivilege(new MW_Auth_Resource_Super(), 'edit')) {
			$buttons .= $this->view->button(array(
				'action'=>'admin',
				'record'=>$record,
			), 'record', true, array(
				'text' => 'Edit',
				'icon' => 'key'
			));
		}
		
		if($buttons) $placeholder->widget('<h3>Available Actions</h3>'.$buttons);

		// Get followers for the widget
		$followers = $record->getMyFollowers();
		if($followers->count()) {
			$placeholder->widget($this->htmlTag("h3", array(), "Followers")."".$this->view->iconCloud($followers));
		}
	}
} // END class EpicDb_View_Helper_RecordSummary