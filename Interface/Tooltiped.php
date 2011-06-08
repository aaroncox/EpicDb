<?php
/**
 * EpicDb_Interface_Tooltiped
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
interface EpicDb_Interface_Tooltiped
{
	// Returns the string URL of where to load the icon for this
	public function getIcon();
	
	// Returns the string name of this
	public function getName();
	
	// Returns the string description
	public function getDescription();
	
	// Returns an array of strings representing view helpers to execute
	public function getTooltipHelpers();
	
} // END class EpicDb_Interface_Tooltiped