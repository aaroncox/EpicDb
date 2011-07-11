<?php
/**
 * EpicDb_Form_Profile_Icon
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Form_Profile_Icon extends EpicDb_Form_Profile
{
	/**
	 * init - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function init()
	{
		parent::init();
		$profile = $this->getProfile();
		$this->removeElement("name");
		$this->addElement("file", "icon", array(
			'label' => 'Image to Upload',
			'required' => true,
		));
		$this->setDefaults($profile->export());
		$this->setButtons(array("save" => "Save"));
	}
	
	public function process($data) {
		$profile = $this->getProfile();
		$profile->logo = $this->optimizeAndUpload('logo', $profile);
		$profile->icon = $this->optimizeAndUpload('icon', $profile);
		$profile->save();
		return true;
	}
	
	public function optimizeAndUpload($type, $profile) {
    $file = $_FILES['icon'];
    $processor = new PHPThumb_GdThumb($file['tmp_name']);
    $extension = strtolower($processor->getFormat());
    $append = "";

    switch($type) {
      case "icon":
        $append = ".icon";
        $processor->resize(80, 80);
        break;
      case "logo":
      	$append = ".logo";
        $processor->resize(300, 300);
        break;
      default:
        throw new Exception("Unrecognized Optimization Method: ".$type);
    }

    $storage = R2Db_S3::getInstance();
    $path = $storage->makeMediaPath($profile, $extension, $append);
    $storage->putMediaFile($processor, $path);

    $filter = new MW_Filter_HttpAddress();
    return $filter->filter($path);
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
} // END class EpicDb_Form_Profile_Icon
