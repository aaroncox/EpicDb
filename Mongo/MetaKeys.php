<?php
/**
 * EpicDb_Mongo_MetaKeys
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Mongo_MetaKeys extends MW_Mongo_Document
{
	protected static $_collectionName = 'metaKeys';
	protected static $_documentType = null;
	protected static $_editForm = 'EpicDb_Form_MetaKeys';
	
	protected $_requirements = array(
		'name' => array('Validator:Alnum', 'Required'),
		'requirements' => array("Array"),
		'formElement' => array("Document"),
		'formElement.options' => array("Array"),
		'recordType' => array("Array"),
	);
	
	protected static $_metaKeys = false;
	protected static $_requirementsArray = false;
	protected static $_titlesArray = false;
	
	protected static function _getData() {
		if(static::$_requirementsArray === false) {
			$result = static::fetchAll();
			$requirements = array();
			$titles = array();
			foreach($result as $metaKey) {
				static::$_metaKeys[$metaKey->name] = $metaKey;
				$titles[$metaKey->name] = $metaKey->title;
				$requirements[$metaKey->name] = $metaKey->requirements;
			}
			static::$_requirementsArray = $requirements;
			static::$_titlesArray = $titles;
		}
	}
	
	public static function getMetaKey($key, $createFlag = false) {
		static::_getData();
		if(isset(static::$_metaKeys[$key])) { 
			return static::$_metaKeys[$key];
		} 
		if($createFlag) {
			$doc = new static;
			$doc->name = $key;
			$doc->title = $key;
			return $doc;			
		}
		return false;
	}
	
	public static function getFormElementsArray($type = false) {
		static::_getData();
		$elements = array();
		$orders = array();
		foreach(static::$_metaKeys as $key => $data) {
			if(!($data->formElement && $data->formElement->type)) continue;
			if($type && !($data->recordType && in_array($type, $data->recordType))) continue;
			if($data->formElement) $elements[$key] = $data->formElement->export();
			if(empty($elements[$key]['options'])) $elements[$key]['options'] = array();
			if(empty($elements[$key]['options']['label'])) $elements[$key]['options']['label'] = static::$_titlesArray[$key];
			if(!empty($elements[$key]['options']['order'])) {
				for ($order = (int)$elements[$key]['options']['order'];isset($orders[$order]);$order++);
				$orders[$order] = true;
				$elements[$key]['options']['order'] = $order;
			}
		}
		return $elements;
	}
	
	public static function getMetaKeys() {
		static::_getData();
		return static::$_metaKeys;
	}
	
	public static function getRequirementsArray() {
		static::_getData();
		return static::$_requirementsArray;
	}
	
	public static function getTitlesArray() {
		static::_getData();
		return static::$_titlesArray();
	}
	
	public function getEditForm() {
		$className = static::$_editForm;
		return new $className(array('metaKey' => $this));
	}
} // END class EpicDb_Mongo_MetaKeys