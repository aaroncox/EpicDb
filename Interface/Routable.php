<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
interface EpicDb_Interface_Routable
{
	// Returns a string of the route name used for this object
	// public $routeName;
	
	// Returns an array of the default route paramaters.
	public function getRouteParams();
} // END class EpicDb_Interface_TagMeta