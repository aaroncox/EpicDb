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
class EpicDb_View_Helper_ProfileSummary extends MW_View_Helper_HtmlTag
{
	public function profileSummary(EpicDb_Mongo_Profile $profile) {
		// $this->card($this->profile, array("class" => "wide"))
		$placeholder = $this->view->summary();
		$placeholder->append($this->view->card($profile, array("class" => "wide")));

		$buttons = '<h3>Available Actions</h3>';
		if ($profile->user && MW_Auth::getInstance()->hasPrivilege(new MW_Auth_Resource_Super(), 'sudo')) {
			$buttons .= $this->view->button(array(
				'controller'=>'user',
				'action'=>'sudo',
				'id'=>$profile->user->_id.''
			), 'default', true, array(
				'text' => 'Sudo',
				'icon' => 'key'
			));
		}
		if(EpicDb_Auth::getInstance()->getUserProfile()) {
			$buttons .= $this->view->button(array(
				'profile' => $profile,
				'action' => 'follow',
			), 'profile', true, array(
				'text' => 'follow',
				'icon' => 'gear',
			));			
		}
		if($buttons) $placeholder->widget($buttons);
		$placeholder->widget($this->htmlTag("h3", array(), "Biography")."".$this->htmlTag("p", array(), $profile->bio));
	}
} // END class EpicDb_View_Helper_ProfileSummary