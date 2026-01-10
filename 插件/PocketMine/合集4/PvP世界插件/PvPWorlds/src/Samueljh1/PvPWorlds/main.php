<?php

#################################################
#						#
#		     PvPWorlds		        #
#		    By Samueljh1		#
#						#
#################################################

#Check Out My Youtube: http://youtube.com/samueljh1
#My Google Play Apps! https://play.google.com/store/apps/developer?id=Samueljh1
#My Website! http://samueljh1.net

namespace Samueljh1\PvPWorlds;

use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;

use pocketmine\level;
use pocketmine\level\Position;

use pocketmine\Player;

use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;
use pocketmine\Server;
use pocketmine\block\Block;

class main extends PluginBase implements Listener{



    public function onEnable(){


        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $this->getLogger()->info(TextFormat::GREEN . "PVP世界 开启!");
                
        $this->saveDefaultConfig();
        $this->reloadConfig();
            
        $this->worlds = $this->getConfig()->get("worlds");
        $this->error = $this->getConfig()->get("pvp-error");
        $this->oppvp = $this->getConfig()->get("enable-op");
        
        //$this->getLogger()->info(TextFormat::GREEN . $this->error);
                    
        $this->getLogger()->info(TextFormat::GREEN . "禁止PVP的世界列表 : " . TextFormat::BLUE . implode(", ", $this->worlds));

   }
        
    public function onDisable(){

        $this->getLogger()->info(TextFormat::RED . "PVP世界 关闭 !");

   }
        
    public function onPvP(EntityDamageEvent $eventPvP){
    
        if($eventPvP instanceof EntityDamageByEntityEvent){

            if($eventPvP->getEntity() instanceof Player && $eventPvP->getDamager() instanceof Player){
                
                $map = $eventPvP->getEntity()->getLevel()->getFolderName();
                
                if(in_array($map, $this->worlds)){
                    
                    $player = $eventPvP->getDamager();
                    
                    if($this->error == ""){
                        
                        if($this->oppvp == "true" && !$player->isOP() || $this->oppvp != "true"){
                        
                                $eventPvP->setCancelled();
                                
                        }
                        
                    }
                    
                    else{
                        
                        if($this->oppvp == "true" && !$player->isOP() || $this->oppvp != "true"){
                        
                                $player->sendMessage($this->error);
                                $eventPvP->setCancelled();
                                
                        }
                        
                    }
                                                            
                }
                                
            }
            
        }
 
    }
}

