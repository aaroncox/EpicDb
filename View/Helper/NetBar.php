<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_View_Helper_NetBar extends Zend_View_Helper_Abstract
{
	protected $_imagePath = "/images/netbar/";
	protected $_scriptPath = "/js/";
	protected $_profile = null;
	public function netBar() {
		$this->_profile = EpicDb_Auth::getInstance()->getUserProfile();
		return clone $this;
	}
	public function recentActivity() {
		return 'stuff';
	}
	public function userStatus() {		
		if($this->_profile) {
			$iconClass = "";
			if($this->_profile->faction) {
				switch($this->_profile->faction->name) {
					case "The Sith Empire":
						$iconClass = " border-faction-sith";
						break;
					case "The Galactic Republic":
						$iconClass = " border-faction-republic";
						break;
					default:
						break;
				}							
			}
			
			$levelBar = $this->view->levelBar($this->_profile);
			$content = $this->view->htmlTag("div", array("class" => "inline-flow menu", "id" => "user-menu"), 
				$this->view->htmlTag("div", array("class" => "inline-flow"), "Welcome back, ".$this->view->htmlTag("strong", array(), $this->view->profileLink($this->_profile)))."".
				$this->view->htmlTag("div", array("class" => "inline-flow"), 
					$this->view->htmlTag("img", array("src" => $this->_imagePath."expand.png"))				
				)."".
				$this->view->htmlTag("div", array("class" => "drop-down wide"), 
					$this->view->htmlTag("div", array("class" => "profile-side inline-flow"), 
						$this->view->htmlTag("div", array("class" => "inline-flow profile-icon rounded".$iconClass), 
							$this->view->htmlTag("img", array("src" => $this->_profile->getIcon()))
						)."".
						$this->view->htmlTag("p", array(), "Level")."".
						$this->view->htmlTag("p", array("class" => "level"), $levelBar->getLevel())
					)."".
					$this->view->htmlTag("div", array("class" => "profile-info inline-flow"), 
						$this->view->htmlTag("p", array("class" => "profile-name"), 
							$this->view->profileLink($this->_profile)
						)."".
						$this->view->htmlTag("p", array("class" => "profile-activity-header"), "Recent Activity")."".
						$this->view->htmlTag("div", array("class" => "profile-activity"), 
							$this->recentActivity()
						)."".
						$this->view->htmlTag("p", array("class" => "profile-activity-footer"), "View Earlier Activity →").""
					)."".
					$this->view->htmlTag("div", array("class" => "buttons"),
						$this->view->htmlTag("a", array("href" => "#"), 
							$this->view->htmlTag("img", array("src" => $this->_imagePath."/profile.png")).""
						)."".
						$this->view->htmlTag("img", array("src" => $this->_imagePath."r2db/divider.png"))."".
						$this->view->htmlTag("a", array("href" => "#"), 
							$this->view->htmlTag("img", array("src" => $this->_imagePath."/achievements.png")).""
						)."".
						$this->view->htmlTag("img", array("src" => $this->_imagePath."r2db/divider.png"))."".
						$this->view->htmlTag("a", array("href" => "#"), 
							$this->view->htmlTag("img", array("src" => $this->_imagePath."/social.png")).""
						)."".
						$this->view->htmlTag("img", array("src" => $this->_imagePath."r2db/divider.png"))."".
						$this->view->htmlTag("a", array("href" => "#"), 
							$this->view->htmlTag("img", array("src" => $this->_imagePath."/inventory.png")).""
						).""
					).""					
				)
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
			$this->view->htmltag("div", array("id" => "netbar-r2-search", "class" => "drop-down"), 
				$this->view->htmlTag("form", array("action" => "http://beta.r2-db.com/search"),
					$this->view->htmlTag("input", array("id" => "searchInput", "type" => "text", "name" => "q", "class" => "search-box", 'placeholder' => "Search R2-Db.com", 'autocomplete' => 'off'))."".
					$this->view->htmlTag("iframe", array("id" => "netbar-search-results", 'width' => "198", "class" => 'netbar-results'), " ")
				)."".
				$this->view->htmlTag("div", array("class" => "buttons"),
					$this->view->htmlTag("a", array("href" => "http://r2-db.com/skill-tree/calculator"), 
						$this->view->htmlTag("img", array("src" => $this->_imagePath."/skill-tree.png")).""
					)."".
					$this->view->htmlTag("img", array("src" => $this->_imagePath."r2db/divider.png"))."".
					$this->view->htmlTag("a", array("href" => "http://r2-db.com/questions"), 
						$this->view->htmlTag("img", array("src" => $this->_imagePath."/questions.png")).""
					).""
				).""
			).""
		)."";
	}
	public function recentAAJNews() {
		$aaj = EpicDb_Mongo::db('website')->fetchOne(array("id" => 3));
		$html = "";
		foreach($aaj->getAuthoredPosts(array(), array("_created" => -1), 5) as $post) {
			$html .= $this->view->htmlTag("li", array("class" => "search-result-row"), 
							 	 $this->view->htmlTag("span", array(), $this->view->postLink($post, array("text" => $this->view->htmlFragment($post->title, 28, " →"))))
							 );
		}
		return $html;
	}
	public function aajbar() {
		return $this->view->htmlTag("div", array("class" => "inline-flow menu"), 
			$this->view->htmltag("a", array("href" => "http://askajedi.com"), 
				$this->view->htmlTag("img", array("src" => $this->_imagePath."aaj.png")).""
			)."".
			$this->view->htmltag("div", array("id" => "netbar-aaj-search", "class" => "drop-down"), 
				$this->view->htmlTag("form", array("action" => "http://reloaded.askajedi.com/search"),
					$this->view->htmlTag("input", array("id" => "searchInput", "type" => "text", "name" => "q", "class" => "search-box", 'placeholder' => "Search AskAJedi.com", 'autocomplete' => 'off'))."".
					$this->view->htmlTag("ul", array("class" => "recently search-results-table"), 
						$this->recentAAJNews()
					)."".
					$this->view->htmlTag("iframe", array("id" => "netbar-search-results", 'width' => "198", "class" => 'netbar-results'), " ")
				)."".
				$this->view->htmlTag("div", array("class" => "buttons"),
					$this->view->htmlTag("a", array("href" => "http://r2-db.com/skill-tree/calculators"), 
						$this->view->htmlTag("img", array("src" => $this->_imagePath."forums.png")).""
					)."".
					$this->view->htmlTag("img", array("src" => $this->_imagePath."r2db/divider.png"))."".
					$this->view->htmlTag("a", array("href" => "http://r2-db.com/questions"), 
						$this->view->htmlTag("img", array("src" => $this->_imagePath."news.png")).""
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
				$this->view->htmlTag("div", array("class" => "inline-flow"), 
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