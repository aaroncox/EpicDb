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
		$text = $post->getName();
		if(isset($params['text'])) {
			$text = $params['text'];
		}
		if(!strlen($text)) {
			$text = $this->view->htmlFragment($post->body, $textLimit)." ";
		}
		$urlParams = array(
			"action" => "view"
		);

		if(isset($params['action'])) {
			$urlParams["action"] = $params["action"];
		}
		$urlParams += $post->getRouteParams();

		$tagAttribs = array();

		if(isset($params['target'])) {
			$tagAttribs["target"] = $params["target"];
		} 
		if(isset($params['rel'])) {
			$tagAttribs["rel"] = $params["rel"];
		}
		
		$route = $post->routeName;
		if(isset($params['routeName'])) {
			$route = $params['routeName'];
		}
		
		$this->view->tooltip($post)->addToCache();
		$tagAttribs["href"] = $this->view->url( $urlParams, $route, true ) . $hash;
		$filter = new EpicDb_Filter_TagJSON();
		$tagAttribs["data-tag-json"] = $filter->single($post);
		if(isset($tagAttribs['class'])) {
			$tagAttribs['class'] .= " tag-json";
		} else {
			$tagAttribs['class'] = "tag-json";
		}
		$this->htmlTag( "a", $tagAttribs, (string) $text );
		return $this->render();
	}
}