<?php
/**
 * EpicDb_Mongo_Image
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 **/
class EpicDb_Mongo_Image extends EpicDb_Auth_Mongo_Resource_Document
{
  protected static $_collectionName = 'images';
	protected static $_documentType = 'image';
	
	protected $_requirements = array();

}