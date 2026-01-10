<?php

namespace ClearInv;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\inventory\Inventory;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\Listener;

class Main extends PluginBase implements Listener{
	public function onEnable(){
$this->getServer()->getPluginManager()->registerEvents($this, $this);
$this->getLogger()->info("进服清背包ClearInventory插件已开启！作者古董！");
        }
public function onJJ(PlayerJoinEvent $e){
$e->getPlayer()->getInventory()->ClearAll();
}
}
   
   
   
   
   
   
   
   
   
   
   
   