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
class EpicDb_View_Helper_WebsiteSummary extends MW_View_Helper_HtmlTag
{
	public function websiteSummary(EpicDb_Mongo_Profile_Group_Website $profile) {
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
		if(R2Db_Auth::getInstance()->hasPrivilege($profile, 'edit')) {
			$buttons .= $this->view->button(array(
				'action' => 'edit',
				'profile' => $profile
			), 'profile', true, array(
				'text' => 'Edit Website',
				'icon' => 'key',
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
		
		if($profile->description) $placeholder->widget($this->htmlTag("h3", array(), "Website Description")."".$this->htmlTag("p", array(), $profile->description));

		if($totalAdmins = $profile->admins->count()) {
			$placeholder->widget(
				$this->htmlTag("h3", array(), $profile->name."'s Admins")."".
				$this->view->iconCloud($profile->admins)."".
				$this->htmlTag("p", array("class" => 'iconCloud-label'), ''
					// $this->htmlTag("a", array('href' => $this->view->url(array(
					// 	'profile' => $profile,
					// 	'action'=>'admins',
					// ), 'profile', true)), 'View All '.$totalAdmins)
				)
			);			
		}
		
		if($totalMembers = $profile->members->count()) {
			$placeholder->widget(
				$this->htmlTag("h3", array(), $profile->name."'s Members")."".
				$this->view->iconCloud($profile->members, 21)."".
				$this->htmlTag("p", array("class" => 'iconCloud-label'), ''
					// $this->htmlTag("a", array('href' => $this->view->url(array(
					// 	'profile' => $profile,
					// 	'action'=>'admins',
					// ), 'profile', true)), 'View All '.$totalAdmins)
				)
			);			
		}
		
		$followers = $profile->getMyFollowers();
		if($totalFollowers = $followers->count()) {
			$placeholder->widget(
				$this->htmlTag("h3", array(), $profile->name."'s Followers")."".
				$this->view->iconCloud($followers, 21)."".
				$this->htmlTag("p", array("class" => 'iconCloud-label'), 				
					$this->htmlTag("a", array('href' => $this->view->url(array(
						'profile' => $profile,
						'action'=>'followers',
					), 'profile', true)), 'View All '.$totalFollowers)
				)
			);			
		}
		
	}
} // END class EpicDb_View_Helper_ProfileSummary