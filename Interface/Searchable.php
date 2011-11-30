<?php
interface EpicDb_Interface_Searchable
{
	// Generates the appropriate data for the 'search' collection and appends whatever the children has passed in as well.
	// Returns an array of data to assign to the search record.
	//   Example: array('name' => 'Something', 'Description' => 'Description', 'tags' => EpicDb_Mongo_Tags)
	public function getSearchCache($data);
}