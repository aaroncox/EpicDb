<?php
/**
 * EpicDb_View_Helper_FeedPost
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_FeedPost extends MW_View_Helper_HtmlTag 
{
	public function feedPost(EpicDb_Mongo_Post $post) {
		$additional = "";
		$type = str_replace("-"," ", $post->_type);
		$profile = $post->tags->getTag('author');
		$myPost = null;
		while($post->_parent instanceOf EpicDb_Mongo_Post && $post->_parent->export() != array()) {
			if(!$myPost) $myPost = $post;
			$post = $post->_parent;
		}
		return $this->htmlTag("p", array(), 
			$this->htmlTag("span", array(), $this->view->profileLink($profile))."".
			" posted a ".
			$this->htmlTag("span", array(), ucwords($type))."".
			" on ".
			$this->htmlTag("span", array(), $this->view->postLink($post))
		);
	}
} // END class EpicDb_View_Helper_FeedPost