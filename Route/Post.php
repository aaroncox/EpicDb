<?php
/**
 *
 *
 * @author Corey Frang
 * @package EpicDb_Route
 * @copyright Copyright (c) 2010 Momentum Workshop, Inc
 */

/**
 *  EpicDb_Route_Post
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Route
 * @copyright Copyright (c) 2010 Momentum Workshop, Inc
 * @version $Id: Post.php 663 2011-03-08 22:41:56Z root $
 */
class EpicDb_Route_Post extends Zend_Controller_Router_Route {
	static public $types = array('m','q','n','s','a','media','image','post','news','response','poll','system','question','article','comment','message','request','answer','article-rss', 'question-comment');
	static public $maps = array(
		'm' => 'message',
		'q' =>'question',
		'n' => 'news',
		's' => 'system',
		'a' => 'answer',
	);
	
	public static function getInstance(Zend_Config $config)
	{
		$defaults = array(
			'controller' => 'post',
			'action' => 'view',
			'module' => 'default',
		);
		$reqs = array(
			'type' => implode('|',self::$types),
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
		$postHash = '';
		if(isset($data['post'])) {
			$post = $data['post'];
		} elseif(isset($this->_values['post'])) {
			$post = $this->_values['post'];
		}
		if ($post instanceOf EpicDb_Mongo_Post) {
			$data['type'] = $post->_type;
			$data['id'] = $post->id;
			unset($data['post']);
		} else {
			throw new Exception("Expected EpicDb_Mongo_Post, got ".get_class($data['post']));
		}
		foreach ($data as $key => $value) {
			if ( isset($this->_defaults[$key]) && $this->_defaults[$key] == $value ) {
				unset($data[$key]);
			}
		}
		$result = parent::assemble($data, $reset, $encode, $partial);
		return $result.$postHash;
	}

	public function getPost($params)
	{
		if(!in_array($params['type'], static::$types)) {
			return null;
		}
		if(isset(static::$maps[$params['type']])) {
			$params['$type'] = static::$maps[$params['type']];
		}
		return EpicDb_Mongo::db($params['type'])->fetchOne(array('id'=>(int)$params['id']));
	}

	public function match($path, $partial = false)
	{
		$match = parent::match($path, $partial);
		// var_dump($match, $path, $partial); exit;
		if ($match) {
			$post = $this->getPost($match);
			if (!$post) {
				$this->_values = array(); return false;
			}
			$match['post'] = $post;
			$this->_values['post'] = $post;
		}
		return $match;
	}
}