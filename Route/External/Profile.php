<?php
class EpicDb_Route_External_Profile extends EpicDb_Route_Profile {
	public function assemble($data = array(), $reset = false, $encode = false, $partial = false)
	{
		/*
				Usage Example: 
				resources.router.routes.r2db-profile.type = "EpicDb_Route_External_Profile"
				resources.router.routes.r2db-profile.route = ":type/:id/:action/*"
				resources.router.routes.r2db-profile.defaults.hostname = "http://r2-db.com/"
		*/
		$hostname = "/";
		if(isset($this->_defaults['hostname'])) {
			$hostname = $this->_defaults['hostname'];			
		}
		$result = parent::assemble($data, $reset, $encode, $partial);
		return $hostname.$result;
	}
}