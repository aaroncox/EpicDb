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
		if($post->_type == 'question') {
			return $this->view->questionLink($post, $params);
		}
		$hash = '';
		$text = $post->title;
		if(isset($params['text'])) {
			$text = $params['text'];
		}
		$rel = "";
		if(isset($params['rel'])) {
			$rel = $params['rel'];
		}
		
		$this->view->tooltip($post)->addToCache();
		
		// var_dump($text);
		if($text == null) {
			$text = '[Read More]';
			$preview = $this->view->htmlFragment($post->body, 80);
			return 
				$this->htmlTag("span", array(
					'class' => 'post-preview',
				), $preview)." ".
				$this->htmlTag("a", array(
				"rel" => $rel,
				"href" => $this->view->url(array(
					'action'=> 'view',
					'post' => $post,
				), 'post', true).$hash,
			), (string) $text);
		}

		// This will let us pass in the post as a # and hit it directly.
		// if(isset($params['#'])) {
		// 	if($params['#'] instanceOf EpicDb_Mongo_Post) {
		// 		$hash = $params['#']->_type."-".$params['#']->id;
		// 	}
		// 	$hash = "#".$hash;
		// }
		return $this->htmlTag("a", array(
			"rel" => 'no-tooltip nofollow',
			"href" => $this->view->url(array(
				'action'=> 'view',
				'post' => $post,
			), 'post', true).$hash,
		), (string) $text);
	}
}