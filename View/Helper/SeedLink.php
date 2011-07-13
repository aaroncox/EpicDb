<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_View_Helper_SeedLink extends MW_View_Helper_HtmlTag
{
	public function seedLink($seed, $record, $options = array()) {
		$text = null;
		if(isset($options['text'])) $text = $options['text'];
		$html = $this->htmlTag("a", array(
			'title' => $seed->renderTitle($record),
			"href" => $this->view->url(array(
					'controller'=>'record',
					'action'=>'seed',
					'record'=>$record,
					'seed'=>$seed->id,
				), 'record', true),
			), $text?:$seed->renderTitle($record));
		return $html;
		
	}
} // END class EpicDb_View_Helper_SeedLink extends MW_View_Helper_HtmlTag