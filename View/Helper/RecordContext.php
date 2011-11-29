<?php
/**
 * EpicDb_View_Helper_ProfileSummary
 *
 * undocumented class
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_RecordContext extends MW_View_Helper_HtmlTag
{
	public function recordContext(EpicDb_Mongo_Record $record, $params = array()) {
		$placeholder = $this->view->context();
		$placeholder->append($this->view->tooltip($record, $params)."");
		$buttons = "";
		if (EpicDb_Auth::getInstance()->hasPrivilege(new EpicDb_Auth_Resource_Moderator())) {
			$buttons .= $this->view->button(array(
				'action'=>'admin',
				'record'=>$record,
			), 'record', true, array(
				'text' => 'Edit',
				'icon' => 'key'
			));
		}
		
		if($buttons) $placeholder->widget('<h3>Available Actions</h3>'.$buttons);

		// $placeholder->widget($this->view->render("./_context/related-images.phtml"));
		
		// Get followers for the widget
		$followers = $record->getMyFollowers();
		if($followers->count()) {
			$placeholder->widget($this->htmlTag("h3", array(), "Followers")."".$this->view->iconCloud($followers, 18));
		}
	}
} // END class EpicDb_View_Helper_ProfileSummary