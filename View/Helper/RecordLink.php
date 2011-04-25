<?php
/**
 * EpicDb_View_Helper_RecordLink
 *
 * Builds the link to a record, using the record route.
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_RecordLink extends MW_View_Helper_HtmlTag
{
	public function recordLink($record, $params = array()) {
		$tooltip = true;
		if(isset($params['tooltip']) && $params['tooltip'] == false) {
			$tooltip = false;
		}
		$text = $record->name;
		if(isset($params['text'])) {
			$text = $params['text'];
		}
		$class = "";
		if(isset($params['class'])) {
			$class = $params['class'];
		}
		if(isset($params['icon'])) {
			$text = "<img src='".$record->getIcon()."' class='".$params['icon']."'/>".$text;
		}
		return $this->htmlTag("a", array(
			"rel" => 'no-tooltip nofollow',
			"class" => $class,
			"href" => $this->view->url(array(
				'action'=> 'view',
				'record' => $record,
			), 'record', true),
		), $text);
	}
}