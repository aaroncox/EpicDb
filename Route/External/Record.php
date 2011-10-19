<?php
class EpicDb_Route_External_Record extends EpicDb_Route_Record {
	public function assemble($data = array(), $reset = false, $encode = false, $partial = false)
	{
		/*
				Usage Example: 
				resources.router.routes.r2db-record.type = "EpicDb_Route_External_Record"
				resources.router.routes.r2db-record.route = ":type/:id/:action/*"
				resources.router.routes.r2db-record.defaults.hostname = "http://r2-db.com/"
		*/
		$hostname = "/";
		if(isset($this->_defaults['hostname'])) {
			$hostname = $this->_defaults['hostname'];			
		}
		$result = parent::assemble($data, $reset, $encode, $partial);
		return $hostname.$result;
	}
}