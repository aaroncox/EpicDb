<?php
/**
 * 
 *
 * @author Corey Frang
 * @package EpicDb_Form
 * @subpackage Element
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 */

/**
 *  EpicDb_Form_Element_Tags
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Form
 * @subpackage Element
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 * @version $Id:$
 */
class EpicDb_Form_Element_Tags extends Zend_Form_Element_Hidden {
	public $helper = 'formTags';

	public function __construct($spec, $options = null)
	{
		if (is_string($spec) && ((null !== $options) && is_string($options))) {
			$options = array('label' => $options);
		}
		if (isset($options['class'])) $options['class'].=' epic-tags';
		else $options['class'] = 'epic-tags';
		
		if (!empty($options['recordType'])) {
			$type = $options['recordType'];
		} else {
			$type = false;
		}
		$filter = new EpicDb_Filter_TagJSON(array('type' => $type));
		if (isset($options['filters'])) {
			$options['filters']["TagJSON"] = array('type'=>$type);
		} else {
			$options['filters'] = array("TagJSON" => $filter);
		}
		$hidden = parent::__construct($spec, $options);
	}
	
	public function getTags() {
		$value = $this->getValue();
		var_dump($value);
		$filter = $this->getFilter("TagJSON");
		return $filter->toArray($this->getValue());
	}

}