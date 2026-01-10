<?php

namespace KRemoveItem;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\tile\Sign;
use pocketmine\tile\Tile;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\block\BlockBreakEvent;


class Main extends PluginBase implements Listener{

public $dj=array();

public function onEnable(){

	 $this->getLogger()->info("§a|KRemoveItem Loading… by Knight|");
	 	$this->getServer()->getPluginManager()->registerEvents($this,$this);
	 	@mkdir($this->getDataFolder(),0777,true);
	 	@mkdir($this->getDataFolder()."/Sign/",0777,true);
  $this->conf=new Config($this->getDataFolder()."Config.yml",Config::YAML,array(
  "木牌第一行"=>"§6[§a点击丢弃物品§6]",
  "木牌第二行"=>false,
  "木牌第三行"=>false,
  "木牌第四行"=>false
  ));
}

public function onPlayerInteract(PlayerInteractEvent $event){

$player = $event->getPlayer();

$block = $event->getBlock();

$item = $event->getItem();

$level = $block->getLevel()->getFolderName();

$tile = $player->getLevel()->getTile($block);

$x = $block->getX();

$y = $block->getY();

$z = $block->getZ();

$k = new Config($this->getDataFolder()."/Sign/".$level.".yml",Config::YAML,array());

if($tile instanceof Sign){

$sign = $tile->getText();

if($block->getID() == 63 || $block->getID() == 68){

if($sign[0] == $this->conf->get("木牌第一行") && $k->exists($x."-".$y."-".$z)){

if($player->getGamemode() == 0){

if(!isset($this->dj[$player->getName()])){

if($item->getID() != 0){

$this->dj[$player->getName()] = 1;

$player->sendMessage("§a[KRemoveItem]§6请再点击一次木牌丢弃手中的物品");

}else{

$player->sendMessage("§a[KRemoveItem]§c请确认你手中持有物品!");

}

}else{

$player->getInventory()->removeItem(new Item($item->getID(),$item->getDamage(),$item->getCount()));

unset($this->dj[$player->getName()]);

$player->sendMessage("§a[KRemoveItem]§a你已将手中物品丢弃!");

}
}else{

$player->sendMessage("§a[KRemoveItem]§c只有生存模式可以丢弃!");

}

}
}
}
}

public function onSignChange(SignChangeEvent $event){

$player = $event->getPlayer();

$block = $event->getBlock();

$level = $block->getLevel()->getFolderName();

$x = $block->getX();

$y = $block->getY();

$z = $block->getZ();

$k = new Config($this->getDataFolder()."/Sign/".$level.".yml",Config::YAML,array());

if($event->getLine(0) == "kitem" && !$player->isOp()){

$event->setCancelled(true);

$player->sendMessage("§a[KRemoveItem]§c你没有权限创建该木牌");

}

if($event->getLine(0) == "kitem" && $player->isOp()){

$k->set($x."-".$y."-".$z);

$k->save(true);

$event->setLine(0,$this->conf->get("木牌第一行"));

if($this->conf->get("木牌第二行") != false){

$event->setLine(1,$this->conf->get("木牌第二行"));

if($this->conf->get("木牌第三行") != false){

$event->setLine(2,$this->conf->get("木牌第三行"));

if($this->conf->get("木牌第四行") != false){

$event->setLine(3,$this->conf->get("木牌第四行"));

}

}

}

}

}

public function onBlockBreak(BlockBreakEvent $event){

$player = $event->getPlayer();

$block = $event->getBlock();

$level = $block->getLevel()->getFolderName();

$tile = $player->getLevel()->getTile($block);

$x = $block->getX();

$y = $block->getY();

$z = $block->getZ();

$k = new Config($this->getDataFolder()."/Sign/".$level.".yml",Config::YAML,array());

if($tile instanceof Sign){

if($player->isOp() && $block->getID() == 63 || $player->isOp() && $block->getID() == 68){

if($k->exists($x."-".$y."-".$z)){

$k->remove($x."-".$y."-".$z);

$k->save(true);

}

}

}

}

}
