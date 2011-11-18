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
		if($record instanceOf EpicDb_Mongo_Profile) return $this->view->profileLink($record, $params+$urlParams); 
		if($record instanceOf EpicDb_Mongo_Post) return $this->view->postLink($record, $params+$urlParams); 
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
		$class = "tag-json";
		if(isset($params['class'])) {
			$class .= " ".$params['class'];
		}
		$rel = "";
		if(isset($params['rel'])) {
			$rel = $params['rel'];
		}
		
		$target = null;
		if(isset($params['target'])) {
			$target = $params['target'];
		} 
		
		if($record->quality) {
			$class .= " quality-".$record->quality;
		}
		
		$routeName = $record->routeName;
		if(isset($params['routeName'])) {
			$routeName = $params['routeName'];
		}
		
		$sectionParams = array();
		if(isset($params['section'])) {
			$sectionParams = $params['section'];
		}
		
		$dataTooltip = "";
		if(isset($params['data-tooltip'])) {
			$dataTooltip = $params['data-tooltip'];
		}
		
		$action = "view";
		if(isset($params['action'])) {
			$action = $params['action'];
		}
					
		$this->view->tooltip($record)->addToCache();

		if(!empty($urlParams)) return $this->htmlTag("a", array(
			"rel" => $rel,
			"target" => $target, 
			"class" => $class,
			"data-tooltip" => $dataTooltip,
			"href" => $this->view->url($urlParams+$record->getRouteParams(), $record->routeName, true),
		), $text);
		$filter = new EpicDb_Filter_TagJSON();
		return $this->htmlTag("a", array(
			"rel" => $rel,
			"class" => $class,
			"target" => $target,
			"data-tooltip" => $dataTooltip,
			"data-tag-json" => $filter->single($record),
			"href" => $this->view->url($sectionParams+array(
				'action'=> $action,
			)+$record->getRouteParams(), $routeName, true),
		), $text);
	}
}