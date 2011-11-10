<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Mongo_Record_Badge extends EpicDb_Mongo_Record implements EpicDb_Badge_Interface
{
	protected static $_collectionName = 'records';
	protected static $_documentType = 'badge';
	protected static $_editForm = 'EpicDb_Form_Record_Badge';
	
	protected $_requirements = array(
		'options' => array('Document:MW_Mongo_Document'),
	);
	
	public function getBadgeHelper() {
		return $this->helper?:'manual';
	}

	public function getBadgeOptions() {
		return $this->options->export();
	}
	
	public function getBadgeQuality() {
		return $this->quality?:'normal';
	}
} // END class EpicDb_Mongo_Record_Badge extends EpicDb_Mongo_Record implements EpicDb_Badge_Interface