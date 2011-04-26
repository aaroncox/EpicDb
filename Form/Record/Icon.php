<?php
/**
 * EpicDb_Form_Advertisement
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Form_Record_Icon extends EpicDb_Form_Record
{
	protected $_record = null;

	/**
	 * getProfile - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function getRecord()
	{
		if($this->_record) return $this->_record;
		throw new Exception("Record for this entry is undefined.");
	}
	/**
	 * setProfile - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function setRecord($record)
	{
		if(!$record instanceOf EpicDb_Mongo_Record) {
			throw new Exception("This isn't an Record.");
		}
		$this->_record = $record;
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
		$record = $this->getRecord();
		$this->addElement("file", "icon", array(
			'label' => 'File to Upload',
			'required' => true,
		));
		$this->setDefaults($record->export());
		$this->setButtons(array("save" => "Save"));
	}
	
	public function process($data) {
		$record = $this->getRecord();
		$file = $_FILES['icon'];
		$processor = new PHPThumb_GdThumb($file['tmp_name']);
		switch($file['type']) {
			case "image/png":
				$file['ext'] = "png";
				break;
			case "image/jpeg":
			case "image/jpg":
				$file['ext'] = "jpg";
				break;
			case "image/gif":
				$file['ext'] = "gif";
				break;
			default:
				throw new Exception("Unsupported File Type: ".$file['type']);
		}
		$filename = null;
		$append = null;
		$filename = $file['tmp_name'];
		$processor->resize(80, 80)->save($filename);
		$config = Zend_Registry::getInstance()->amazon_s3; 
		$storage = new Zend_Service_Amazon_S3($config['access'], $config['secret']);
		$profile = EpicDb_Auth::getInstance()->getUserProfile();
		$path = $config['bucket']."/icons/".$profile->id."/".$record->_id.".".$file['ext'];
		$storage->putFile($filename, $path, array(
			Zend_Service_Amazon_S3::S3_ACL_HEADER => Zend_Service_Amazon_S3::S3_ACL_PUBLIC_READ, 
			Zend_Service_Amazon_S3::S3_CONTENT_TYPE_HEADER => $file['type'],
		));			
		$filter = new MW_Filter_HttpAddress();
		$url = $filter->filter($path);
		$record->icon = $url;
		$record->save();
		return true;
	}
	public function render()
	{
		foreach($this->getElements() as $element) {
			$element->setAttrib('class', 'ui-state-default');
		}
		$this->save->setAttrib('class','login r2-button ui-state-default ui-corner-all');
		$this->getDecorator('HtmlTag')->setOption('class','r2-form transparent-bg rounded')->setOption('id', 'ad-edit');
		return parent::render();
	}	
} // END class EpicDb_Form_Profile
