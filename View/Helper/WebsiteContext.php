<?php
/**
 * EpicDb_View_Helper_ProfileContext
 *
 * undocumented class
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_WebsiteContext extends MW_View_Helper_HtmlTag
{
	public function websiteContext(EpicDb_Mongo_Profile_Group_Website $profile) {
		$placeholder = $this->view->context();
		$placeholder->append($this->view->tooltip($profile));
		
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
		if(EpicDb_Auth::getInstance()->hasPrivilege($profile, 'edit')) {
			$buttons .= $this->view->button(array(
				'action' => 'edit',
				'profile' => $profile
			), 'profile', true, array(
				'text' => 'Edit',
				'icon' => 'key',
			));						
			$buttons .= $this->view->button(array(
				'action' => 'logo',
				'profile' => $profile
			), 'profile', true, array(
				'text' => 'Upload Icon/Logo',
				'icon' => 'key',
			));
		}
		if($profile->feed) {
			$buttons .= $this->view->button(array(
				'action' => 'crawl',
				'profile' => $profile
			), 'profile', true, array(
				'text' => 'Scan RSS',
				'icon' => 'key',
			));			
		}
		if($curUser = EpicDb_Auth::getInstance()->getUserProfile()) {
			if($application = $profile->application($curUser)) {
				if($profile->_groupType == "open" || $profile->_groupType == "closed") {
					$buttons .= $this->view->button(array(
						'user' => $application->candidate->id,
						'group' => $application->group->id,
						'gtype' => $application->group->_type,
						'action' => 'view',
					), 'group_application', true, array(
						'text' => 'Review Application',
						'icon' => 'key',
						'data-epic-tooltip' => 'Your application is currently under review by the leaders of this '.$profile->_type.', if you\'d like to delete your application, click here.',
					));				
				}				
			} elseif(!$profile->isMember()) {
				if($profile->_groupType == "open" || $profile->_groupType == "closed") {
					$buttons .= $this->view->button(array(
						'action' => 'join',
						'profile' => $profile
					), 'profile', true, array(
						'text' => 'Join this '.ucfirst($profile->_type),
						'icon' => 'key',
					));				
				}
			} else {
				$buttons .= $this->view->button(array(
					'action' => 'leave',
					'profile' => $profile
				), 'profile', true, array(
					'text' => 'Leave this '.ucfirst($profile->_type),
					'icon' => 'key',
				));								
			}
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
} // END class EpicDb_View_Helper_ProfileContext