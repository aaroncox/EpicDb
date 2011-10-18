<?php
/**
 * EpicDb_Mongo_Post_Comment
 *
 * Comment (Post) Mongo Object
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Post_Comment extends EpicDb_Mongo_Post implements EpicDb_Vote_Interface_Votable
{
	public $noTypeList = true;
	protected static $_collectionName = 'posts';
	protected static $_documentType = 'comment';
	protected static $_editForm = 'EpicDb_Form_Post_Comment';
	
	/**
	 * __construct - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	// public function __construct($data = array(), $config = array())
	// {
		// $this->addRequirements(array(
		// 	'revisions' => array('DocumentSet'),
		// 	'revisions.$' => array('Document:EpicDb_Mongo_Revision'),
		// ));
		// return parent::__construct($data, $config);
	// }
	
	// Returns the string name of this
	public function getName() {
		if($subject = $this->tags->getTag('subject')?:$this->tags->getTag('parent')) {
			return $subject->name?:$subject->title;
		}
		return "";
	}
	
	public function getTooltipHelpers() {
		return array('icon', 'subjectTitle', 'body');
	}

} // END class EpicDb_Mongo_Post