<?php

/*
 * This file is a part of WalkingParticles.
 * Copyright (C) 2015  CyberCube-HK
 *
 * WalkingParticles is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * WalkingParticles is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WalkingParticles.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace WalkingParticles\listeners;

use pocketmine\tile\Sign;
use pocketmine\tile\Tile;
use pocketmine\level\sound\BatSound;
use pocketmine\level\sound\ClickSound;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerInteractEvent;

use WalkingParticles\base\BaseListener;
use WalkingParticles\WalkingParticles;

class SignListener extends BaseListener{
  
  public function onBlockBreak(BlockBreakEvent $event){
   if($event->getBlock()->getID() == 323 || $event->getBlock()->getID() == 63 || $event->getBlock()->getID() == 68){
     $sign = $event->getPlayer()->getLevel()->getTile($event->getBlock()); 
     if(!$sign instanceof Sign){
       return;
     }
     $sign = $sign->getText();
     if($sign[0]=='§f[§aWParticles§f]'){ 
       if($event->getPlayer()->hasPermission("walkingparticles.sign.destroy")){
         $event->getPlayer()->sendMessage("§bWalkingParticles §esign has been destroyed!");
         return true;
       }else{
         $event->getPlayer()->sendMessage("§cYou have no permission for this!");
         $event->setCancelled(true);
       }
     }
   }
 }
 
 public function onSignChange(SignChangeEvent $event){
   if($event->getBlock()->getID() == 323 || $event->getBlock()->getID() == 63 || $event->getBlock()->getID() == 68){
     $sign = $event->getPlayer()->getLevel()->getTile($event->getBlock()); 
     if(!$sign instanceof Sign){
       return;
     }
     $sign = $event->getLines();
     if($sign[0] == '[WParticles]'){
       if($event->getPlayer()->hasPermission("walkingparticles.sign.create")){
         if(!empty($sign[1]) && !empty($sign[2])){
           if($sign[1] == 'add' || $sign[1] == 'amplifier' || $sign[1] == 'remove' || $sign[1] == 'display'){
             $event->setLine(0, "§f[§aWParticles§f]");
             $event->setLine(1, "§e".$sign[1]);
             switch($sign[1]):
               case 'amplifier':
                 if(is_numeric($sign[2]) !== true){
                   $event->setLine(0, null);        
                   $event->setLine(1, null);
                   $event->setLine(2, null);
                   $event->setLine(3, null);
                   $event->getPlayer()->sendMessage("§cSign broken, line 3 must be numeric as your creating a sign to change the amplifier!!");
                   return false;
                 }
               break;
               case 'display':
                 if($sign[2] == "line" || $sign[2] == "group"){
                   //correct lol
                 }else{
                   $event->setLine(0, null);        
                   $event->setLine(1, null);
                   $event->setLine(2, null);
                   $event->setLine(3, null);
                   $event->getPlayer()->sendMessage("§cSign broken, line 3 must be line/group as your creating a sign to change the display!!");
                   return false;
                 }
               break;
             endswitch;
             $event->getPlayer()->sendMessage("§bWalkingParticles §asign created!");
             $event->setLine(2, "§d".$sign[2]);
             return true;
           }else{
             $event->setLine(0, null);
             $event->setLine(1, null);
             $event->setLine(2, null);
             $event->setLine(3, null);
             $event->getPlayer()->sendMessage("§cSign broken, line 2 must be §eadd§c, §eremove §cor §eamplifier§c!!");
             return false;
           }
         }else if(!empty($sign[1]) && empty($sign[2])){
           if($sign[1] == 'clear' || $sign[1] == 'get' || $sign[1] == 'list'){
             $event->getPlayer()->sendMessage("§bWalkingParticles §asign created!");
             $event->setLine(0, "§f[§aWParticles§f]");
             $event->setLine(1, "§e".$sign[1]);
             $event->setLine(3, null);
             return true;
           }else{
             $event->setLine(0, null);
             $event->setLine(1, null);
             $event->setLine(2, null);
             $event->setLine(3, null);
             $event->getPlayer()->sendMessage("§cSign broken, please fill in correct information!");
             return false;
           }
         }else{
           $event->setLine(0, null);
           $event->setLine(1, null);
           $event->setLine(2, null);
           $event->getPlayer()->sendMessage("§cSign broken, please fill in correct information!");
           return false;
         }
       }else{
         $event->setLine(0, null);
         $event->setLine(1, null);
         $event->setLine(2, null);
         $event->getPlayer()->sendMessage("§cSign broken, you have no permission for this!");
         return false;
       }
     }
   }
 }
 
 public function onInteract(PlayerInteractEvent $event){
   if($event->getBlock()->getID() == 323 || $event->getBlock()->getID() == 63 || $event->getBlock()->getID() == 68){
     $sign = $event->getPlayer()->getLevel()->getTile($event->getBlock()); 
     if(!$sign instanceof Sign){
       return;
     }
     $sign = $sign->getText();
     if($sign[0]=='§f[§aWParticles§f]'){ 
       if(empty($sign[1]) !== true && empty($sign[2]) !== true){
         if($event->getPlayer()->hasPermission("walkingparticles.sign.toggle")){
           switch(strtolower($sign[1])):
             case "§eadd":
               $particle = substr($sign[2], 3);
               $this->getPlugin()->addPlayerParticle($event->getPlayer(), $particle);
               $event->getPlayer()->sendMessage("§aYou added your §bWalkingParticles§a's ".$particle." particle!");
               $event->getPlayer()->getLevel()->addSound(new BatSound($event->getPlayer()), $this->getPlugin()->getServer()->getOnlinePlayers());
               return true;
             break;
             case "§eremove":
               $particle = substr($sign[2], 3);
               $this->getPlugin()->removePlayerParticle($event->getPlayer(), $particle);
               $event->getPlayer()->sendMessage("§aYou removed your §bWalkingParticles§a's ".$particle." particle!");
               $event->getPlayer()->getLevel()->addSound(new BatSound($event->getPlayer()), $this->getPlugin()->getServer()->getOnlinePlayers());
               return true;
             break;
             case "§eamplifier":
               $amplifier = substr($sign[2], 3);
               $this->getPlugin()->setPlayerAmplifier($event->getPlayer(), $amplifier);
               $event->getPlayer()->sendMessage("§aYou changed your §bWalkingParticles§a's amplifier!");
               $event->getPlayer()->getLevel()->addSound(new BatSound($event->getPlayer()), $this->getPlugin()->getServer()->getOnlinePlayers());
               return true;
             break;
             case "§edisplay":
               $display = substr($sign[2], 3);
               $this->getPlugin()->setPlayerDisplay($event->getPlayer(), $display);
               $event->getPlayer()->sendMessage("§aYou changed the display of your particles!");
               $event->getPlayer()->getLevel()->addSound(new BatSound($event->getPlayer()), $this->getPlugin()->getServer()->getOnlinePlayers());
               return true;
             break;
           endswitch;
         }else{
           $event->getPlayer()->sendMessage("§cYou have no permission for this!");
           return false;
         }
       }else if(!empty($sign[1]) && empty($sign[2])){
         if($event->getPlayer()->hasPermission("walkingparticles.sign.toggle")){
           switch(strtolower($sign[1])):
             case "§eget":
               $event->getPlayer()->sendMessage("§aYour §bWalkingParticles§a: §f".$this->getPlugin()->getAllPlayerParticles($event->getPlayer()));
               $event->getPlayer()->getLevel()->addSound(new ClickSound($event->getPlayer()), $this->getPlugin()->getServer()->getOnlinePlayers());
               return true;
             break;
             case "§eclear":
               $this->getPlugin()->clearPlayerParticle($event->getPlayer());
               $event->getPlayer()->sendMessage("§aYour §bWalkingParticles §ahas been cleared!");
               $event->getPlayer()->getLevel()->addSound(new BatSound($event->getPlayer()), $this->getPlugin()->getServer()->getOnlinePlayers());
               return true;
             break;
             case "§elist":
               $event->getPlayer()->sendMessage("§aList of available particles: §6explode / bubble / splash / water / critical / spell / dripwater / driplava / spore / portal / flame / lava / reddust / heart / ink / snowball / smoke / entityflame");
               $event->getPlayer()->getLevel()->addSound(new ClickSound($event->getPlayer()), $this->getPlugin()->getServer()->getOnlinePlayers());
               return true;
             break;
             endswitch;
         }else{
           $event->getPlayer()->sendMessage("§cYou have no permission for this!");
           return true;
         }
       }else{
         $event->getPlayer()->sendMessage("§cSorry, you're clicking an incorrect §bWalkingParticles §csign!");
         return false;
       }
     }
   }
 }
 
}
?>
