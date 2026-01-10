<?php
namespace ITouchSign;

use pocketmine\plugin\PluginBase;
use pocketmine\block\SignPost;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use ITouchSign\Listener\SignDestroyListener;
use ITouchSign\Listener\SignCreateListener;
use ITouchSign\Listener\PlayerTouchListener;
use ITouchSign\ITouchSignTask;
use pocketmine\level\Position;
use pocketmine\utils\TextFormat;

class ITouchSign extends PluginBase{
 
    public $mapPlayers;
	public $work;
	
    public function onEnable(){
        $path = $this->getDataFolder();
		if(!is_file($path)){
			@mkdir($this->getDataFolder());
			$this->saveResource("IConfig.yml");
			$this->saveResource("Uplog.yml");
            $this->con = new Config($path."IConfig.yml",2);
            $this->dat = new Config($path."IDate.yml",2);
			/*
				x:y:z:level:
					to:world/x:y:z:level
					name: xxx
			*/
		}
		$this->work = $this->dat->getAll();//更新Ticks
        $this->getServer()->getPluginManager()->registerEvents(new PlayerTouchListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new SignCreateListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new SignDestroyListener($this), $this);
		$this->getLogger()->info(TextFormat::GREEN . 'ITouchSign v1.0.0 For API3.0.0+ 已安全启动');
		$this->getLogger()->info(TextFormat::GREEN . 'All by ibook');
		$this->getLogger()->info(TextFormat::GREEN . '欢迎加群为我们提供创意和线索! Q群 546488737 ');
		if($this->con->get('autoUp')==true)
			$this->getServer()->getScheduler()->scheduleRepeatingTask(new ITouchSignTask($this), (20*($this->con->get("upTicks"))));
	}
	
	/*
		x:y:z:level:
			to:world/x:y:z:level
			name: xxx
	*/
	public function initSign(string $to,string $name,string $from){
		$value = array(
			"to"=>$to,
			"name"=>$name
		);
		$this->dat->set($from,$value);
		$this->dat->save();
		//public function loadSign(string $from,$value,$justUp=true)
		$this->saveMapPlayers();
		$this->loadSign($from,$this->dat->get($from),false);
		$this->work = $this->dat->getAll();//更新Ticks缓存
	}
	
	public function removeSign($blockOrVec){
		$string = $this->getIndex($blockOrVec);
		$this->dat->remove($string);
		$this->dat->save();		
		unset($this->work[$string]);//更新Ticks缓存
	}
	
	public function getIndex($block){
		$string = "{$block->getX()}:{$block->getY()}:{$block->getZ()}:{$block->getLevel()->getName()}";
		return $string;
	}
	
	public function divideIndex(string $vec){
		$vec = explode(":",$vec);//xyzl
		return $vec;
	}
	
	public function brFormat(\pocketmine\Player $player){
		$player->sendMessage("§0--------[ §2字牌创建帮助帮助 §0]--------");
		$player->sendMessage("§3第一行: §bITouchSign#世界名称/x:y:z:世界名");
		$player->sendMessage("§3第二行: §b你给该目的地取的名字(任意)");
		$player->sendMessage("§3第三行: §b留空");
		$player->sendMessage("§3第四行: §b留空");
		$player->sendMessage("§b目前支持两种类型一是世界传送,");
		$player->sendMessage("§b传送到世界的出生地,");
		$player->sendMessage("§b二是地标传送传送到某点坐标,");
		$player->sendMessage("§b注意/代表或者不是要填写的字符, 并且设置的世界必须是存在的");
	}
	
	public function saveMapPlayers(){
		$mapPlayers = array();
		/*旧算法
		foreach($this->getServer()->getOnlinePlayers() as $player){
			$map = $player->getLevel()->getName();
			if(!isset($mapPlayers[$map]))
				$mapPlayers[$map]=1;
			else
				$mapPlayers[$map]++;
		}*/
		foreach($this->getServer()->getLevels() as $level){
			$name = $level->getName();
			if(!isset($mapPlayers[$name]))
				$mapPlayers[$name]=count($level->getPlayers());
		}
		return $this->mapPlayers = $mapPlayers;
	}
	
	/*
		§9[ 传送世界牌子 ]/[ 传送地标牌子 ]
		§2传送至: §b$name §2世界/地标
		§2目标世界人数 [§a $map §2] 
		§2上次更新: §b23:25:45
		3 4行更新
	*/
	public function loadSign(string $from,$value,$justUp=true){
		$to = $this->divideIndex($value["to"]);//xyzl
		//$from = $this->getIndex($from);
		$from = $this->divideIndex($from);
		$vector3 = new Vector3($from[0],$from[1],$from[2]);//!!!!!!
		$title = $this->getServer()->getLevelByName($from[3])->getTile($vector3);
		if($title==null){
			$this->removeSign(new Position($from[0],$from[1],$from[2],$this->getServer()->getLevelByName($from[3])));
			$this->getServer()->getLogger()->info("§4> §a检测到木牌由于未知原因强制移除, 已删除木牌记录");
			foreach($this->getServer()->getOnlinePlayers() as $p){
				$p->sendMessage("§4> §a检测到木牌由于未知原因强制移除, 已删除木牌记录");
			}
			return;
		}
//		if($justUp){ changeSign事件是在操作中触发,并非操作结束后 所以此时设置木牌无效会被覆盖故修改方式
		if(false){
			$old_title = $title->getText();
//			$old_title[0] = $old_title[0];
//			$old_title[1] = $old_title[1];
			$to = $this->divideIndex($value["to"]);
			if(isset($to[3]))
				$map = $to[3];
			else
				$map = $value["to"];
			
			$title->setText($old_title[0],$old_title[1],"§2目标世界人数 [§a {$this->mapPlayers[$map]} §2]","§2上次更新: §b".date("H:i:s"));
		}else{
			$to = $this->divideIndex($value["to"]);
			if(isset($to[3])){
				$map = $to[3];
				$tag = "地标";
			}else{
				$map = $value["to"];
				$tag = "世界";
			}
			$title->setText("§9[ 传送{$tag}牌子 ]","§2传送至: §b{$value["name"]} §2$tag",
			"§2目标世界人数 [§a {$this->mapPlayers[$map]} §2]","§2上次更新: §b".date("H:i:s"));
			
		}
	}
}