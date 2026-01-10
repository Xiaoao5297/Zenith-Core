<?php

namespace evilpoint;

use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\item\item;
use pocketmine\event\Listener;
use pocketmine\math\vector3;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamagebyentityEvent;
use pocketmine\event\player\playerdeathevent;
use pocketmine\inventory\PlayerInventory;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\PluginTask ;
use pocketmine\plugin\Plugin;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\scheduler\CallbackTask;
class evilpoint extends PluginBase implements Listener{
  public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		}
		 public function playerjoin( PlayerJoinEvent $event){
    $player=$event->getplayer();
    @mkdir($this->getDataFolder() . "");
    $cfg = new Config($this->getDataFolder() . $player->getName() . ".yml", Config::YAML, array("nc"=>"white","ep"=>0)); 
    $nc=$cfg->get("nc");
    switch($nc){
    case "red":
    $player->setnametag("§c".$player->getname());
    break;
    case "purple":
    $player->setnametag("§5大魔王".$player->getname());
    break;
    case "yellow":
    $player->setnametag("§e".$player->getname());
    break;
    case "green":
    $player->setnametag("§a".$player->getname());
    break;
    case "cray":
    $player->setnametag("§9<圣者>".$player->getname());
    break;
    }
    }
    
		
				
				 
    public function tip(PlayerMoveEvent $event){
    $player=$event->getplayer();
				@mkdir($this->getDataFolder() . "");
                $cfg = new Config($this->getDataFolder() . $player->getName() . ".yml", Config::YAML, array()); 
			
				$ep=$cfg->get("ep");
			
				
				
				 $level=$player->getlevel();
				 $ln=$level->getfoldername();
				$player->sendtip("§6罪恶值:".$ep);
				
		
    
    }
   
    
    public function pwaskilled(EntityDamageEvent $event){
    if($event instanceof entitydamagebyentityevent){
    $player=$event->getentity();
    $pn=$player->getname();
    $killer=$event->getdamager();
    $kn=$killer->getname();
    @mkdir($this->getDataFolder());
    $cfg = new Config($this->getDataFolder() . $killer->getName() . ".yml", Config::YAML,array());
  
    $cfg2 = new Config($this->getDataFolder() . $player->getName() . ".yml", Config::YAML,array()); 
    $ep=$cfg->get("ep");
    $ep2=$cfg2->get("ep");
    if($ep<=-50){
    $killer->setnametag("§5"."<圣者>".$kn);
    $cfg->set("nc","cray");
    $cfg->save(true);
    }elseif($ep>-50&&$ep<=-30){
    $killer->setnametag("§a".$kn);
   $cfg->set("nc","green");
    $cfg->save(true);
    }elseif($ep>20&&$ep<=70){
    $killer->setnametag("§e".$kn);
   $cfg->set("nc","yellow");
    $cfg->save(true);
    }elseif($ep>70&&$ep<100){
    $killer->setnametag("§c".$kn);
   $cfg->set("nc","red");
    $cfg->save(true);
    }elseif($ep>=100){
    $killer->setnametag("§5"."<大魔王>".$kn);
    $cfg->set("nc","purple");
    $cfg->save(true);
    }
    if($player->gethealth() - $event->getdamage() <= 0){
    //未完杀人
 
				$nc=$cfg->get("nc");
				$cfg->save(true);
		
				$nc2=$cfg2->get("nc");
		
				switch($nc2){
				case "purple":
				 $cfg->set("ep",$ep-20);
		
				$cfg->save(true);
				break;
				case "red":
				$cfg->set("ep",$ep-10);
			
				$cfg->save(true);
				break;
				case "yellow":
				$cfg->set("ep",$ep-5);
				$cfg->save(true);
				break;
				case "white":
				$cfg->set("ep",$ep+2);
				$cfg->save(true);
				break;
				case "green":
				$cfg->set("ep",$ep+5);
				
			 $cfg->save(true);
				break;
				case "cray":
				$cfg->set("ep",$ep+10);
				
			 $cfg->save(true);
				break;
				}
				 switch($nc){
				case "red":
				$cfg->set("ep",$ep+2);
				
				$cfg->save(true);
				break;
				case "yellow":
				$cfg->set("ep",$ep+2);
				$cfg->save(true);
				break;
				case "white":
				$cfg->set("ep",$ep+2);
				$cfg->save(true);
				break;
				case "green":
				$cfg->set("ep",$ep+2);
			 $cfg->save(true);
				break;
				case "cray":
				$cfg->set("ep",$ep+5);
			 $cfg->save(true);
				break;
				}
				
    
    }
    }

}
}