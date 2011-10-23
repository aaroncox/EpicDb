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
		$textLimit = 80;
		if(isset($params['text-limit'])) {
			$textLimit = $params['text-limit'];
		}
		$hash = '';
		if(isset($params['hash'])) {
			$hash = $params['hash'];
		}
		$text = $post->title;
		if(isset($params['text'])) {
			$text = $params['text'];
		}
		if(!strlen($text)) {
			$text = $this->view->htmlFragment($post->body, $textLimit);
		}
		$action = "view";
		if(isset($params['action'])) {
			$action = $params['action'];
		}
		$rel = "";
		if(isset($params['rel'])) {
			$rel = $params['rel'];
		}
		
		$route = $post->routeName;
		if(isset($params['routeName'])) {
			$route = $params['routeName'];
		}
		
		$this->view->tooltip($post)->addToCache();
		// var_dump($text);
		if($text == null) {
			return 
				$this->htmlTag("a", array(
				"rel" => $rel,
				"href" => $this->view->url(array(
					'action'=> $action,
				)+$post->getRouteParams(), $route, true).$hash,
			), (string) $text)."";
		}

		// This will let us pass in the post as a # and hit it directly.
		// if(isset($params['#'])) {
		// 	if($params['#'] instanceOf EpicDb_Mongo_Post) {
		// 		$hash = $params['#']->_type."-".$params['#']->id;
		// 	}
		// 	$hash = "#".$hash;
		// }
		return $this->htmlTag("a", array(
			"rel" => $rel,
			"href" => $this->view->url(array(
				'action'=> $action,
			)+$post->getRouteParams(), $route, true).$hash,
		), (string) $text)."";
	}
}