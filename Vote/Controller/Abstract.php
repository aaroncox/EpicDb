<?php
/**
 * EpicDb_Vote_Controller_Abstract
 *
 * Abstract controller for Voting
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
abstract class EpicDb_Vote_Controller_Abstract extends MW_Controller_Action
{
	public function castAction()
	{
		if (!$this->getRequest()->isPost() || !$this->_request->isXmlHttpRequest()) {
			throw new Exception("Nope.");
		}

		$query = array(
			'id' => (int) $this->getRequest()->getParam('id')
		);
		$post = EpicDb_Mongo::db($this->getRequest()->getParam('type'))->fetchOne($query);

		$value = $this->getRequest()->getParam('vote');
		$vote = EpicDb_Vote::factory($post, $value, $this->_helper->auth->getUserProfile());
		$vote->reason = $this->getRequest()->getParam('reason');
		if ($vote instanceOf EpicDb_Vote_Close) {
			if ( $id = (int)$this->getRequest()->getParam('dupe') ) {
				$vote->setDupeOf( EpicDb_Mongo::db("question")->fetchOne(array("id" => $id )) );
			}
		}

		$result = array(
			"postType" => $post->_type,
			"post" => $post->id,
			"_id" => $post->_id."",
			"ok" => $vote->cast(),
			"error" => $vote->getError(),
			"newScore" => EpicDb_Vote::countVotes($post),
			"yourVote" => $value,
			"hasCast" => $vote->hasCast(),
		);

		$params = $this->getRequest()->getParams();
		if($this->_request->isXmlHttpRequest()) {
			$this->getResponse()->setHeader('Content-type', 'application/json');
			$params = $this->getRequest()->getParams();
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
			if($result) {
				echo Zend_Json::encode($result); exit;
			}
		}
		$this->_redirect($_SERVER['HTTP_REFERER']);
	}
}