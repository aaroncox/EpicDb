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
	
	public $sql = null;
	public function torheadAction() {
		$this->sql = new mysqli('linode1', 'torhead', 't0rh34dDat4', 'torhead_temp');
		var_dump(APPLICATION_ENV);
		$this->torheadImportSQL();
		$this->torheadConvertSkills();
		// $this->torheadConvertItems();
	}

	public $itemMap = array(
		'idstring' => 'fqn',
		'name' => 'name',
		'description' => 'descriptionSource',
	);
	public function torheadConvertItems() {
		$attribsMap = array(
			'minlevel' => 'requireLevel',
			'basequality' => 'quality',
			'maxdurability' => 'durability',
		);
		$statData = $this->sql->query("select * from game_stat_data");
		$statRefs = array();
		// Build stats
		while($statRow = $statData->fetch_object()) {
			if(!$statRow->name) {
				continue;
			}
			$name = preg_replace("/[^a-zA-Z]/","",lcfirst($statRow->name));
			$query = array();
			$query['$or'][] = array('name' => $name);
			$query['$or'][] = array('fqn' => $statRow->idstring);
			$metaKey = EpicDb_Mongo::db('metaKeys')->fetchOne($query);
			if(!$metaKey) {
				$metaKey = EpicDb_Mongo::newDoc('metaKeys');
				$metaKey->name = $name;
				$metaKey->fqn = $statRow->idstring;
				$metaKey->recordType = array('item');
				$metaKey->title = $statRow->name;
				$metaKey->save();
				echo "Add meta key for: ".$statRow->idstring."\n\r";
			}
			$statRefs[$statRow->id] = $metaKey->name;
		}
		$items = EpicDb_Mongo::db('item');
		$result = $this->sql->query("select * from game_item");
		$markdown = new EpicDb_Form_Element_Markdown(array("name" => "temp"));
		
		echo "Parsing Torhead Items (".$result->num_rows.") \n\r";
		$i = 0;
		
		$adapter = new Zend_ProgressBar_Adapter_Console();
		$bar = new Zend_ProgressBar($adapter, 0, $result->num_rows);

		if($result){
		    while ($row = $result->fetch_object()){
					// Find R2-Db's Version: 
					$query = array(
						'fqn' => $row->idstring,
					);
					$r2item = $items->fetchOne($query);
					if(!$r2item) {
						$r2item = EpicDb_Mongo::newDoc('item');
					}
					$r2item->torhead->icon = $row->icon;
					$r2item->torhead->id = $row->display_id;
					$r2item->torhead->url = "http://www.torhead.com/item/".$row->display_id;
					// Setup base data from Torhead
					foreach($this->itemMap as $from => $to) {
						$r2item->$to = utf8_encode(utf8_decode($row->$from)); 	
					}
					// Setup Attributes from Torhead
					$stats = $this->sql->query("select * from game_equip_stat where id = ".$row->id);
					while($statrow = $stats->fetch_object()) {
						if(isset($statRefs[$statrow->_key])) {
							$statName = $statRefs[$statrow->_key];
							$r2item->attribs->$statName = $statrow->val;							
						}
					}
					// $row->id
					foreach($attribsMap as $from => $to) {
						$r2item->attribs->$to = $row->$from; 	
					}
					if($row->mindmg && $row->maxdmg) {
						$r2item->attribs->damage->min = $row->mindmg;
						$r2item->attribs->damage->max = $row->maxdmg;
					}
					// Bind Rules Setup
					if($row->bindrule) {
						unset($r2item->attribs->isBindOnEquip);
						unset($r2item->attribs->isBindOnPickup);
						switch($row->bindrule) {
							case "2":
								$r2item->attribs->isBindOnEquip = true;
								break;
							case "3":
								$r2item->attribs->isBindOnPickup = true;
								break;
							default:
							 	echo "Unknown bind type: [".$row->bindrule."] for [".$row->name."]"; exit;
								break;
						}
					}
					// Parse the Source into HTML w/ Markdown
					if($r2item->descriptionSource) {
						$markdown->setValue($r2item->descriptionSource); 
						$r2item->description = $markdown->getRenderedValue();
					}
					$r2item->save();
					// var_dump($row, $r2item->export()); exit;
					// $r2item->newRevision();
					// Update Status Bar
					$i++; $bar->update($i);
		    }
		    // Free result set
		    $result->close();
		    $this->sql->next_result();
		}
	}
	
	public function torheadConvertSkills() {
		$attribsMap = array(
			'ability_passive' => 'isPassive',
			'range' => 'range',
			'cooldown' => 'useCooldown',
		);
		$attribsMapTypes = array(
			'ability_passive' => 'bool',
			'range' => 'range',
			'cooldown' => 'float',
		);
		$skills = EpicDb_Mongo::db('skill');
		$result = $this->sql->query("select * from game_ability");
		$classes = array();
		foreach(EpicDb_Mongo::db('class')->fetchAll() as $class) {
			$classes[$class->name] = $class;
		}
		foreach(EpicDb_Mongo::db('advanced-class')->fetchAll() as $class) {
			$classes[$class->name] = $class;
		}

		echo "Parsing Torhead Skills (".$result->num_rows.") \n\r";
		$i = 0;
		
		$markdown = new EpicDb_Form_Element_Markdown(array("name" => "temp"));
		
		$adapter = new Zend_ProgressBar_Adapter_Console();
		$bar = new Zend_ProgressBar($adapter, 0, $result->num_rows);

		if($result){
		    while ($row = $result->fetch_object()){
					$infos = $this->sql->query("
						select class.tabname from game_ability_info as info 
						left join game_class_ability as class
							on info.packageid = class.package
						where info.ability = '".$row->idstring."'
					");
					$requiredClasses = array();
					while ($infos && $info = $infos->fetch_object()) {
						if($info->tabname && isset($classes[$info->tabname])) {
							$requiredClasses[] = $classes[$info->tabname];
						}
					}
					// Find R2-Db's Version: 
					$query = array(
						'fqn' => $row->idstring
					);
					$r2skill = $skills->fetchOne($query);
					echo "Searching for: ".$row->ability_name." \ ".$row->idstring."\n";
					if(!$r2skill) {
						echo "Not found by FQN, checking name/tags...\n";
						if($requiredClasses) {
							$query = array(
								'name' => $row->ability_name,
								// 'fqn' => array('$exists' => false),
							);
							foreach($requiredClasses as $class) {
								$treeQuery = array(
									'class' => $class->createReference()
								);
								foreach(EpicDb_Mongo::db('skill-tree')->fetchAll($treeQuery) as $tree) {
									$query['$or'][]['_tree'] = $tree->createReference();																	
								}
							}
							$r2skill = $skills->fetchOne($query);						
						}
					}
					if(!$r2skill) {
						echo "Not found by name/tags, creating...\n";
						$r2skill = EpicDb_Mongo::newDoc('skill');
					}
					$r2skill->name = $row->ability_name;
					$r2skill->fqn = $row->idstring;
					foreach($attribsMap as $from => $to) {
						if(isset($attribsMapTypes[$from])) {
							switch($attribsMapTypes[$from]) {
								case "bool":
									$r2skill->attribs->$to = (bool) $row->$from; 								
									break;
								case "float":
									$r2skill->attribs->$to = (float) $row->$from; 								
									break;								
								case "int":
									$r2skill->attribs->$to = (int) $row->$from; 								
									break;								
								case "range":
									$range = new EpicDb_Filter_Range();
									$value = $range->filter($row->$from);
									$r2skill->attribs->$to = $value; 								
									break;
								default:
									echo "unknown type: ".$attribsMapTypes[$from];
									break;
							}
						} else {
							$r2skill->attribs->$to = $row->$from; 								
						}
					}
					
					if(!$row->ability_passive) {
						if($row->casttime) {
							$r2skill->attribs->useTime = (float) $row->casttime;							
						} else {
							$r2skill->attribs->useTime = 0;							
						}
					}
					
					// Parse the Source into HTML w/ Markdown
					if($row->ability_description) {
						$markdown->setValue(str_replace(array("\n","\r"), array("\n\n", "\r\r"), $row->ability_description)); 
						$r2skill->description = $markdown->getRenderedValue();
						$r2skill->descriptionSource = $markdown->getValue();
					}
					
					$r2skill->tags->setTags('required-class', $requiredClasses);
					$r2skill->torhead->icon = strtolower($row->ability_icon);
					// var_dump($row->ability_icon);
					$r2skill->torhead->id = $row->display_id;
					$r2skill->torhead->url = "http://www.torhead.com/ability/".$row->display_id;
					// Update Status Bar
					$i++; $bar->update($i);
					$r2skill->save();
					// var_dump($row, $r2skill->export()); exit;
		    }
		    // Free result set
		    $result->close();
		    $this->sql->next_result();
		}
	}
	
	public function torheadImportSQL() {
		$path = "../torhead/";
		echo "Importing SQL Dumps into MySQL...\n\r";
		$files = scandir($path);
		
		foreach($files as $file) {
			$info = pathinfo($path.$file);
			if($info['extension'] != 'sql') {
				continue;
			}
			$query = file_get_contents($path.$file);
			// $result = mysqli_multi_query($sql,$query);
			if ($this->sql->multi_query($query)) {
					echo "Importing '".$file;
			    do {
			        /* store first result set */
			        if ($result = $this->sql->store_result()) {
			            while ($row = $result->fetch_row()) {
										echo ".";
			            }
			            $result->free();
			        }
			        /* print divider */
			        if ($this->sql->more_results()) {
								echo ".";
			        }
			    } while ($this->sql->next_result());
			}
			echo "\n\r";
		}
	}
}