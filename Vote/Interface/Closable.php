<?php
/**
 * 
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @subpackage Interface
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 */

/**
 *  EpicDb_Vote_Interface_Closable
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @subpackage Interface
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 * @version $Id:$
 */
interface EpicDb_Vote_Interface_Closable {
	public function close($profiles, $reason, $dupe = false);
	public function reopen($profiles);
}