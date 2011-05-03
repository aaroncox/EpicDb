<?php
/**
 * EpicDb_Form_Wiki
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Form_Wiki extends EpicDb_Form
{	
	protected $_wiki = null;
	protected $_isNew = false;
	protected $_record = null;
	protected $_type = null;
	
	public function setType($type) {
		$this->_type = $type;
		return $this;
	}
	
	public function getType() {
		return $this->_type;
	}
	
	/**
	 * setRecord - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function setRecord($record)
	{
		$this->_record = $record;
		return $this;
	}
	/**
	 * getRecord - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function getRecord()
	{
		return $this->_record;
	}
	
	/**
	 * getPost - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function getWiki()
	{
		if($this->_wiki) return $this->_wiki;		
		$class = EpicDb_Mongo::dbClass('wiki');
		$wiki = $this->_wiki = new $class();
		$this->_isNew = true;
		$this->setWiki($wiki);
		return $wiki;
	}
	/**
	 * setPost($post) - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function setWiki($wiki)
	{
		$this->_wiki = $wiki;
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
		$wiki = $this->getWiki();
		if($this->_isNew) {
			if($type = $this->getType()) {
				$this->addElement("hidden", "type", array(
					'required' => true,
					'value' => $type,		
				));			
			} else {
				$this->addElement("text", "type", array(
					'required' => true,
					'label' => 'Type',			
				));							
			}
		}
		$this->addElement("text", "title", array(
				'required' => true,
				'label' => 'Title',
		));
		$this->addElement("markdown", "source", array(
				'order' => 100,
				'required' => true,
				'class' => 'markDownEditor',
				'label' => 'Edit Wiki',
			));
		if(!$this->_isNew) {
			$this->setDefaults(array("source" => $wiki->source, "type" => $wiki->type, "title" => $wiki->header));			
		} 
		$this->setButtons(array("save" => "Save"));
	}

	public function save() {
		$wiki = $this->getWiki();
		if($this->source) {
			$wiki->source = $this->source->getValue();
			$wiki->html = $this->source->getRenderedValue();			
		}
		if($this->title) {
			$wiki->header = $this->title->getValue();
		}
		if($this->type) {
			$wiki->type = $this->type->getValue();
		}
		if($this->_isNew) {
			$wiki->record = $this->_record;
		}
		return $wiki->save();
		// $post = $this->getPost();
		// if($this->source) {
		// 	$post->source = $this->source->getValue();
		// 	$post->body = $this->source->getRenderedValue();			
		// }		
		// if($this->_isNew) {
		// 	$post->grant(MW_Auth::getInstance()->getUser());			
		// 	// If we don't have viewers for some reason, lets make em
		// } else {
		// 	$post->_lastEdited = time();
		// 	$post->_lastEditedBy = MW_Auth::getInstance()->getUserProfile()->createReference();
		// }
		// if($this->requestType) {
		// 	$post->_requestType = $this->requestType->getValue();
		// }
		// if($parentId = $this->parent->getValue()) {
		// 	$post->_parent = EpicDb_Mongo_Post::find($parentId)->touch();
		// }
		// 
		// // var_dump($post); exit;
		// return $post->save();
	}
	public function process($data) {
		if($this->isValid($data)) {
			$this->save();
			return true;
		}
		return false;
	}
	public function render() {
		$this->removeDecorator('FloatClear');    
		return parent::render();
	}

} // END class EpicDb_Form_Wiki