<?php
defined('ROOT') OR exit('No pagesFileect script access allowed');

## Traitements à effecturer lors de l'installation du plugin
function pageInstall(){
	$page = new page();
	if(count($page->getItems()) < 1){
		$pageItem = new pageItem();
		$pageItem->setName('Accueil');
		$pageItem->setPosition(1);
		$pageItem->setIsHomepage(1);
		$pageItem->setContent('<p>L\'installation s\'est déroulée avec succès !<br>Rendez-vous sur le site officiel de 99ko pour télécharger des plugins et des thèmes.</p>');
		$pageItem->setIsHidden(0);
		$pageItem->setFile('home.php');
		$page->save($pageItem);
		$page = new page();
		$pageItem = new pageItem();
		$pageItem->setName('Page 2');
		$pageItem->setPosition(2);
		$pageItem->setIsHomepage(0);
		$pageItem->setContent("<p>Iamque lituis cladium concrepantibus internarum non celate ut antea turbidum saeviebat ingenium a veri consideratione detortum et nullo inpositorum vel conpositorum fidem sollemniter inquirente nec discernente a societate noxiorum insontes velut exturbatum e iudiciis fas omne discessit, et causarum legitima silente defensione carnifex rapinarum sequester et obductio capitum et bonorum ubique multatio versabatur per orientales provincias, quas recensere puto nunc oportunum absque Mesopotamia digesta, cum bella Parthica dicerentur, et Aegypto, quam necessario aliud reieci ad tempus.</p><p>Haec igitur lex in amicitia sanciatur, ut neque rogemus res turpes nec faciamus rogati. Turpis enim excusatio est et minime accipienda cum in ceteris peccatis, tum si quis contra rem publicam se amici causa fecisse fateatur. Etenim eo loco, Fanni et Scaevola, locati sumus ut nos longe prospicere oporteat futuros casus rei publicae. Deflexit iam aliquantum de spatio curriculoque consuetudo maiorum.</p>");
		$pageItem->setIsHidden(0);
		$pageItem->setFile('');
		$page->save($pageItem);
		$page = new page();
		$pageItem = new pageItem();
		$pageItem->setName('Texte support');
		$pageItem->setPosition(3);
		$pageItem->setIsHomepage(0);
		$pageItem->setContent('<p style="padding:15px;background:#f7f7f7;">Site officiel : <a href="http://99ko.org">http://99ko.org</a><br>
Forum : <a href="http://99ko.org/support/">http://99ko.org/support/</a><br>
Page Facebook : <a href="https://www.facebook.com/99kocms">https://www.facebook.com/99kocms</a></p>');
		$pageItem->setIsHidden(1);
		$pageItem->setFile('');
		$page->save($pageItem);
		$page = new page();
		$pageItem = new pageItem();
		$pageItem->setName('99ko');
		$pageItem->setPosition(4);
		$pageItem->setIsHomepage(0);
		$pageItem->setContent('');
		$pageItem->setIsHidden(0);
		$pageItem->setFile('');
		$pageItem->setTarget('http://99ko.org');
		$pageItem->setTargetAttr('_blank');
		$page->save($pageItem);
	}
}

## Hook (notifications admin)
function pageAdminNotifications(){
	$page = new page();
	$core = core::getInstance();
	if(!$page->createHomepage()){
		show::msg($core->lang("No homepage defined"), "error");
	}
}

## Hook (ajout des items navigation)
function pageStartCreatePlugin(){
	page::addToNavigation();
}

## Hook (header thème)
function pageEndFrontHead(){
	global $runPlugin;
	if($runPlugin->getName() == 'page'){
		global $pageItem;
		if($pageItem->getNoIndex()){
			$data = '<meta name="robots" content="noindex"><meta name="googlebot" content="noindex">';
			echo $data;
		}
	}
}

## Classe page manager
class page{
	private $items;
	private $pagesFile;
	
	public function __construct(){
		$this->pagesFile = DATA_PLUGIN.'page/pages.json';
		$this->items = $this->loadPages();
	}
	
	public static function addToNavigation(){
		$page = new page();
		$pluginsManager = pluginsManager::getInstance();
		// Création d'items de navigation absents (plugins)
		foreach($pluginsManager->getPlugins() as $k=>$plugin) if($plugin->getConfigVal('activate') && $plugin->getPublicFile() && $plugin->getName() != 'page'){
			$find = false;
			foreach($page->getItems() as $k2=>$pageItem){
				if($pageItem->getTarget() == $plugin->getName()) $find = true;
			}
			if(!$find){
				$pageItem = new pageItem();
				$pageItem->setName($plugin->getInfoVal('name'));
				$pageItem->setPosition($page->makePosition());
				$pageItem->setIsHomepage(0);
				$pageItem->setContent('');
				$pageItem->setIsHidden(0);
				$pageItem->setFile('');
				$pageItem->setTarget($plugin->getName());
				$pageItem->setNoIndex(0);
				$page->save($pageItem);
			}
		}
		// génération de la navigation
		foreach($page->getItems() as $k=>$pageItem) if(!$pageItem->getIsHidden()){
			$core = core::getInstance();
			$pluginsManager->getPlugin('page')->addToNavigation($pageItem->getName(), $page->makeUrl($pageItem), $pageItem->getTargetAttr());
		}
	}
	
	public static function getPageContent($id){
		$page = new page();
		if($temp = $page->create($id)){
			return $temp->getContent();
		}
		else return '';
	}
	
	public function getItems(){
		return $this->items;
	}
	
	public function create($id){
		foreach($this->items as $pageItem){
			if($pageItem->getId() == $id) return $pageItem;
		}
		return false;
	}
	
	public function createHomepage(){
		foreach($this->items as $pageItem){
			if($pageItem->getIshomepage()) return $pageItem;
		}
		return false;
	}
	
	public function save($obj){
		$id = intval($obj->getId());
		if($id < 1) $id = $this->makeId();
		$position = intval($obj->getPosition());
		if($position < 1) $position = $this->makePosition();
		$data = array(
			'id' => $id,
			'name' => $obj->getName(),
			'position' => $position,
			'isHomepage' => $obj->getIsHomepage(),
			'content' => $obj->getContent(),
			'isHidden' => $obj->getIsHidden(),
			'file' => $obj->getFile(),
			'mainTitle' => $obj->getMainTitle(),
			'metaDescriptionTag' => $obj->getMetaDescriptionTag(),
			'metaTitleTag' => $obj->getMetaTitleTag(),
			'targetAttr' => $obj->getTargetAttr(),
			'target' => $obj->getTarget(),
			'noIndex' => $obj->getNoIndex(),
		);
		$update = false;
		foreach($this->items as $k=>$v){
			if($v->getId() == $obj->getId()){
				$this->items[$k] = $obj;
				$update = true;
			}
		}
		if(!$update){
			$this->items[] = $obj;
		}
		if($obj->getIsHomepage() > 0) $this->initIshomepageVal();
		$pages = util::readJsonFile($this->pagesFile, true);
		if($update){
			if(is_array($pages)){
				foreach($pages as $k=>$v){
					if($v['id'] == $obj->getId()){
						$pages[$k] = $data;
						$update = true;
					}
				}
			}
		}
		else{
			$pages[] = $data;
		}
		if(util::writeJsonFile($this->pagesFile, $pages)){
			$this->repairPositions($obj);
			return true;
		}
		return false;
	}
	
	public function del($obj){
		if($obj->getIsHomepage() < 1 && $this->count() > 1){
			foreach($this->items as $k=>$v){
				if($v->getId() == $obj->getId()){
					unset($this->items[$k]);
				}
			}
			$pages = util::readJsonFile($this->pagesFile, true);
			foreach($pages as $k=>$v){
				if($v['id'] == $obj->getId()){
					unset($pages[$k]);
				}
			}
			if(util::writeJsonFile($this->pagesFile, $pages)){
				$this->repairPositions($obj);
				return true;
			}
			return false;
		}
		return false;
	}
	
	public function makePosition(){
		$pos = array(0);
		foreach($this->items as $pageItem){
			$pos[] = $pageItem->getPosition();
		}
		return max($pos)+1;
	}
	
	public function count(){
		return count($this->items);
	}
	
	public function listTemplates(){
		$core = core::getInstance();
		$data = array();
		$items = util::scanDir(THEMES .$core->getConfigVal('theme').'/', array('header.php', 'footer.php', 'style.css', '404.php'));
		foreach($items['file'] as $file){
			if(in_array(util::getFileExtension($file), array('htm', 'html', 'txt', 'php'))) $data[] = $file;
		}
		return $data;
	}
	
	public function makeUrl($obj){
		$core = core::getInstance();
		// => Page
		if($obj->targetIs() == 'page'){
			$temp = ($core->getConfigVal('defaultPlugin') == 'page' && $obj->getIsHomepage()) ? $core->getConfigVal('siteUrl') : $core->getConfigVal('siteUrl').'/'.$core->makeUrl('page', array('name' => $obj->getName(), 'id' => $obj->getId()));
		}
		// => URL
		elseif($obj->targetIs() == 'url'){
			$temp = $obj->getTarget();
		}
		// => Plugin
		else{
			$temp = $core->getConfigVal('siteUrl').'/'.$core->makeUrl($obj->getTarget());
		}
		return $temp;
	}
	
	private function makeId(){
		$ids = array(0);
		foreach($this->items as $pageItem){
			$ids[] = $pageItem->getId();
		}
		return max($ids)+1;
	}

	private function initIshomepageVal(){
		foreach($this->items as $obj){
			$obj->setIsHomepage(0);
			$this->save($obj);
		}
	}
	
	private function repairPositions($currentObj){
		foreach($this->items as $obj) if($obj->getId() != $currentObj->getId()){
			$pos = $obj->getPosition();
			if($pos == $currentObj->getPosition()){
				$obj->setPosition($pos+1);
				$this->save($obj);
			}
		}
	}
	
	private function loadPages(){
		$data = array();
		if(file_exists($this->pagesFile)){
			$items = util::readJsonFile($this->pagesFile);
			$items = util::sort2DimArray($items, 'position', 'num');
			foreach($items as $pageItem){
				$data[] = new pageItem($pageItem);
			}
		}
		return $data;
	}
}

## Classe page item
class pageItem{
	private $id;
	private $name;
	private $position;
	private $isHomepage;
	private $content;
	private $isHidden;
	private $file;
	private $mainTitle;
	private $metaDescriptionTag;
	private $metaTitleTag;
	private $target;
	private $targetAttr;
	private $noIndex;
	
	public function __construct($val = array()){
		if(count($val) > 0){
			$this->id = $val['id'];
			$this->name = $val['name'];
			$this->position = $val['position'];
			$this->isHomepage = $val['isHomepage'];
			$this->content = $val['content'];
			$this->isHidden = $val['isHidden'];
			$this->file = $val['file'];
			$this->mainTitle = $val['mainTitle'];
			$this->metaDescriptionTag = $val['metaDescriptionTag'];
			$this->metaTitleTag = (isset($val['metaTitleTag']) ? $val['metaTitleTag'] : '');
			$this->target = (isset($val['target']) ? $val['target'] : '');
			$this->targetAttr = (isset($val['targetAttr']) ? $val['targetAttr'] : '_self');
			$this->noIndex = (isset($val['noIndex']) ? $val['noIndex'] : 0);
		}
	}

	public function setName($val){
		$val = trim($val);
		$this->name = $val;
	}
	
	public function setPosition($val){
		$this->position = intval($val);
	}
	
	public function setIsHomepage($val){
		$this->isHomepage = trim($val);
	}
	
	public function setContent($val){
		$this->content = trim($val);
	}
	
	public function setIsHidden($val){
		$this->isHidden = intval($val);
	}
	
	public function setFile($val){
		$this->file = trim($val);
	}
	
	public function setMainTitle($val){
		$this->mainTitle = trim($val);
	}
	
	public function setMetaDescriptionTag($val){
		$val = trim($val);
		if(mb_strlen($val) > 150) $val = mb_strcut($val, 0, 150).'...';
		$this->metaDescriptionTag = $val;
	}
	
	public function setMetaTitleTag($val){
		$val = trim($val);
		if(mb_strlen($val) > 50) $val = mb_strcut($val, 0, 50).'...';
		$this->metaTitleTag = $val;
	}
	
	public function setTarget($val){
		$this->target = trim($val);
	}
	
	public function setTargetAttr($val){
		$this->targetAttr = trim($val);
	}
	
	public function setNoIndex($val){
		$this->noIndex = trim($val);
	}

	public function getId(){
		return $this->id;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getPosition(){
		return $this->position;
	}
	
	public function getIsHomepage(){
		return $this->isHomepage;
	}
	
	public function getContent(){
		return $this->content;
	}
	
	public function getIsHidden(){
		return $this->isHidden;
	}
	
	public function getFile(){
		return $this->file;
	}
	
	public function getMainTitle(){
		return $this->mainTitle;
	}
	
	public function getMetaDescriptionTag(){
		return $this->metaDescriptionTag;
	}
	
	public function getMetaTitleTag(){
		return $this->metaTitleTag;
	}
	
	public function getTarget(){
		return $this->target;
	}
	
	public function getTargetAttr(){
		return $this->targetAttr;
	}
	
	public function getNoIndex(){
		return $this->noIndex;
	}
	
	public function targetIs(){
		if($this->getTarget() == '') return 'page';
		elseif(filter_var($this->getTarget(), FILTER_VALIDATE_URL)) return 'url';
		else return 'plugin';
	}
}
?>