<?php
/**
 * R2Db_Form_Post_Comment
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Form_Seed extends EpicDb_Form
{
	protected $_isNew = false;
	protected $_recordType = 'seed';
	protected $_seed = null;
	
	/**
	 * getSeed - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function getSeed()
	{
		return $this->_seed;
	}
	/**
	 * setPost($post) - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function setSeed($seed)
	{
		$this->_seed = $seed;
		$this->_isNew = $seed->isNewDocument();
		return $this;
	}
	/**
	 * init - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function init()
	{
		parent::init();
		$seed = $this->getSeed();
		$this->addElement("text", "title", array(
				'required' => true,
				'validators' => array(
					array('StringLength',150,10),
				),
				'label' => 'Title of Seed Post',
				'size' => 80,
				'description' => '150 characters or less.',
			));		
		$this->addElement("text", "tag", array(
			'required' => false, 
			'filters' => array(
				'StringTrim',
			),
			'label' => 'Tagged With',
		));
		$this->addElement("text", "tagDb", array(
			'filters' => array(
				'StringTrim',
			),
			'label' => 'Tags Database',
			'description' => 'Leave empty if tags exist on this record type',
		));
		$this->addElement("text", "tagType", array(
			'filters' => array(
				'StringTrim',
			),
			'label' => 'Tags Type',
		));
		$recordTypes = array();
		foreach(EpicDb_Mongo::db('record')->getTypesArray() as $type) {
			$recordTypes[$type] = $type;
		}
		$this->addElement("multiselect", "types", array(
			'label' => 'Affected Record Types',
			'multiOptions' => $recordTypes,
		));
		if(!$this->_isNew) {
			$this->setDefaults(array(
				'title' => $seed->title,
				'tag' => $seed->tag,
				'tagDb' => $seed->tagDb,
				'tagType' => $seed->tagType,
			));			
			if($seed->types) {
				$this->setDefaults(array(
					'types' => $seed->types,
				));
			}
		}
		$this->setButtons(array("save" => "Save"));
	}
	public function process($data) {
		if($this->isValid($data)) {
			$seed = $this->getSeed();
			$seed->title = $this->title->getValue();
			$seed->types = $this->types->getValue();
			$seed->tag = $this->tag->getValue();
			$seed->tagDb = $this->tagDb->getValue();
			$seed->tagType = $this->tagType->getValue();
			return $seed->save();
		}
		return false;
	}
} // END class R2Db_Form_Post_Comment
