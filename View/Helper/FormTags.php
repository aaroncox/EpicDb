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
		// MW_Script::load('autocomplete');
		$info = $this->_getInfo($name, $value, $attribs);
		extract($info); // name, value, attribs, options, listsep, disable

		if (!isset($attribs['data-search-url'])) {
			$params = array();
			if (!empty($attribs['recordType'])) {
				$params['type'] = $attribs['recordType'];
				unset($attribs['recordType']);
			}
			$attribs['data-search-url'] = $this->view->url($params, 'tag-search', true);
		}
		
		if(isset($attribs['limit'])) {
			$attribs['data-limit'] = $attribs['limit'];
			unset($attribs['limit']);
		}
		
		$hidden = parent::formHidden($name, $value, $attribs);
		
		
		$filter = new EpicDb_Filter_TagJSON();
		$current = $filter->toArray($value);
		$tagger = $this->view->htmlTag('div',array(
			'class' => 'tags-ui ui-helper-clearfix'
		),$hidden.$this->tagCards($current))."";
		
		return $tagger;
	}
}