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
  protected static $_documentType = 'user';
	protected static $_editForm = 'EpicDb_Form_Profile_User';
	
	public function __construct($data = array(), $config = array())
	{
	  // handled here in order to deal with subclassed resources...
	  if (!is_array($this->_requirements)) $this->_requirements = array();
	  $this->_requirements += array(
			'user' => array('Document:MW_Auth_Mongo_User', 'AsReference', 'Required'),
			'following' => array('DocumentSet:EpicDb_Mongo_DocumentSet_Dynamic'),
			'following.$' => array('Document:MW_Mongo_Document', 'AsReference', 'Required'),
			'blocking' => array('DocumentSet:EpicDb_Mongo_DocumentSet_Dynamic'),
			'blocking.$' => array('Document:MW_Mongo_Document', 'AsReference', 'Required'),
	    );
	  $return = parent::__construct($data, $config);
	}
	
	public function getDescription() {
		if($this->bio) return $this->bio; 
		return '';
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

  public function isBlocking(MW_Mongo_Document $record)  
  {
    $id = (string) $record->_id;
    foreach ($this->blocking as $key => $target) {
      if ( (string) $target->_id == $id) {
        return true;
      } 
    }    
    return false;
  }

	public function block($record)
  {
    if (!$this->isBlocking($record))
      $this->blocking->addDocument($record);
		return $this;
  }

  public function unblock($record)
  {
    $id = (string)$record->_id;
    foreach ($this->blocking as $key => $target) {
      if ((string)$target->_id == $id) {
        $this->blocking->setProperty($key, null);
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
	
	public function getIcon() {
		if($this->email) {
			$helper = new EpicDb_View_Helper_Gravatar();
			return $helper->gravatar($this->email)->url();
		}
		return "http://s3.r2-db.com/unknown.jpg";
	}
} // END class EpicDb_Mongo_Profile_User