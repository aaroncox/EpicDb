<?php
/**
 * EpicDb_Form_Post_Comment
 *
 * undocumented class
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Auth_Form_Register extends MW_Form
{
	/**
	 * init - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function init()
	{
		parent::init();
		$this->addElement("text", "name", array(
				'autocomplete'=>'off',
				'label' => 'Display Name',
				'description' => 'Enter your name as you\'d like it to be displayed',
				'filters'    => array('StringTrim'),
				'required' => true,
			));
		$this->addElement("text", "display_email", array(
				'autocomplete'=>'off',
				'label' => 'Email',
				'description' => '(We will never publish your email address)',
				'required' => true,
				'filters'  => array('StringToLower','StringTrim'),
				'validators' => array(new EpicDb_Auth_Form_Validate_UniqueEmail())
			));
		$this->addElement("text", "username", array(
				'autocomplete'=>'off',
				'label' => 'Username',
				'description' => 'No spaces, only numbers, letters and dashes',
				'required' => true,
				'filters'  => array('StringToLower','StringTrim'),
				'validators' => array(
					new EpicDb_Auth_Form_Validate_Username(),
					new Zend_Validate_Regex(array('pattern' => '/^[a-zA-Z0-9\-]*$/')),
				)
			));
		$this->addElement("password", "password1", array(
				'autocomplete'=>'off',
				'label' => 'Password',
				'required' => true,
				'filters'    => array('StringTrim'),
				'validators' => array(
						'NotEmpty',
						array('StringLength', false, array(6))
				),
			));
		$this->addElement("password", "password2", array(
				'autocomplete'=>'off',
				'label' => 'Password (Again)',
				'required' => true,
				'filters'    => array('StringTrim'),
				'validators' => array(
						'NotEmpty',
						array('StringLength', false, array(6))
				),
			));
		// ReCaptcha Services
		$pubKey = "6Lch0r0SAAAAAI_1-2qj4li0ZdYS8j_Jot9wB8CM";
		$privKey = "6Lch0r0SAAAAABlQvAyRkUp5WlAljT6N6uDpYhzG";
		$recaptcha = new Zend_Service_ReCaptcha($pubKey, $privKey);
		$captcha = new Zend_Form_Element_Captcha('challenge', array(
			'captcha' => 'ReCaptcha',
			'captchaOptions' => array(
				'captcha' => 'ReCaptcha',
				'service' => $recaptcha
			)));
		$this->addElement($captcha);
		$this->setButtons(array("save" => "Create Account"));
	}
	public function process($data) {
		$this->password2->setRequired(true)->setValidators(array(new MW_Auth_IdenticalValidator($data['password1'])));
		if($this->isValid($data)) {
			$user = new MW_Auth_Mongo_User();
			$userAuth = false;
			foreach ($user->auth as $auth)
			{
				if ($auth->email) {
					$auth->email = $this->display_email->getValue();
					if ($this->password1->getValue()) $auth->password = md5($this->password1->getValue());
					$auth->save();
					$userAuth = true;
				}
			}
			if (!$userAuth)
			{
				$auth = $user->auth->new();
				$auth->email = $this->display_email->getValue();
				$auth->password = md5($this->password1->getValue());
				$user->auth->addDocument($auth);
			}
			$profile = EpicDb_Mongo::newDoc('user');
			// $profile->slug = $data['username'];
			$profile->name = $data['name'];
			$profile->display_email = $data['display_email'];
			$profile->username = $data['username'];
			$user->addGroup(MW_Auth_Group_User::getInstance());
			$user->save();
			$profile->user = $user;
			$profile->grant($user);
			$firstFriend = EpicDb_Mongo::db('user')->fetchOne(array("id" => 1));
			if($firstFriend) {
				$profile->follow($firstFriend);
			}
			$profile->save();
			MW_Auth::getInstance()->login($this->display_email->getValue(), $this->password1->getValue());
			return true;
		}
	}
	public function render()
	{
		foreach($this->getElements() as $element) {
			$element->setAttrib('class', 'ui-state-default');
		}
		$this->save->setAttrib('class','login r2-button ui-state-default ui-corner-all');
		$this->getDecorator('HtmlTag')->setOption('class','r2-form transparent-bg rounded')->setOption('id', 'ad-edit');
		return parent::render();
	}
} // END class EpicDb_Form_Post_Comment
