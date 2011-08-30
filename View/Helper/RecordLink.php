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
	public function recordLink($record, $params = array(), $urlParams = array()) {
		if($record instanceOf EpicDb_Mongo_Profile) return $this->view->profileLink($record, $params); 
		if($record instanceOf EpicDb_Mongo_Post) return $this->view->postLink($record, $params); 
		// Quick fix... need better resolution
		// $record = EpicDb_Mongo::db('record')->find($record->_id);
		if(!$record || !$record->id) return null;
		$tooltip = true;
		if(isset($params['tooltip']) && $params['tooltip'] == false) {
			$tooltip = false;
		}
		$text = $record->name;
		$routeParams = $record->getRouteParams();
		if(isset($routeParams['part'])) {
			$text .= " [Part ".$routeParams['part']."]";
		}
		if(isset($params['text'])) {
			$text = $params['text'];
		}
		$class = "";
		if(isset($params['class'])) {
			$class = $params['class'];
		}
		$rel = "";
		if(isset($params['rel'])) {
			$rel = $params['rel'];
		}
		
		if($record->quality) {
			$class .= " quality-".$record->quality;
		}
		
		$this->view->tooltip($record)->addToCache();

		if(!empty($urlParams)) return $this->htmlTag("a", array(
			"rel" => $rel,
			"class" => $class,
			"href" => $this->view->url($urlParams+$record->getRouteParams(), $record->routeName, true),
		), $text);
		return $this->htmlTag("a", array(
			"rel" => $rel,
			"class" => $class,
			"href" => $this->view->url(array(
				'action'=> 'view',
			)+$record->getRouteParams(), $record->routeName, true),
		), $text);
	}
}