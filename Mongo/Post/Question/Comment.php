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
class EpicDb_Mongo_Post_Question_Comment extends EpicDb_Mongo_Post implements EpicDb_Vote_Interface_UpOnly
{
	protected static $_collectionName = 'posts';
	protected static $_documentType = 'question-comment';
	protected static $_editForm = 'EpicDb_Form_Post_Question_Comment';

	protected $_requirements = array(
	    '_parent' => array('Document:EpicDb_Mongo_Post_Question', 'AsReference', 'Required'), 
	  );

	// Returns an array of strings representing view helpers to execute
	public function getTooltipHelpers() {
		return array("icon", "superparentTitle", "body");
	}
} // END class EpicDb_Mongo_Post_Question_Answer