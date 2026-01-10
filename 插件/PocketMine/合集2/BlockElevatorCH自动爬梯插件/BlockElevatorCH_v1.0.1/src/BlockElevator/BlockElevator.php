<?php

namespace BlockElevator;

use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\scheduler\CallbackTask;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\Server;

class BlockElevator extends PluginBase implements Listener{
 public $players = [];
 public $cli = [];
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
}      	
/* public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
        switch($cmd->getName()){
			case "be":
			if($sender instanceof Player){
			if(in_array($sender->getName(),$this->cli)){
				$sender->sendMessage("[Elevator] Turn off the elevator");
				$founded = array_search($sender->getName(), $this->cli);
		if ($founded !== false) {
			array_splice($this->cli, $founded, 1);   //移除此键值的数据
		}
		unset($founded);
			}else{
			$this->cli[] = $sender->getName();
			$sender->sendMessage("[Elevator] Turn on the elevator");}
		}else{$sender->sendMessage("Please run this command in game");}
		return true;
	}
}*/
       public function onJoin(PlayerJoinEvent $event){
		   $event->getPlayer()->sendMessage("[BlockElevator] 手持火把会自动爬楼梯和水流");
	   }
       public function onMove(PlayerMoveEvent $event){
      if($event->getPlayer()->getInventory()->getItemInHand()->getID() == 50){
      $p = $event->getPlayer();
     	$block1 = $p->getLevel()->getBlockIdAt($this->fixPos($p->getX()), ($p->getY() -1), $this->fixPos($p->getZ()));
 	    	$block2 = $p->getLevel()->getBlockIdAt($this->fixPos($p->getX()), ($p->getY()), $this->fixPos($p->getZ()));
		   if($p->isInsideOfWater() or ($block1 == 65 and $block2 == 65)){
			  /* if(in_array($p->getName(),$this->cli)){
			   if(!in_array($p->getName(),$this->players)){*/
			   $v3 = new Vector3($p->motionX,0.3,$p->motionZ);
			   $event->getPlayer()->setMotion($v3);
			  /* $this->players[] = $p->getName();
			     $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this,"removePlayer"],[$p->getName()]),1);
			   }}*/
	   }}unset($block1,$block2,$v3,$p);
	   }
   public function removePlayer($name) {
		$founded = array_search($name, $this->players);
		if ($founded !== false) {
			array_splice($this->players, $founded, 1);   //移除此键值的数据
		}
		unset($founded);
	}
        
    function fixPos($a){
	if($a<0)
	{
		return $a-1;
	}
	return $a;
}
}

