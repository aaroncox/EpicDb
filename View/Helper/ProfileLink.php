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
	public function profileLink($profile, $params = array()) {
		$tooltip = true;
		if(isset($params['tooltip']) && $params['tooltip'] == false) {
			$tooltip = false;
		}
		if (!is_object($profile)) {
			return '';
		}
		$text = $profile->name;
		if(isset($params['text'])) {
			$text = $params['text'];
		}
		return $this->htmlTag("a", array(
			"rel" => 'no-tooltip',
			"href" => $this->view->url(array(
				'action'=> 'view',
				'profile' => $profile,
			), 'profile', true),
		), $text);
	}
}