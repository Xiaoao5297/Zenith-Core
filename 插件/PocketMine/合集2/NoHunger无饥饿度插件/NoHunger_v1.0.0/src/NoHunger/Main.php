<?php

namespace NoHunger;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\player\PlayerHungerChangeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\Listener;

class Main extends PluginBase implements Listener{
	public function onEnable(){
$this->getServer()->getPluginManager()->registerEvents($this, $this);
$this->getLogger()->info("NoHunger插件已开启！作者古董！");
        }
public function onJoin(PlayerJoinEvent $e){
$e->getPlayer()->sendMessage("§e[NoHunger]本服务器使用了防止饥饿插件! 作者古董.");
}
public function onHunger(PlayerHungerChangeEvent $e){
$e->setCancelled();
}
#         ####      ####     #     ####       我
#                 #      #     #     #     #               滑
#       ####      #     #    #     ####         了
#        #             #     #    #     #     #           个
#       ####     ####    #     ####             稽
#╭(╯ε╰)╮
}
   
   
   
   
   
   
   
   
   
   
   
   