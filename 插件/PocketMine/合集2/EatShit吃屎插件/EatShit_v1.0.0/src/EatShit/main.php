<?php

namespace EatShit;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\entity\Effect;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\event\player\PlayerInteractEvent;

class main extends PluginBase implements Listener{
	public function onEnable(){
   		$this->getServer()->getPluginManager()->registerEvents($this,$this);
}
		
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		switch($command->getName()){
		case"我要吃屎":
      if(!$sender instanceof Player){
		 $sender->sendMessage("控制台我吃你麻痹");
		 }else{
$sender->getInventory()->addItem(new Item(367,0,1));
		$sender->sendMessage("你已经获得一坨新鲜滚烫的屎，趁热吃吧！");		
}}}
public function onTouch(PlayerInteractEvent $event){
   $player = $event->getPlayer();	
if($event->getItem()->getID() == 367){
$player->getInventory()->removeItem(new Item(367,0,1));
$player->addEffect(Effect::getEffect(9)->setDuration(200)->setAmplifier(1)->setVisible(true));
$player->setHealth($player->getMaxHealth());
$player->sendMessage("你居然吃屎！！！啧啧啧");	
}}
	}