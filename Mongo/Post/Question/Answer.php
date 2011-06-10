<?php
/**
 * EpicDb_Mongo_Post_Question_Answer
 *
 * undocumented class
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Post_Question_Answer extends EpicDb_Mongo_Post_Question implements EpicDb_Vote_Interface_Acceptable, EpicDb_Vote_Interface_Votable
{
	protected static $_documentType = 'answer';
	protected static $_editForm = 'EpicDb_Form_Post_Question_Answer';

	protected $_requirements = array(
	    '_parent' => array('Document:EpicDb_Mongo_Post_Question', 'AsReference', 'Required'), 
	  );
	
	// Returns the string name of this
	public function getName() {
		if($parent = $this->tags->getTag('parent')) {
			return $parent->title;
		}
		return "";
	}
	
	// Returns an array of strings representing view helpers to execute
	public function getTooltipHelpers() {
		return array("icon", "parentTitle", "body");
	}
	

} // END class EpicDb_Mongo_Post_Question_Answer