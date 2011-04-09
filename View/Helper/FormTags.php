<?php
/**
 *
 *
 * @author Corey Frang
 * @package EpicDb_View
 * @subpackage Helper
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 */

/**
 *  EpicDb_View_Helper_FormTags
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_View
 * @subpackage Helper
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 * @version $Id:$
 */
class EpicDb_View_Helper_FormTags extends Zend_View_Helper_FormHidden {
	
	public function tagCards($tagged)
	{
		$cards = array();
		foreach($tagged as $tag) {
			$cards[] = $this->view->card($tag, array("class" => "medium-icon"))."";
		}
		return implode($cards,'');
	}
	
	public function formTags($name, $value = null, array $attribs = null)
	{
		MW_Script::load('autocomplete');
		if (!isset($attribs['data-search-url'])) {
			$attribs['data-search-url'] = $this->view->url(array(), 'tag-search', true);
		}
		$info = $this->_getInfo($name, $value, $attribs);
		extract($info); // name, value, attribs, options, listsep, disable
		$hidden = parent::formHidden($name, $value, $attribs);
		
		
		$filter = new EpicDb_Filter_TagJSON();
		$current = $filter->toArray($value);
		$tagger = $this->view->htmlTag('div',array(
			'class' => 'tags-ui ui-helper-clearfix'
		),$hidden.$this->tagCards($current))."";
		
		return $tagger;
	}
}