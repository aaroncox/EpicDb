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
	protected $_type;

	public function __construct($options = array()) {
		if (isset($options['type'])) $this->_type = $options['type'];
	}
	
	public function filter($value)
	{
		if (is_array($value)) {
			// tags array
			$type = $this->_type;
			$contained = array();
			$mapped = array_filter($value, function( $value ) use($type, &$contained) {
				if (in_array($value->_id."", $contained)) return false;
				if ($type && $value->_type != $type ) return false;
				$contained[] = $value->_id."";
				return true;
			});
			
			$map = function($value) {
				$ref = $value->createReference();
				$ref['$id'].='';
				return $ref;
			};
			$mapped = array_map($map, $mapped);
			return json_encode($mapped);
		} else {
			$refs = json_decode($value, true);
			$contained = array();
			if (!is_array($refs)) $refs = array();
			$refs = array_filter($refs, function($ref) use(&$contained) {
				if (in_array($ref['$id'], $contained)) return false;
				$contained[] = $ref['$id'];
				return true;
			});
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
				$added = array();
				$record = EpicDb_Mongo::resolveReference($value);
				$id = $record->_id."";
				if (in_array($id, $added)) continue;
				if ($record && (!$this->_type || $this->_type == $record->_type) ) {
					$return[] = $record;					
					$added[] = $id;
				}
			}
		}
		return $return;
	}
}