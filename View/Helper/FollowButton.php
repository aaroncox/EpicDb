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
	public function followButton($record, $opts = array()) {
		$mode = 'follow';
		$modeField = 'following';
		if(isset($opts['mode'])) {
			$mode = $opts['mode'];
			$modeField = $opts['mode']."ing";
		}
		if($record instanceOf EpicDb_Mongo_Record) $type = $route = 'record';
		if($record instanceOf EpicDb_Mongo_Profile) $type = $route = 'profile';
		if($record instanceOf EpicDb_Mongo_Post) $type = $route = 'post';
		if(isset($opts['route'])) {
			$route = $opts['route'];
		}
		if($profile = EpicDb_Auth::getInstance()->getUserProfile()) {
			if(in_array($record->createReference(), $profile->$modeField->export())) {
				return $this->view->button(array(
					 $type => $record,
					'action' => 'un'.$mode,
				), $route, true, array(
					'data-tooltip' => 'Un'.$mode,
					'icon' => 'gear',
					'class' => 'has-tooltip epicdb-ajaxbutton ui-state-active',
				));
			} else {
				return $this->view->button(array(
					 $type => $record,
					'action' => $mode,
				), $route, true, array(
					'data-tooltip' => $mode,
					'icon' => 'gear',
					'class' => 'has-tooltip epicdb-ajaxbutton',
				));
			}
		}
		return null;

	}
} // END class EpicDb_View_Helper_FollowButton

