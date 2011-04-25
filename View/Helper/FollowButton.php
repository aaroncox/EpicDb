<?php
/**
 * EpicDb_View_Helper_FollowButton
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_FollowButton extends MW_View_Helper_HtmlTag
{
	public function followButton($record) {
		if($record instanceOf EpicDb_Mongo_Record) $type = $route = 'record';
		if($record instanceOf EpicDb_Mongo_Profile) $type = $route = 'profile';
		if($record instanceOf EpicDb_Mongo_Post) $type = $route = 'post';
		if($profile = EpicDb_Auth::getInstance()->getUserProfile()) {
			if(in_array($record->createReference(), $profile->following->export())) {
				return $this->view->button(array(
					 $type => $record,
					'action' => 'unfollow',
				), $route, true, array(
					'text' => 'unfollow',
					'icon' => 'gear',
				));							
			} else {
				return $this->view->button(array(
					 $type => $record,
					'action' => 'follow',
				), $route, true, array(
					'text' => 'follow',
					'icon' => 'gear',
				));			
			}
		} 
		return null;
		
	}
} // END class EpicDb_View_Helper_FollowButton

