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
		$class = "";
		if(isset($params['class'])) {
			$class = $params['class'];
		}
		$rel = "";
		if(isset($params['rel'])) {
			$rel = $params['rel'];
		}
		if(trim($text) == "") $text = "Unknown";
		return $this->htmlTag("a", array(
			"rel" => $rel,
			"class" => $class,
			"title" => $profile->name."'s Profile",
			"href" => $this->view->url(array(
				'action'=> 'view',
				'profile' => $profile,
			), 'profile', true),
		), (string) $text);
	}
}