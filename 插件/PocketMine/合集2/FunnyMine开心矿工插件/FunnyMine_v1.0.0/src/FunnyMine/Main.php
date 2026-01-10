<?php

namespace FunnyMine;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\player\PlayerJoinEvent;
use onebone\economyapi\EconomyAPI;
use pocketmine\event\block\BlockBreakEvent; 
use pocketmine\event\Listener;
use pocketmine\block\Block;

class Main extends PluginBase implements Listener{
	public function onEnable(){
$this->getServer()->getPluginManager()->registerEvents($this, $this);
$this->getLogger()->info("FunnyMine插件已开启！作者古董！");
        }
public function onJoin(PlayerJoinEvent $e){
$e->getPlayer()->sendMessage("§e[FunnyMine]欢迎来到本服务器，挖矿时有几率获得奖励!本插件古董制作!");
}
public function onB(BlockBreakEvent $e){
$p=$e->getPlayer();
$id=$e->getBlock()->getId();
$mt=mt_rand(1,100);
if($e->isCancelled()){
return;
}
if($id == 1){
if($mt == 100){//这个是大奖
$mt2=mt_rand(50,500);
EconomyAPI::getInstance()->addMoney($p,$mt2);
$p->sendMessage("§e[FunnyMine]恭喜你在挖矿时获得了<超级奖励>{$mt2}游戏币!");
}
if($mt == 50){//这个是普通奖
$mt2=mt_rand(10,100);
EconomyAPI::getInstance()->addMoney($p,$mt2);
$p->sendMessage("§b[FunnyMine]恭喜你在挖矿时获得了<普通奖励>{$mt2}游戏币!");
}
}
}}
   
   
   
   
   
   
   
   
   
   
   
   