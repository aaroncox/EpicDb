<?php
/**
 * R2Db_Form_Message
 *
 * undocumented class
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Form_Post extends EpicDb_Form
{
	protected $_post = null;
	protected $_revision = false;

	protected $_sourceLabel = "Post Message";
	protected $_editSourceLabel = "Edit Post";

	// These need to move into the config, just trying to get the protection online.
	private $_publickey = "6Lf4j8USAAAAAKDmcKGNlZSGLxWa-lm1hL3II3pu";
	private $_privatekey = "6Lf4j8USAAAAAPi5c4-afd6QOhwuOdr0Yh0MDRb5";

	/**
	 * getPost - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function getPost()
	{
		if (!$this->_post instanceOf EpicDb_Mongo_Post) throw new Exception("Invalid Post");
		return $this->_post;
	}

	/**
	 * setPost($post) - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function setPost($post)
	{
		$this->_post = $post;
		return $this;
	}

	public function setRev($rev) {
		$this->_revision = $rev;
		return $this;
	}

	/**
	 * Returns the logged in user's profile
	 *
	 * @return EpicDb_Mongo_Profile
	 * @author Corey Frang
	 **/
	public function getAuthorProfile()
	{
		return EpicDb_Auth::getInstance()->getUserProfile();
	}

	/**
	 * Checks if the document is new
	 *
	 * @return boolean
	 * @author Corey Frang
	 **/
	public function isNewPost()
	{
		$post = $this->getPost();
		return $this->_post->isNewDocument();
	}

	public function getInitialData()
	{
		$post = $this->getPost();
		return ($this->_revision === false) ? $post : $post->revisions[ $this->_revision ];
	}

	public function getDefaultValues()
	{
		$values = array();
		$data = $this->getInitialData();

		$values['source'] = $data->source ?: $data->body;
		$values['tags'] = $data->tags->getTags('tag');

		if ($this->_revision !== false) $values['reason'] = "Rollback to Revision #".($this->_revision+1);

		return $values;
	}

    public function __construct($options = null)
	{
		parent::__construct( $options );
		// postinit - post decorators
		$this->setDefaults( $this->getDefaultValues() );
	}

	/**
	 * init - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function init()
	{
		parent::init();
		$post = $this->getPost();
		$profile = $this->getAuthorProfile();

		$this->addElement("markdown", "source", array(
				'order' => 100,
				'required' => true,
				'class' => 'markDownEditor',
				'label' => $this->_sourceLabel,
				'description' => '',
				'cols' => 90,
				'rows' => 15,
			));
		$this->addElement("tags", "tags", array(
			'order' => 150,
			'required' => true,
			'label' => 'Tags',
			'description' => 'Tagging questions helps categorize them, making it easier to find questions based on specific topics. To use the tagging engine, simply start typing what you are looking for in the search box, and click on the tag that matches the topics your question is related to.',
		));

		if ($post->isNewDocument()) {
			if ($profile) {
				// grant the posting user permissions to this post.
				$post->grant($profile->user);
				// tag the author
				$post->tags->tag($profile, 'author');
			}
		} else {
			// Add a reason for your edit
			$this->addElement("text", "reason", array(
				'order' => 1000,
				'required' => false,
				'placeholder' => 'Reason for Edit',
				'label' => 'Reason for Edit',

			));
			// Change the label to edit post
			$this->source->setLabel($this->_editSourceLabel);
		}

		if(!$profile) {
			$recaptcha = new Zend_Service_ReCaptcha($this->_publickey, $this->_privatekey);
			$captcha = new Zend_Form_Element_Captcha('challenge', array(
				'order' => 2000,
				'label' => 'Prove your a human',
				'captcha' => 'ReCaptcha',
				'captchaOptions' => array(
					'captcha' => 'ReCaptcha',
					'service' => $recaptcha
				)
			));
			$this->addElement($captcha);
		}

		$this->setButtons(array("save" => "Post"));

	}
	
	public function save() {
		$me = $this->getAuthorProfile();
		$post = $this->getPost();

		if($this->source) {
			$post->source = $this->source->getValue();
			$post->body = $this->source->getRenderedValue();
		}

		$filter = new EpicDb_Filter_TagJSON();

		if ($this->tags) {
			$post->tags->setTags($this->tags->getTags(),'tag');
		}

		if($this->requestType) {
			$post->_requestType = $this->requestType->getValue();
		}
		return $post->save();
	}
	public function process($data) {
		$post = $this->getPost();
		if($this->isValid($data)) {
			if(!MW_Auth::getInstance()->getUser()) {
				$recaptcha = new Zend_Service_ReCaptcha($this->_publickey, $this->_privatekey);
				$result = $recaptcha->verify($this->_getParam('recaptcha_challenge_field'),
									$this->_getParam('recaptcha_response_field'));
				if (!$result->isValid()) {
					return false;
				}
			}

			if($post->isNewDocument()) {
				$post->_created = time();
			} else {
				$post->bump($me);
				EpicDb_Mongo_Revision::makeEditFor($post, $this->reason->getValue());
			}

			$this->save();
			return true;
		}
		return false;
	}
	public function render() {
		$this->removeDecorator('FloatClear');
		return parent::render();
	}
} // END class R2Db_Form_Message
