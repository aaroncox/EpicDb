<?php
/**
 * 
 *
 * @author Corey Frang
 * @package EpicDb_Feed
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 */

/**
 *  EpicDb_Feed_Generator
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Feed
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 * @version $Id:$
 */
class EpicDb_Feed_Generator extends MW_Feed_Generator {
	public function addPost(EpicDb_Mongo_Post $post) {
		$view = $this->getView();
		if (!$view) throw new Exception("Must set the view");
		
		$link = $this->makeUri($post->getPermaLink($view));
		if($post->title) {
			$title = strip_tags($post->title);
		} else {
			$subject = $post->tags->getTag('subject');
			$parent = $post->_parent;
			$on = '';
			$x = 0;
			if ($subject) {
				$on = ' on '.$subject->name;
			}
			else while ($parent && $parent->id) {
				if ($x++ > 5) exit;
				if ($parent->title) {
					$on .= ' on '.$parent->title;
					break;
				}
				$on .= ' on '.ucfirst($parent->_type);
				
				$parent = $parent->_parent;
			}
			$author = $post->tags->getTag('author');
			$title = $post->tldr ?: ucfirst($post->_type).' by '.$author->name.$on;
		}
		$this->addEntry(array(
				'title' => $title,
				'link' => $link,
				'guid' => $link,
				'author' => $post->feedAuthor,
				'description' => $post->body,
				'content' => $post->body,
				'lastUpdate' => $post->_created ?: $post->touched,
			));
	}
}