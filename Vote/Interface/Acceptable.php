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
 *  EpicDb_Vote_Interface_Acceptable
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Vote
 * @subpackage Interface
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 * @version $Id:$
 */
interface EpicDb_Vote_Interface_Acceptable {
  public function isReputationDisabled(); // Returns true or false based on whether the reputation gain/loss is disabled	
}