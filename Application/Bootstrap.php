<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Application_Bootstrap extends MW_Application_Bootstrap
{
	/**
  * Get the plugin loader for resources - makes sure to add prefix path to the loader
  *
  * @return Zend_Loader_PluginLoader_Interface
  */
  public function getPluginLoader()
  {
    if ($this->_pluginLoader === null) {
      parent::getPluginLoader();
      $this->_pluginLoader->addPrefixPath('EpicDb_Application_Resource', dirname(__FILE__).'/Resource/');
    }
    return $this->_pluginLoader;
  }

} // END class EpicDb_Application_Bootstrap extends MW_Application_Bootstrap