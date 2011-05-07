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
		$placeholder->append($this->view->card($profile, array("class" => "wide", "tagType" => "h2"))."");

		$buttons = '';
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
		if($profile->feed) {
			$buttons .= $this->view->button(array(
				'action' => 'manual-crawl',
				'profile' => $profile
			), 'profile', true, array(
				'text' => 'Scan RSS',
				'icon' => 'key',
			));			
		}
		if(EpicDb_Auth::getInstance()->getUserProfile()) {
			$buttons .= $this->view->followButton($profile);
			$buttons .= $this->view->followButton($profile, array("mode" => "block"));
		}
		if($buttons != "") "<h3>Available Actions</h3>".$placeholder->widget($buttons);
		
		if($profile->bio) $placeholder->widget($this->htmlTag("h3", array(), "Biography")."".$this->htmlTag("p", array(), $profile->bio));

		$followers = $profile->getMyFollowers();
		if($followers->count()) {
			$placeholder->widget($this->htmlTag("h3", array(), $profile->name."'s Followers")."".$this->view->iconCloud($followers));			
		}
		
		if($profile->following && $profile->following->export() != array()) {
			$placeholder->widget($this->htmlTag("h3", array(), $profile->name." is following")."".$this->view->iconCloud($profile->following));			
		}
	}
} // END class EpicDb_View_Helper_ProfileSummary