<?php
/**
 *
 *
 * @author Corey Frang
 * @package EpicDb_Filter
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 */

/**
 *  EpicDb_Filter_TagJSON
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Filter
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 * @version $Id:$
 */
class EpicDb_Filter_UseCost implements Zend_Filter_Interface {	
	public function filter($value)
	{
		if ($value instanceOf Shanty_Mongo_Document) {
			return json_encode($value->export());
		} else {
			return json_decode($value, true);
		}
		return "{}";
	}
}