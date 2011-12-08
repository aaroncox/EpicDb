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
	protected $_limit;
	
	// json format:
	//
	// [{
	//	"type" => "website",
	//	"id" => 1,
	//	"name" => "R2-Db.com"
	// }, ....

	public function __construct($options = array()) {
		if ( isset( $options['limit'] ) ) {
			$this->_limit = (int) $options['limit'];
		}
		if ( isset( $options['type'] ) ) {
			$this->_type = explode( ",", $options['type'] );
			if ( empty( $options["type"] ) || !count( $this->_type ) ) {
				$this->_type = false;
			}
		}
	}
	
	public function limit($output) {
		if($this->_limit) {
			$output = array_slice($output, 0, $this->_limit);				
		}
		return $output;
	}
	
	public function single($tag) {
		$filter = new MW_Filter_Slug();
		$icon = null;
		if ( method_exists( $tag, "getIcon" ) ) {
			$icon = $tag->getIcon();
		}
		return json_encode(array(
			"type" => $tag->_type,
			"id" => $tag->id,
			"name" => $tag->name?:$tag->title,
			"slug" => $tag->slug?:$filter->filter($tag->title?:$tag->name),
			"icon" => $icon,
		));
	}

	public function filter($value)
	{

		// array input -> json output
		if (is_array($value)) {

			// tags array
			$type = $this->_type;
			$contained = array();
			foreach ( $value as $tag ) {
				if ( !$tag ) continue;
				if ( $type && !in_array( $tag->_type, $type ) ) {
					continue;
				}
				$contained[ $tag->_type . ":" . $tag->id ] = array(
					"type" => $tag->_type,
					"id" => $tag->id,
					"name" => $tag->name
				);
			}
			return json_encode($this->limit(array_values($contained)));
		} else {

			// validate json
			$refs = json_decode($value, true);
			if (!is_array($refs)) {
				$refs = array();
			}

			$contained = array();
			foreach( $refs as $ref ) {
				if ( empty($ref["type"]) || $this->_type && !in_array($ref["type"], $this->_type) ) {
					continue;
				}
				if ( isset($ref["new"]) ) {
					$contained[] = $ref;
				} else {
					$contained[ $ref["type"].":".$ref["id"] ] = $ref;
				}
			}
			return json_encode($this->limit(array_values($contained)));
		}
	}

	// to get an array of documents from a json string
	public function toArray($value)
	{
		$return = array();
		$refs = json_decode( $value, true );
		if ( !is_array( $refs ) ) {
			$refs = array();
		}
		$added = array();
		foreach ( $refs as $value ) {
			if ( isset( $value['new'] ) ) {

				// TODO: Validate the ability to create
				$ref = EpicDb_Mongo::newDoc( $value['type'] );
				$ref->name = $value['name'];
				$ref->tags->setTag('author', EpicDb_Auth::getInstance()->getUserProfile());
				$ref->_created = time();
				$ref->save();
				$return[] = $ref;

			} elseif ($value) {
				if ( empty($value["type"]) ) {
					continue;
				}

				try {
					$db = EpicDb_Mongo::db($value['type']);
					$record = $db->fetchOne(array( "id" => (int)$value['id'] ));
				} catch ( MW_Mongo_Exception $e ) {
					$record = false;
				}

				if ( !$record ) {
					continue;
				}

				// make sure we found one - and that it matches our type filter
				if ( $this->_type && !in_array( $record->_type, $this->_type ) ) {
					continue;
				}

				$id = $record->_type.":".$record->id;
				if ( isset( $added[$id] ) ) {
					continue;
				}
				$return[] = $record;
				$added[$id] = $id;
			}
		}
		return $this->limit($return);
	}
}