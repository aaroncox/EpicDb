<?php
/**
 * EpicDb_Crawler
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Crawler
{
	/**
	 * Class Instance - Singleton Pattern
	 *
	 * @var self
	 **/
	static protected $_instance = NULL;

	/**
	 * private constructor - singleton pattern
	 *
	 * @return void
	 * @author Corey Frang
	 **/
	private function __construct()
	{
	}

	/**
	 * Returns (or creates) the Instance - Singleton Pattern
	 *
	 * @return self
	 * @author Corey Frang
	 **/
	static public function getInstance()
	{
		if (self::$_instance === NULL) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	protected static $_log = array();
	
	public static function crawl($profile, $throwErrors = true) {
		// echo "Starting on ".$profile->feed;
		$config = array(
     // 'adapter' => 'Zend_Http_Client_Adapter_Proxy',
     // 'timeout' => '15',
     // 'useragent' => '',
     // 'proxy_host' => '192.168.1.7',
     // 'proxy_port' => '8888',
     // 'proxy_user' => '', 
     // 'proxy_pass' => '',
			'encoding'      => 'UTF-8'

		);
		$profile->crawledFeed = time();
		$profile->save();
		try {
			$reader = new Zend_Feed_Reader();
			$client = new Zend_Http_Client($profile->feed, $config);
			$reader->setHttpClient($client);
			$feed = $reader->import($profile->feed);						
		} catch (Exception $e) {
			static::$_log[] = "ERROR: ".$e->getMessage();
			// NYI - Should probably throw an error here and let the users know their URL sucks.
			return null;
		}
		if($profile->_deleted) return;
		foreach($feed as $idx => $entry) {
			$article = EpicDb_Mongo::db('article-rss')->retrieveArticle($profile, $entry);
			$purifier = new MW_Filter_HtmlPurifier(array(array("HTML.Nofollow", 1)));

			if(trim($article->body) == "") {
				static::$_log[] = "Skipped crawling [".$article->title."] because body is empty";
				continue;
			}
			if(trim($article->title) == "") {
				static::$_log[] = "Skipped crawling [".$entry->getPermaLink()."] because title is empty";
				continue;
			}

			$article->title = $entry->getTitle();
			$article->body = $purifier->filter($entry->getContent()?:$entry->getDescription());

			try {
				$article->_modified = strtotime((string)$entry->getDateModified());			
				$article->_created = strtotime((string)$entry->getDateCreated());
				if($article->_created == false) {
					$article->_created = $article->_modified;
				}
				if($article->_created > time()) {
					$article->_created = time();
				}
				$article->link = $entry->getPermaLink();
				$article->save();
				static::$_log[] = "Successfully crawled content @ ".$entry->getPermaLink();
				// exit;
			} catch(Exception $exception) {
				static::$_log[] = "Error crawling content @ ".$entry->getPermaLink();
			}
		}
		$profile->crawledFeed = time();
		$profile->save();
		return true;
	}
	
	public static function getLog() {
		return static::$_log;
	}
} // END class EpicDb_Crawler