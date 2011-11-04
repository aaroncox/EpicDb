<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_View_Helper_Stash extends Zend_View_Helper_Abstract
{
	public function sampleData() {
		$frontendOptions = array(
		   'lifetime' => 3000,
		   'automatic_serialization' => true
		);
		$backendOptions = array(
			'cache_dir' => '/tmp/'
		);
		$cache = Zend_Cache::factory(
		    'Output', 'Memcached', $frontendOptions, $backendOptions
		);
		try {
			if(!$html = $cache->load('sample_data_stash')) {	
				$html = "";				
				foreach(EpicDb_Mongo::db('advanced-class')->fetchAll() as $record) {
					$html .= $this->iconify($record);
				} 
				foreach(EpicDb_Mongo::db('class')->fetchAll() as $record) {
					$html .= $this->iconify($record);
				} 
				foreach(EpicDb_Mongo::db('race')->fetchAll() as $record) {
					$html .= $this->iconify($record);
				} 
				foreach(EpicDb_Mongo::db('profession')->fetchAll() as $record) {
					$html .= $this->iconify($record);
				} 
				foreach(EpicDb_Mongo::db('faction')->fetchAll() as $record) {
					$html .= $this->iconify($record);
				} 
				foreach(EpicDb_Mongo::db('companion')->fetchAll() as $record) {
					$html .= $this->iconify($record);
				} 
				$cache->save($html, 'sample_data_stash');
			}
		} catch (Exception $e) {

		}

		
		return $html;
	}
	public function loadStash() {
		$html = "";
		if($profile = EpicDb_Auth::getInstance()->getUserProfile()) {
			foreach($profile->stashed->getTags() as $record) {
				$html .= $this->iconify($record);
			}
		}
		return $html;
	}
	public function iconify($doc) {
		return $this->view->htmlTag("li", array("class" => "stash-item"), 
			$this->view->iconLink($doc, array("class" => "icon small inline-flow"))."".
			$this->view->htmlTag("span", array("class" => "record"), $this->view->recordLink($doc, array("rel" => "no-tooltip")))
		);
	}
	public function control($id, $icon, $tooltip) {
		return $this->view->htmlTag("div", array(
			"class" => "ui-state-default control inline-flow rounded has-tooltip",
			"id" => "r2-stash-".$id,
			"data-tooltip" => $tooltip,
		), $this->view->htmlTag("span", array("class" => "ui-icon ui-icon-".$icon)," "))."";
	}
	public function controls() {
		return $this->view->htmlTag("div", array("class" => "controls"),
			$this->control("grid", "calculator", "Display this stash in a grid format.")."".
			$this->control("list", "contact", "Display this stash in a search result format.")."".
			$this->control("trash", "trash", "Drag things here to remove them.")."".
			$this->control("export", "clipboard", "Export this stash as Text, BBCode or Markdown.")
		);
	}
	public function stash() {
		$dataParams = array();
		if($profile = EpicDb_Auth::getInstance()->getUserProfile()) {
			$dataParams["data-stash-url"] = $this->view->url(array('profile' => $profile, 'action' => 'stash'), 'profile', true);
		}
		return $this->view->htmlTag("div", array("id" => "r2-stash", "class" => "r2-tooltip rounded")+$dataParams, 
			$this->view->htmlTag("div", array("class" => "stash-header"), 
				"Stash"."".
				$this->controls()
			)."".
			$this->view->htmlTag("ul", array("id" => "", "class" => "stash-content layout-grid"), 
				$this->loadStash()
			).""
		);
	}
} // END class EpicDb_View_Helper_Stash extends Zend_View_Helper_Abstract