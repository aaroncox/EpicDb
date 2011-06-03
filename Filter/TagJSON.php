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
class EpicDb_Filter_TagJSON implements Zend_Filter_Interface {
	public function filter($value)
	{
		if (is_array($value)) {
			// tags array
			$map = function($value) {
				$ref = $value->createReference();
				$ref['$id'].='';
				return $ref;
			};
			$mapped = array_map($map, $value);
			return json_encode($mapped);
		} else {
			$refs = json_decode($value);
			if (!is_array($refs)) $refs = array();
			return json_encode($refs);
		}
	}
	
	public function toArray($value)
	{
		$return = array();
		$refs = json_decode($value,true);
		if (!is_array($refs)) $refs = array();
		foreach($refs as $value) {
			if ( isset( $value['$new'] ) ) {
				$ref = EpicDb_Mongo::newDoc('tag');
				$ref->name = $value['name'];
				$ref->tags->setTag('author', EpicDb_Auth::getInstance()->getUserProfile());
				$ref->_created = time();
				$ref->save();
				$return[] = $ref;
			} elseif ($value) {
				$return[] = EpicDb_Mongo::resolveReference($value);
			}
		}
		return $return;
	}
}