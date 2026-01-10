<?php

namespace LittleBigMC\Leaps;

use pocketmine\math\Vector3;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\item\Item;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\level\sound\AnvilUseSound;
use pocketmine\entity\Entity;
use pocketmine\plugin\PluginBase;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\utils\Random;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\protocol\UseItemPacket;

class Main extends PluginBase implements Listener{
  public function onEnable(){
        $yml = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->yml = $yml->getAll();
    $this->getLogger()->info("§aLeaps by LittleBigMC loaded.");
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
	$this->saveResource("config.yml");
  }
            public function onPacketReceived(DataPacketReceiveEvent $event){
            $pk = $event->getPacket();
            $player = $event->getPlayer();
            if($pk instanceof UseItemPacket and $pk->face === 0xff) {
             $block = $player->getLevel()->getBlock($player->floor()->subtract(0, 1));
            $item = $player->getInventory()->getItemInHand();
            if($player->hasPermission("leapers.use.axe")){
            if( $block->getId() === 0){
if( $this->yml["Choose"] == "Popup"){
$player->sendTIP($this->yml["CoolDownMsg"]);
   }
   elseif($this->yml["Choose"] == "Message"){
   $player->sendMessage($this->yml["CoolDownMsg"]);
}      
      return true;
      }
            if($item->getId() == $this->yml["ID"] ){
      if($player->getDirection() == 0){
        $player->knockBack($player, 0, 1, 0, 1);
      }
      elseif($player->getDirection() == 1){
        $player->knockBack($player, 0, 0, 1, 1);
      }
      elseif($player->getDirection() == 2){
        $player->knockBack($player, 0, -1, 0, 1);
      }
      elseif($player->getDirection() == 3){
        $player->knockBack($player, 0, 0, -1, 1);
        }
      if( $this->yml["Decide"] == "Popup"){
$player->sendTIP($this->yml["Message"]);
   }
   elseif($this->yml["Decide"] == "Message"){
   $player->sendMessage($this->yml["Message"]);
}
}
}
}
}
	public function onPlayerItemHeldEvent(PlayerItemHeldEvent $e){
		$i = $e->getItem();
		$p = $e->getPlayer();
		if($i instanceof Item){
			switch ($i->getId()){
				case $this->getConfig()->get("ID"):
				$p->sendPopup($this->getConfig()->get("ItemName"));
				}
		}
}
public function onDamage(EntityDamageEvent $event){
if($event->getEntity() instanceof Player){
      if($event->getCause() == EntityDamageEvent::CAUSE_FALL){
$event->setCancelled();
}
}
   }
 public function onSneak(PlayerToggleSneakEvent $e){
 $player = $e->getPlayer();
 $level = $player->getLevel();
 $block = $player->getLevel()->getBlock($player->floor()->subtract(0, 1));
 $pos = new Vector3($player->getFloorX(), $player->getFloorY() - 2, $player->getFloorZ());
$pos2 = $level->getBlock($pos);
          if($player->hasPermission("leapers.use.crouch")){
          if($pos2->getId() === 0){
if( $this->yml["Choose"] == "Popup"){
$player->sendTIP($this->yml["CoolDownMsg"]);
   }
   elseif($this->yml["Choose"] == "Message"){
   $player->sendMessage($this->yml["CoolDownMsg"]);
   return true;
}
}
     if($block->getId() === 0){
     if($pos2->getId() !== 0){
     if($player->getDirection() == 0){
        $player->knockBack($player, 0, 1, 0, 1);
      }
      elseif($player->getDirection() == 1){
        $player->knockBack($player, 0, 0, 1, 1);
      }
      elseif($player->getDirection() == 2){
        $player->knockBack($player, 0, -1, 0, 1);
      }
      elseif($player->getDirection() == 3){
        $player->knockBack($player, 0, 0, -1, 1);
      }
      if( $this->yml["Decide"] == "Popup"){
$player->sendTIP($this->yml["Message"]);
   }
   elseif($this->yml["Decide"] == "Message"){
   $player->sendMessage($this->yml["Message"]);
   }
}
}
}
}
}




