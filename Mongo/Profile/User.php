<?php
/**
 * EpicDb_Mongo_Profile_User
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_Profile_User extends EpicDb_Mongo_Profile
{
	
	public function __construct($data = array(), $config = array())
	{
	  // handled here in order to deal with subclassed resources...
	  if (!is_array($this->_requirements)) $this->_requirements = array();
	  $this->_requirements += array(
			'following' => array('DocumentSet'),
			'following.$' => array('Document:MW_Mongo_Document', 'AsReference', 'Required'),
	    );
	  $return = parent::__construct($data, $config);
	}
	
  public function isFollowing(MW_Mongo_Document $record)  
  {
    $id = (string) $record->_id;
    foreach ($this->following as $key => $target) {
      if ( (string) $target->_id == $id) {
        return true;
      } 
    }    
    return false;
  }

	public function follow($record)
  {
    if (!$this->isFollowing($record))
      $this->following->addDocument($record);
		return $this;
  }

  public function unfollow($record)
  {
    $id = (string)$record->_id;
    foreach ($this->following as $key => $target) {
      if ((string)$target->_id == $id) {
        $this->following->setProperty($key, null);
      }
    }
		return $this;
  }

} // END class EpicDb_Mongo_Profile_User