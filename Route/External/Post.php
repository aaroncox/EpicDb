<?php
class EpicDb_Route_External_Post extends EpicDb_Route_Post {
	public function assemble($data = array(), $reset = false, $encode = false, $partial = false)
	{
		/*
				Usage Example: 
				resources.router.routes.r2db-post.type = "EpicDb_Route_External_Post"
				resources.router.routes.r2db-post.route = ":type/:id/:action/*"
				resources.router.routes.r2db-post.defaults.hostname = "http://r2-db.com/"
		*/
		$hostname = $this->_defaults['hostname']?:"/";
		$result = parent::assemble($data, $reset, $encode, $partial);
		return $hostname.$result;
	}
}