<?php
/**
 * EpicDb_Mongo_Tags
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Post_Poll_Options extends Shanty_Mongo_DocumentSet
{
	protected $_requirements = array(
		'$' => array('Document:EpicDb_Mongo_Post_Poll_Option'),
	);
	
	// $options = array()
	public function setOptions($options) 
	{
		foreach($this as $idx => $option) {
			$this->setProperty($idx, null);
		}
		foreach($options as $idx => $option) {
			if(!$option->id) {
				$option->id = new MongoId();
			}
			$this->setProperty($idx, $option);
		}
		return $this;
	}
} // END class EpicDb_Mongo_Tags