<?php
/**
 * EpicDb_Mongo_Record
 *
 * undocumented class
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Record extends MW_Mongo_Document implements EpicDb_Interface_Cardable
{
	public $summaryHelper = 'recordSummary';
	
	protected static $_collectionName = 'records';
	protected static $_documentType = null;
	protected static $_documentSetClass = 'EpicDb_Mongo_Records';

	/**
	 * __construct - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function __construct($data = array(), $config = array())
	{
		$this->addRequirements(array(
			'_lastEditedBy' => array('Document:EpicDb_Mongo_Profile', 'AsReference'),
			'revisions' => array('DocumentSet'),
			'revisions.$' => array('Document:EpicDb_Mongo_Revision'),

		));
		return parent::__construct($data, $config);
	}

	public function getPropertyClass() {
		if (isset($data['_type'])) {
			return EpicDb_Mongo::db($data['_type']);
		}
	}

	public function getIcon() {
		if($this->icon) {
			return $this->icon;
		}
		return "/images/icons/unknown.jpg";
	}

	public function cardProperties($view) {
		return array(
			'is a' => $view->recordTypeLink($this)
		);
	}
	
	public function getMyFollowers() {
		return $this->getFollowers($this);
	}
	public static function getFollowers($record, $limit = 9999) {
		return $return = EpicDb_Mongo::db('profile')->fetchAll(array("following" => $record->createReference()), array(), $limit);
	}
	
	public function getAdminForms() {
		$forms = array();
		$forms['changeIcon'] = new EpicDb_Form_Record_Icon(array("record" => $this, "title" => "Change Icon", "description" => "Change the Icon that this record uses, image should be an 80x80 jpg/png/gif."));
		return $forms;
	}
	
} // END class EpicDb_Mongo_Record