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
	
	public function resave($collection) {
		$docs = EpicDb_Mongo::db($collection)->fetchAll(array(), array("id" => 1));
		echo "Resaving ".count($docs)." documents in ".$collection."...\n";
		$i = 0;
		
		$adapter = new Zend_ProgressBar_Adapter_Console();
		$bar = new Zend_ProgressBar($adapter, 0, count($docs));
		foreach($docs as $doc) {
			$i++;
			$bar->update($i, 'Saved '.$doc->_type.'/'.$doc->_id);
			$doc->save();
		}
	}
}