<?php
/**
 *  CliController
 *
 * undocumented
 *
 * @author Corey Frang
 */
abstract class EpicDb_Controller_Cli extends Zend_Controller_Action {

	public function preDispatch()
	{
		if (!($this->_response instanceOf Zend_Controller_Response_Cli))
			throw new MW_Controller_404Exception("CLI Access Only");
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

	}

	public function crawlFeedsAction()
	{
		
		$feeders = EpicDb_Mongo::db('profile')->fetchAll(array(
				'_deleted' => array('$exists'=>false),
				'feed' => array('$exists'=>true, '$ne' => ''),
				'$or' => array(
					array("crawledFeed" => array('$exists'=>false)),
					array("crawledFeed" => array('$lt' => time() - 7200))
				)
		), array('crawledFeed' => 1));
		echo "Crawling ".count($feeders)." feeds: \n";
		foreach ($feeders as $profile) {
			echo " Crawling ".$profile->feed." for ".$profile->name." /".$profile->_type."/".$profile->id."\n";
			try {
				EpicDb_Crawler::crawl($profile, true);
			} catch (Exception $e) {
				echo "  Error: Caught ".get_class($e)." ".$e->getMessage()." \n";

			}
		}
	}

	public function resaveEverythingAction() {
		$this->resave('post');
		$this->resave('profile');
		$this->resave('record');
		$this->resave('media');
		$this->resave('wiki');
	}
	
	public function resaveAnswersAction() {
		$this->resave('answer');
	}

	public function resavePostsAction() {
		$this->resave('post');		
	}
	
	public function resaveProfilesAction() {
		$this->resave('profile');
	}
	
	public function resaveRecordsAction() {
		$this->resave('record');
	}
	
	public function resaveUsersAction() {
		$this->resave('user');
	}
	
	public function resaveMediaAction() {
		$this->resave('media');
	}
	
	public function resaveSeedsAction() {
		$docs = EpicDb_Mongo::db('seed')->fetchAll(array(), array("id" => 1));
		echo "Resaving seeds for ".count($docs)." questions ...\n";
		$i = 0;
		
		$adapter = new Zend_ProgressBar_Adapter_Console();
		$bar = new Zend_ProgressBar($adapter, 0, count($docs));
		foreach($docs as $doc) {
			$i++;
			foreach($doc->types as $type) {
				foreach(EpicDb_Mongo::db($type)->fetchAll() as $subject) {
					$name = strip_tags(str_replace("[[NAME]]", $subject->name, $doc->title));
					$tags = array();
					$tags['subject'] = $subject;
					$query = array(
						'record' => $subject->createReference(),
						'seed' => $doc->createReference(),
					); 
					$search = EpicDb_Mongo::db('search')->fetchOne($query);
					if(!$search) {
						$search = EpicDb_Mongo::newDoc('search');
						$search->record = $subject;
						$search->seed = $doc;
					}
					$search->name = $name;
					$search->save();
				}				
			}
			$bar->update($i);
		}
	}
	
	public function resaveWikiAction() {
		$this->resave('wiki');
	}
	
	public function stressTestAction() {
		$start = microtime(true);
		$i = 0;
		
		$users = EpicDb_Mongo::db('user')->fetchAll(array("id" => 2));
		$adapter = new Zend_ProgressBar_Adapter_Console();
		$bar = new Zend_ProgressBar($adapter, 0, count($users));
		foreach($users as $user) {
			$temp = $user->getFollowedPosts()->limit(50);
			foreach($temp as $post) {
				$this->view->postStub($post);
			}
			$i++;
			$bar->update($i);		
		}
		$end = microtime(true);
		$diff = $end - $start;
		var_dump($start, $end, $diff." sec"); 
	}
	
	
	public function resave($collection) {
		$docs = EpicDb_Mongo::db($collection)->fetchAll(array(), array("id" => 1));
		echo "Resaving ".count($docs)." documents in ".$collection."...\n";
		$i = 0;
		
		$adapter = new Zend_ProgressBar_Adapter_Console();
		$bar = new Zend_ProgressBar($adapter, 0, count($docs));
		foreach($docs as $doc) {
			$i++;
			$bar->update($i, 'Saved '.$doc->_type.'/'.$doc->_id);
			try {
				$doc->save();				
			} catch (Exception $e) {
				
			}
		}
	}
}