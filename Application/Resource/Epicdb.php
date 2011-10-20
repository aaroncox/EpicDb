<?php
class EpicDb_Application_Resource_Epicdb extends Zend_Application_Resource_ResourceAbstract {
  /**
   * Loads configuration data 
   *
   * @return void
   * @author Corey Frang
   **/
  public function init()
  {
	  $bootstrap = $this->getBootstrap();
    $bootstrap->bootstrap('mongo');
    $config = $this->getOptions();
		if (isset($config['site']['profile']['id']) && isset($config['site']['profile']['type'])) {
			$type = $config['site']['profile']['type'];
			$id = (int) $config['site']['profile']['id'];
			$query = array(
				'id' => $id,
			);
			$profile = EpicDb_Mongo::db($type)->fetchOne($query);
			EpicDb_Mongo::setSiteProfile($profile);
		}
  }
}