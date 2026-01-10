<?php

namespace NoCheat;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\utils\MainLogger;
use pocketmine\utils\TextFormat;
use pocketmine\level\Level;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\Event;
use pocketmine\event\EventPriority;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerChatEvent;

class Main extends PluginBase implements Listener{
public $players= array();
public $move=array();
public $time=array();
	//public $settime = array();
	public $messaget = array();
	
    public function onEnable(){
        MainLogger::getLogger()->info(TextFormat::LIGHT_PURPLE."NoCheat by PeratX QQ:1215714524 已加载");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
		/*
		@mkdir($this->getDataFolder());
 		$this->cfg=new Config($this->getDataFolder()."config.yml",Config::YAML,array());
		if(!$this->cfg->exists("Message-delay"))
		{
			$this->cfg->set("Message-delay","1.5");
			$this->cfg->save();
		}
		$this->settime=$this->cfg->get("Message-delay");
		*/
    }

    public function onDisable(){
        MainLogger::getLogger()->info(TextFormat::LIGHT_PURPLE."NoCheat 已禁用.");
    }
    public function onMove(PlayerMoveEvent $event){

if($event->getPlayer() instanceof Player and $event->getPlayer()->getGamemode() !== 1){
           $player = $event->getPlayer();
$nx=$player->getX();
$ny=$player->getY();
$nz=$player->getZ();
if(!isset($this->players[$player->getName()]["j"])) {
	$this->players[$player->getName()]["j"]=0;
	$this->players[$player->getName()]["x3"]=$player->getFloorX();
	$this->players[$player->getName()]["y3"]=$player->getFloorY();
	$this->players[$player->getName()]["z3"]=$player->getFloorZ();
	$this->players[$player->getName()]["x"]=$player->getX();
	$this->players[$player->getName()]["y"]=$player->getY();
	$this->players[$player->getName()]["z"]=$player->getZ();
	$this->players[$player->getName()]["te"]=false;
    $this->players[$player->getName()]["w"]=0;
}
	$this->players[$player->getName()]["c"]=false;
if(!isset($this->players[$player->getName()]["t"])) {
$this->players[$player->getName()]["t"]=0;
}
            $block = $event->getPlayer()->getLevel()->getBlock(new Vector3($player->getX(),$player->getY()-1,$player->getZ()));
			$block0 = $event->getPlayer()->getLevel()->getBlock(new Vector3($player->getX(),$player->getY(),$player->getZ()));
			if($player->isOnGround() or !($block->getID() == 0 and !$block->getID() == 10 and !$block->getID() == 11 and !$block->getID() == 8 and !$block->getID() == 9 and !$block->getID() == 182 and !$block->getID() == 126 and !$block->getID() == 44 and $block0->getID() == 0)) { $this->players[$player->getName()]["t"]=0;
			} else ++$this->players[$player->getName()]["t"];
			//$this->getLogger()->warning($player->getY()-$this->players[$player->getName()]["y"]);
           
		if($this->players[$player->getName()]["t"]>30) {
                   $player->kill();
                   $this->getLogger()->warning($player->getName()." 飞太久，被杀了！");
                }
                if ($this->players[$player->getName()]["t"]>=15) {
			$floorv3 = $this->findfloor($player->getLevel(), new Vector3($player->getFloorX(),$player->getFloorY(),$player->getFloorZ()));
						if ($floorv3 === false) {  //脚下是虚空
						}
						else {
         						//$player->setMotion(new Vector3(0,50,0));
								$player->teleport($floorv3);
								$this->players[$player->getName()]["c"]=true;
						        $this->getLogger()->warning($player->getName()." 飞的太高被拉回来了～");
		                        //$this->players[$player->getName()]["t"]=0;
						}
			}
			        if($this->players[$player->getName()]["t"]>6){ 
			        $player->setMotion(new Vector3(0,-1.5,0));
			        						$this->getLogger()->warning($player->getName()." 在飞行～!");
			        }

					$p=$player->getName();
$pcod=new Vector3($player->getX(),0,$player->getZ());
If(isset($this->move[$p]) and isset($this->time[$p])){
if($pcod !== $this->move[$p] and $this->getMillisecond() !== $this->time[$p] and !$this->players[$player->getName()]["te"] and $player->getLevel()->getFolderName()==$this->players[$player->getName()]["l"]){
$speed = $pcod->distance($this->move[$p])/($this->getMillisecond() - $this->time[$p]);
$spy=($player->getY()-$this->players[$player->getName()]["y"])/($this->getMillisecond() - $this->time[$p]);
//$this->getLogger()->warning($spy);
     if(($block->getID()>=8 and $block->getID()<=11) and !($block0->getID()>=8 and $block0->getID()<=11) and ($spy>0) and !$player->isInsideOfSolid()) { 
                   //$player->setMotion(new Vector3(0,-0.01,0));
                   ++$this->players[$player->getName()]["w"];
                } else $this->players[$player->getName()]["w"]=0;
                if($this->players[$player->getName()]["w"]>=5) {
                  $this->getLogger()->warning($player->getName()." 水上漂，被杀了！ ".$this->players[$player->getName()]["w"]);
                  $player->kill();
                  //$player->setPosition(new Vector3($player->getX(),$player->getY()-$this->players[$player->getName()]["w"]*2,$player->getZ()));
                }


//$this->getLogger()->notice($p." ".$speed);
		if($speed>=0.007) {
				$this->players[$player->getName()]["j"]++;
			} else 
			{
				$this->players[$player->getName()]["j"]=0;
			}
if($speed <= 0.004) {
				$this->players[$player->getName()]["x3"]=$player->getFloorX();
				$this->players[$player->getName()]["y3"]=$player->getFloorY();
				$this->players[$player->getName()]["z3"]=$player->getFloorZ();
}
if($speed >= 0.1) {
$event->setCancelled();
$this->getLogger()->warning($player->getName()."瞬移ing ".$speed);
}
 if($this->players[$player->getName()]["j"]>=5){
$player->teleport($this->findfloor($player->getLevel(),new Vector3($this->players[$player->getName()]["x3"],$this->players[$player->getName()]["y3"],$this->players[$player->getName()]["z3"])));
$this->getLogger()->warning($player->getName()."疾跑ing ".$this->players[$player->getName()]["j"]);
}
}
}
$this->move[$p] = $pcod;
$this->time[$p] = $this->getMillisecond();

			        $this->players[$player->getName()]["x"]=$player->getX();
			        $this->players[$player->getName()]["y"]=$player->getY();
			        $this->players[$player->getName()]["z"]=$player->getZ();
           $this->players[$player->getName()]["l"]=$player->getLevel()->getFolderName();
           $this->players[$player->getName()]["te"]=false;
			}
}

	public function onTeleport(EntityTeleportEvent $event) {
		$player = $event->getEntity();
		if($player instanceof Player) {
			//防止传送时被拉回来
			if(isset($this->players[$player->getName()]) && isset($this->players[$player->getName()]["c"])) 
				if(!$this->players[$player->getName()]["c"]) $this->players[$player->getName()]["te"] = true;
		}
	}
    public function getMillisecond() {
		list($s1, $s2) = explode(' ', microtime());		
		return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);	
	}
	public function findfloor($level, $v3) {
		$y = $v3->getY();
		do {
   			$y = $y - 1;
			$v3->y = $y;
			$block = $level->getBlock($v3);
			$id = $block->getID();
			//echo ($id." ");
		}
		while ($id == 0 and $y >= 0);
		
		if ($y < 0) {  //脚下是虚空
			return false;
		}
		else {
			$v3->y = $v3->y + 1;
			return $v3;
		}
	}
	public function onJoin(PlayerJoinEvent $event) {
		$this->players[$event->getPlayer()->getName()]["c"]=false;
	}
	/*
	public function onPlayerChat(PlayerChatEvent $event)
	{
		$name=$event->getPlayer()->getName();
		$nowday=date("d");
		$nowhour=date("H");
		$nowminute=date("i");
		$nowsec=date("s");
        if(isset($this->messaget[$name])){
	       if(!$nowhour=$this->messaget[$name]["hour"] or !$nowminute=$this->messaget[$name]["minute"]){
			   $this->messaget[$name]["minute"] = $nowminute;
			$this->messaget[$name]["day"] = $nowday;
			$this->messaget[$name]["hour"] = $nowhour;
			$this->messaget[$name]["sec"] = $nowsec;
		   }else{
			   if($nowsec < ($this->messaget[$name]["sec"] + $this->settime)){
				   $event->setCancelled(true);
				   $event->getPlayer()->sendMessage("[系统消息]]".TextFormat::RED."检测到疑似刷屏，请过一会发言哦");
			   }else{
				   			$this->messaget[$name]["sec"] = $nowsec;
			   }
		   }
		}else{
			$this->messaget[$name]["minute"] = $nowminute;
			$this->messaget[$name]["day"] = $nowday;
			$this->messaget[$name]["hour"] = $nowhour;
			$this->messaget[$name]["sec"] = $nowsec;
		}
	}
	*/
}
