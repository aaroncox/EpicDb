<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_View_Helper_NetBar extends Zend_View_Helper_Abstract
{
	protected $_imagePath = "http://reloaded.askajedi.com/images/netbar/";
	protected $_scriptPath = "/js/";
	protected $_profile = null;
	public function netBar() {
		$this->_profile = EpicDb_Auth::getInstance()->getUserProfile();
		return clone $this;
	}
	public function userStatus() {		
		if($this->_profile) {
			$levelBar = $this->view->levelBar($this->_profile);
			$content = $this->view->htmlTag("div", array("class" => "inline-flow menu", "id" => "user-menu"), 
				$this->view->htmlTag("div", array("class" => "inline-flow"), $this->view->profileLink($this->_profile))."".
				$this->view->htmlTag("div", array("class" => "inline-flow"), 
					$this->view->htmlTag("img", array("src" => $this->_imagePath."expand.png"))				
				)."".
				$this->view->htmlTag("div", array("class" => "drop-down"), "Some User Stuff?")
			)."";
			$content .= $this->view->htmlTag("div", array("class" => "inline-flow"), 
				$this->view->htmlTag("img", array("src" => $this->_imagePath."divider.png")).""
			);
			$content .= $this->view->htmlTag("div", array("class" => "inline-flow"), "Level");
			$content .= $this->view->htmlTag("div", array("class" => "inline-flow level"), $levelBar->getLevel());
			$content .= $this->view->htmlTag("div", array("class" => "inline-flow"), $levelBar."");
			// $content .= $this->view->htmlTag("div", array("class" => "inline-flow"));
		} else {
			$content = $this->view->htmlTag("p", array(), 
				'<a href="/user/login">Log in</a> or <a href="/user/register">Create an Account</a>'
			);
		}
		return $this->view->htmlTag("div", array("class" => "user-status"), $content);
	}
	public function r2Bar() {
		return $this->view->htmlTag("div", array("class" => "inline-flow menu"), 
			$this->view->htmltag("a", array("href" => "http://r2-db.com"), 
				$this->view->htmlTag("img", array("src" => $this->_imagePath."r2db.png")).""
			)."".
			$this->view->htmltag("div", array("class" => "drop-down"), 
				$this->view->htmlTag("form", array("id" => "netbar-search", "action" => "http://beta.r2-db.com/search"),
					$this->view->htmlTag("input", array("id" => "searchInput", "type" => "text", "name" => "q", "class" => "search-box", 'placeholder' => "Search R2Db", 'autocomplete' => 'off'))."".
					$this->view->htmlTag("iframe", array("id" => "netbar-search-results", 'width' => "198"), " ")
				)."".
				$this->view->htmlTag("div", array("class" => "buttons"),
					$this->view->htmlTag("a", array("href" => "http://r2-db.com/skill-tree/calculators"), 
						$this->view->htmlTag("img", array("src" => $this->_imagePath."r2db/calcs.png")).""
					)."".
					$this->view->htmlTag("img", array("src" => $this->_imagePath."r2db/divider.png"))."".
					$this->view->htmlTag("a", array("href" => "http://r2-db.com/questions"), 
						$this->view->htmlTag("img", array("src" => $this->_imagePath."r2db/questions.png")).""
					).""
				).""
			).""
		)."";
	}
	public function aajbar() {
		return $this->view->htmlTag("div", array("class" => "inline-flow menu"), 
			$this->view->htmltag("a", array("href" => "http://askajedi.com"), 
				$this->view->htmlTag("img", array("src" => $this->_imagePath."aaj.png")).""
			)."".
			$this->view->htmltag("div", array("class" => "drop-down"), 
				$this->view->htmlTag("input", array("type" => "text", "name" => "q", "class" => "search-box"))."".
				$this->view->htmlTag("div", array("class" => "buttons"),
					$this->view->htmlTag("a", array("href" => "http://r2-db.com/skill-tree/calculators"), 
						$this->view->htmlTag("img", array("src" => $this->_imagePath."r2db/calcs.png")).""
					)."".
					$this->view->htmlTag("img", array("src" => $this->_imagePath."r2db/divider.png"))."".
					$this->view->htmlTag("a", array("href" => "http://r2-db.com/questions"), 
						$this->view->htmlTag("img", array("src" => $this->_imagePath."r2db/questions.png")).""
					).""
				).""
			).""
		)."";		
	}
	public function script() {
		return "<script src='".$this->_scriptPath."netbar.js'></script>";
	}
	public function render() {
		return $this->view->htmlTag("div", array("id" => "netbar"), 
			$this->view->htmlTag("div", array("class" => "wrap"), 
				$this->userStatus()."".
				$this->r2Bar()."".
				$content .= $this->view->htmlTag("div", array("class" => "inline-flow"), 
					$this->view->htmlTag("img", array("src" => $this->_imagePath."divider.png")).""
				)."".
				$this->aajBar()
			)."".
			$this->script()
		)."";
	}
	public function __toString() {
		return $this->render();
	}
} // END class 
/*
<div id="netbar">
	<div class="wrap">
		<div class="user-status menu">
			<? if($profile = EpicDb_Auth::getInstance()->getUserProfile()): ?>
				<div class="inline-flow" id="user-menu">
					<div class="inline-flow">
						<?= $this->profileLink($profile) ?>
					</div>
					<div class="inline-flow">
						<img src="/images/netbar/expand.png"/>
					</div>
				</div>
				<div class="inline-flow"><img src="/images/netbar/divider.png"></div>
				<? $levelBar = $this->levelBar($profile) ?>
				<div class="inline-flow">
					Level
				</div>
				<div class="inline-flow level">
					 <?= $levelBar->getLevel() ?>
				</div>
				<div class="inline-flow">
					<?= $levelBar ?>
				</div>
				<div class="drop-down">
					Some User Stuff
				</div>
			<? else: ?>
				<a href="/user/login">Log in</a> or <a href="/user/register">Create an Account</a>
			<? endif; ?>
		</div>
		<div class="inline-flow menu">
			<a href="http://r2-db.com">
				<img src="/images/netbar/r2db.png">
			</a>
			<div class="drop-down">
				<input type="text" name="q" class="search-box"/>
				<div class="buttons">
					<a href="http://r2-db.com/skill-tree/calculator"><img src="/images/netbar/r2db/calcs.png" alt="Skill Tree Calculators on R2-Db.com"></a>
					<img src="/images/netbar/r2db/divider.png">
					<a href="http://r2-db.com/questions"><img src="/images/netbar/r2db/questions.png" alt="SWTOR Questions on R2-Db.com"></a>
				</div>
			</div>
		</div>
		<div class="inline-flow"><img src="/images/netbar/divider.png"></div>
		<div class="inline-flow menu">
			<a href="http://askajedi.com">
				<img src="/images/netbar/aaj.png">
			</a>
			<div class="drop-down">
				<p>Something!</p>
				<div class="buttons">
					<a href="http://r2-db.com/skill-tree/calculator"><img src="/images/netbar/r2db/calcs.png" alt="Skill Tree Calculators on R2-Db.com"></a>
					<img src="/images/netbar/r2db/divider.png">
					<a href="http://r2-db.com/questions"><img src="/images/netbar/r2db/questions.png" alt="SWTOR Questions on R2-Db.com"></a>
				</div>
			</div>
		</div>
		<!-- <div class="inline-flow"><img src="/images/netbar/divider.png"></div>
		<div class="inline-flow">
			<a href="http://forceheal.com">
				<img src="/images/netbar/forceheal.png">
			</a>
		</div> -->
	</div>
</div>
<script src="/js/netbar.js"></script>
*/