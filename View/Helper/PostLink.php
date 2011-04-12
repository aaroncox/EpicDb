<?php
/**
 * EpicDb_View_Helper_PostLink
 *
 * Builds the link to a profile, using the profile route.
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_PostLink extends MW_View_Helper_HtmlTag
{
	public function postLink($post, $params = array()) {
		if (!is_object($post)) {
			return '';
		}
		$text = $post->title;
		if(isset($params['text'])) {
			$text = $params['text'];
		}
		return $this->htmlTag("a", array(
			"rel" => 'no-tooltip nofollow',
			"href" => $this->view->url(array(
				'action'=> 'view',
				'post' => $post,
			), 'post', true),
		), (string) $text);
	}
}