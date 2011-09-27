<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_View_Helper_TopSeeds extends MW_View_Helper_HtmlTag
{
	public function topSeeds($record) {
		$query = array(
			'types' => $record->_type
		);
		$seeds = EpicDb_Mongo::db('seed')->fetchAll($query);
		$html = ' ';
		foreach($seeds as $seed) {
			$html .= $this->view->seedStub($seed, $record);
		}
		return $this->htmlTag('div', array('class' => 'transparent-bg-blue rounded'), $html);
	}
} // END class EpicDb_View_Helper_TopSeeds extends MW_View_Helper_HtmlTag