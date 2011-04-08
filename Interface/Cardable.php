<?php
interface EpicDb_Interface_Cardable
{
	// Returns an array of properties to put on the card
	/* for instance: array('is a' => $view->recordLink(item), 'in a guild called' => $view->recordLink(guild)) */
	public function cardProperties($view);
}