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
class EpicDb_Mongo_Post_Question_Answer extends EpicDb_Mongo_Post implements EpicDb_Vote_Interface_Acceptable, EpicDb_Vote_Interface_Votable
{
	public $noTypeList = true;
	
	protected static $_documentType = 'answer';
	protected static $_editForm = 'EpicDb_Form_Post_Question_Answer';

	protected $_requirements = array(
	    '_parent' => array('Document:EpicDb_Mongo_Post_Question', 'AsReference', 'Required'), 
	  );
	
	// Returns the string name of this
	public function getName() {
		if($parent = $this->tags->getTag('parent')?:$this->_parent) {
			return $parent->title;
		}
		return "";
	}
	
	// Returns an array of strings representing view helpers to execute
	public function getTooltipHelpers() {
		return array("parentTitle", "body");
	}
	
	public function delete() {
		parent::delete();
		$this->_parent->save();
	}

	public function save()
	{
		$this->closed = $this->_parent->closed;
		$this->disableRep = $this->_parent->disableRep;
		return parent::save();
	}

	public function getParentResource()
	{
		return new EpicDb_Auth_Resource_QAPost( $this->isReputationDisabled() );
	}

} // END class EpicDb_Mongo_Post_Question_Answer