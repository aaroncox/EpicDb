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
	
	public function getBadgeHelper() {}

	public function getBadgeOptions() {}
} // END class EpicDb_Mongo_Record_Badge extends EpicDb_Mongo_Record implements EpicDb_Badge_Interface