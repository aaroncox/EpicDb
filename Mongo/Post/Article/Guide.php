<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Mongo_Post_Article_Guide extends EpicDb_Mongo_Post implements EpicDb_Vote_Interface_Votable
{
	protected static $_documentType = 'guide';
  protected static $_editForm = 'EpicDb_Form_Post_Article_Guide';
 
	protected $_requirements = array(
	    'class' => array('Document:EpicDb_Mongo_Tags'), 
	  );
	
	public function getRouteParams() {
		return parent::getRouteParams()+array('slug' => $this->slug);
	}
} // END class EpicDb_Mongo_Post_Article_Guide extends EpicDb_Mongo_Post_Article