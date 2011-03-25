<?php
/**
 * EpicDb_Route_Profile
 *
 * Route for Profile Documents
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Route_Profile extends Zend_Controller_Router_Route
{
	static public $types = array('user','group');

  public static function getInstance(Zend_Config $config)
  {
    $defaults = array(
      'controller' => 'profile',
      'action' => 'view',
      'module' => 'default', 
      'slug' => '-',
    );
    $reqs = array(
      'type' => implode('|',static::$types),
    );

		$route = $config->route;
    $reqs = ($config->reqs instanceof Zend_Config) ? array_merge($config->reqs->toArray(),$reqs) : $reqs;
    $defs = ($config->defaults instanceof Zend_Config) ? $config->defaults->toArray() + $defaults : $defaults;
		// var_dump($route, $defs, $reqs);
    return new static($route, $defs, $reqs);
  }
  
  public function assemble($data = array(), $reset = false, $encode = false, $partial = false)
  {
		$profile = false;
		if(isset($data['profile'])) {
			$profile = $data['profile'];
		} elseif(isset($this->_values['profile'])) {
			$profile = $this->_values['profile'];
		} 
		if(!$profile instanceOf EpicDb_Mongo_Profile && $profile instanceOf Shanty_Mongo_Document) {
			$profile = EpicDb_Mongo_Schema::getInstance()->getCollectionForType('profile')->find($profile->_id);
		}
    if($profile instanceOf EpicDb_Mongo_Profile) {
      $data['id'] = $profile->id;				
      $data['type'] = $profile->_type;				
			if(!isset($data['slug'])) {
				$slug = new MW_Filter_Slug();
				$data['slug'] = $slug->filter($profile->name);
			}
      unset($data['profile']);
    } else {
			throw new Exception("Expected EpicDb_Mongo_Profile, got ".get_class($data['profile']));
		}
		// if(!isset($data['type'])) {
		// 	var_dump($data, $profile); exit;
		// }
		if(!isset($data['type'])) {
			var_dump($profile, $data); exit;			
		}
    return parent::assemble($data, $reset, $encode, $partial);
  }

	public function getProfile($params)
	{
		return EpicDb_Mongo_Schema::getInstance()->getCollectionForType($params['type'])->fetchOne(array("id" => (int) $params['id']));		
	}

  public function match($path, $partial = false)
	{
		$match = parent::match($path, $partial);
		if ($match) {
			$profile = $this->getProfile($match);
			if (!$profile) {
				$this->_values = array(); return false;
			}
			$match['profile'] = $profile;
			$this->_values['profile'] = $profile;
		}
		return $match;
	}
} // END class EpicDb_Route_Profile