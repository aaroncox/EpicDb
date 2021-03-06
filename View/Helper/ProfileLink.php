<?php
/**
 * EpicDb_View_Helper_ProfileLink
 *
 * Builds the link to a profile, using the profile route.
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_ProfileLink extends MW_View_Helper_HtmlTag
{
	public function getDiamonds($profile)
	{
		$user = $profile->user;
		$admin = $moderator = null;
		if($user instanceOf MW_Auth_Mongo_Role) {
			$admin = $user->isMember(MW_Auth_Group_Super::getInstance());
			if (!$admin) {
				$moderator = $user->isMember(EpicDb_Auth_Group_Moderators::getInstance());
			}
		}
		$title = '';
		if($admin || $moderator) {
			$title .= $this->htmlTag("span", array(
				"title" => $admin ? "Site Administrator" : "Moderator"
			), $admin ? "&diams;&diams;" : "&diams;");
		}
		return $title;
	}
	
	public function profileLink($profile, $params = array()) {
		if ( !$profile instanceOf EpicDb_Mongo_Profile ) return "";
		$tooltip = true;
		if(isset($params['tooltip']) && $params['tooltip'] == false) {
			$tooltip = false;
		}
		if (!is_object($profile)) {
			return 'anonymous';
		}
		$text = $this->getDiamonds($profile).$profile->name;
		if(isset($params['text'])) {
			$text = $params['text'];
		}
		$class = "tag-json";
		if(isset($params['class'])) {
			$class .= " ".$params['class'];
		}
		$target = null;
		if(isset($params['target'])) {
			$target = $params['target'];
		} 
		$dataTooltip = "";
		if(isset($params['data-epic-tooltip'])) {
			$dataTooltip = $params['data-epic-tooltip'];
		}
		$rel = "";
		if(isset($params['rel'])) {
			$rel = $params['rel'];
		}
		$action = "view";
		if(isset($params['action'])) {
			$action = $params['action'];
		}
		$format = null;
		if(isset($params['format'])) {
			$format = $params['format'];
		}
		
		$itemprop = null;
		if(isset($params['itemprop'])) {
			$itemprop = $params['itemprop'];
			$text = $this->view->htmlTag("span", array("itemprop" => "title"), $text);
		}
		
		// $this->view->tooltip($profile)->addToCache();
		$filter = new EpicDb_Filter_TagJSON();
		if(trim($text) == "") $text = "Unknown";
		return $this->htmlTag("a", array(
			"rel" => $rel,
			"class" => $class,
			"target" => $target,
			"data-epic-tooltip" => $dataTooltip,
			"itemprop" => $itemprop,
			"title" => $profile->name."'s Profile",
			"data-tag-json" => $filter->single($profile),
			"href" => $this->view->url(array(
				'action'=> $action,
				'profile' => $profile,
				"format" => $format,
			), 'profile', true),
		), (string) $text);
	}
}