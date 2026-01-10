<?php

namespace gj\spawn;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\level\Level;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Server;

class main extends PluginBase implements Listener{
	public function onEnable(){
echo "正在加载，作者guojing";
$this->getServer()->getPluginManager()->registerEvents($this, $this);
        }
        public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$user = $player->getName();
		$y = $player->getY();
		$level = $this->getServer()->getDefaultLevel()->getSpawn();
		  if ($y＜=-1){
		  $player->teleport($level);
		  $player->sendMessage("§欢迎来到本服务器！！！");}
		  }
}