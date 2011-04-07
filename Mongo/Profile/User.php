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
			'user' => array('Document:MW_Auth_Mongo_Role', 'AsReference', 'Required'),
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

	public static function getProfile($user) {
		if(!$user) return null;
		if($user instanceOf EpicDb_Mongo_Profile_User) {
			return $user;
		}
		$query = array();
		if($user instanceOf MW_Auth_Mongo_Role) {	
			$query['user'] = $user->createReference();
    } elseif(is_string($user)) {
			$query = array("name" => new MongoRegex('/'.$user.'/i'));
			$user = false;
		}
		$profile = self::fetchOne($query);
		// var_dump($query, $profile, $user); exit;
		if(!$profile && $user) {
			$slug = new MW_Filter_Slug();
			$profile = new self();
			$profile->user = $user;
      $profile->slug = $slug->filter($user->name);
			$profile->username = strtolower($slug->filter($user->name));
			$profile->name = $slug->filter($user->name);
			$profile->display_email = strtolower($slug->filter($user->email));
			$profile->save();
		}
		return $profile;
	}
} // END class EpicDb_Mongo_Profile_User