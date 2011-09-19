<?php
/**
 * EpicDb_Mongo_Post_Message
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Post_Message extends EpicDb_Mongo_Post implements EpicDb_Vote_Interface_UpOnly
{
	public $noTypeList = true;
	protected static $_documentType = 'message';
  protected static $_editForm = 'EpicDb_Form_Post_Message';
	
} // END class EpicDb_Mongo_Post_Message