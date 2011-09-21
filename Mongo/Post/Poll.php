<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Mongo_Post_Poll extends EpicDb_mongo_Post
{
	public $routeName = "polls";
	protected static $_documentType = 'poll';
	protected static $_editForm = 'EpicDb_Form_Post_Poll';
	
	protected $_requirements = array(
    'options' => array('DocumentSet:EpicDb_Mongo_Post_Poll_Options', 'Required'), 
  );
	
	public function getRouteParams() {
		$filter = new MW_Filter_Slug();
		return parent::getRouteParams()+array('slug' => $filter->filter($this->title));
	}
	
} // END class EpicDb_Mongo_Post_Poll extends EpicDb_mongo_Post