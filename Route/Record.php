<?php
class EpicDb_Route_Record extends Zend_Controller_Router_Route {

	static public $types = array('tag');

	public static function getInstance(Zend_Config $config)
	{
		$defaults = array(
			'controller' => 'record',
			'action' => 'view',
			'module' => 'default',
			'slug'=>'-',
		);
		$reqs = array(
			'type' => implode('|',static::$types),
			'id' => '\d+|[a-f0-9]{24}',
		);

		$route = $config->route;
		$reqs = ($config->reqs instanceof Zend_Config) ? array_merge($config->reqs->toArray(),$reqs) : $reqs;
		$defs = ($config->defaults instanceof Zend_Config) ? $config->defaults->toArray() + $defaults : $defaults;
		return new static($route, $defs, $reqs);
	}

	public function assemble($data = array(), $reset = false, $encode = false, $partial = false)
	{
		$record = false;
		if(isset($data['record'])) {
			$record = $data['record'];
		} elseif(isset($this->_values['record'])) {
			$record = $this->_values['record'];
		}
		if ($record instanceOf EpicDb_Mongo_Record) {
			$data['type'] = $record->_type;
			$data['id'] = $record->id;
			$slug = new MW_Filter_Slug();
			$data['slug'] = $slug->filter($record->name);
		} else {
			throw new Exception("Expected EpicDb_Mongo_Record, got ".get_class($data['record']));
		}
		if(isset($data['seed'])) {
			$seed = $data['seed'];
			if($seed instanceOf EpicDb_Mongo_Seed) {
				$slug = new MW_Filter_Slug();
				$title = str_replace(array("[[NAME]]", "[[TYPE]]"), array($record->name, $record->_type), strip_tags($seed->title));
				$data['title'] = $slug->filter($title);			
				$data['seed'] = $seed->id;				
			}
		}
		unset($data['record']);
		return parent::assemble($data, $reset, $encode, $partial);
	}

	public function getRecord($params)
	{
		$query = array();
		if(empty($params['id'])) {
			$query['slug'] = $params['slug'];
		} else {
			$query['id'] = (int) $params['id'];
		}
		return EpicDb_Mongo::db($params['type'])->fetchOne($query);
	}

	public function match($path, $partial = false)
	{
		$match = parent::match($path, $partial);
		if ($match) {
			$record = $this->getRecord($match);
			if (!$record) {
				$this->_values = array(); return false;
			}
			$match['record'] = $record;
			$this->_values['record'] = $record;
		}
		return $match;
	}
}