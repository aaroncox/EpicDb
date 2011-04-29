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
		try {
			$reader = new Zend_Feed_Reader();
			$client = new Zend_Http_Client($profile->feed, $config);
			$reader->setHttpClient($client);
			$feed = $reader->import($profile->feed);						
		} catch (Exception $e) {
			// var_dump($e);
		  if ($throwErrors) throw $e;
			// NYI - Should probably throw an error here and let the users know their URL sucks.
			return null;
		}
		if($profile->_deleted) return;
		foreach($feed as $idx => $entry) {
			$article = EpicDb_Mongo_Post_Article_RSS::retrieveArticle($profile, $entry);
			$purifier = new MW_Filter_HtmlPurifier(array(array("HTML.Nofollow", 1)));

			$article->title = $entry->getTitle();
			$article->body = $purifier->filter($entry->getDescription());
			// If the body is empty, fuck it, skip it.
			if(trim($article->body) == "") continue;
			// same with the title
			if(trim($article->title) == "") continue;
			// var_dump($entry->getImage()); exit;
			try {
				$article->_modified = strtotime((string)$entry->getDateModified());			
				$article->_created = strtotime((string)$entry->getDateCreated());
				// var_dump($article->_created); exit;
				if($article->_created == false) {
					$article->_created = $article->_modified;
				}
				$article->link = $entry->getPermaLink();
				$article->save();
				// exit;
			} catch(Exception $exception) {
				echo "\n\rUnable to crawl!";
				var_dump($entry);
			}
		}
		$profile->crawledFeed = time();
		$profile->save();
		return true;
	}
} // END class EpicDb_Crawler