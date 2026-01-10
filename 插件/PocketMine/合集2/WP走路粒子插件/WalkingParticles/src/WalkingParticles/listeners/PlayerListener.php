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

use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
use pocketmine\item\Item;

use WalkingParticles\base\BaseListener;
use WalkingParticles\WalkingParticles;

class PlayerListener extends BaseListener{
  
  public function onMove(PlayerMoveEvent $event){
   if($event->getPlayer()->hasPermission("walkingparticles")){
     if($this->getConfig()->get("enable") !== false && $this->getPlugin()->isCleared($event->getPlayer()) !== true){
         $x = $event->getPlayer()->x;
         $y = $event->getPlayer()->y;
         $z = $event->getPlayer()->z;
         $x1 = $x-1;
         $x2 = $x+1;
         $y1 = $y+0.6;
         $y2 = $y+1;
         $y3 = $y+1.4;
         $z1 = $z-1;
         $z2 = $z+1;
         for($i=0;$i < $this->getPlugin()->getPlayerAmplifier($event->getPlayer());$i++){
           $t = $this->getPlugin()->getData()->getAll();
           foreach($t[$event->getPlayer()->getName()]["particle"] as $p){
             if($this->getPlugin()->getPlayerDisplay($event->getPlayer()) == "line"){
               $event->getPlayer()->getLevel()->addParticle($this->getPlugin()->getParticles()->getTheParticle($p, new Vector3($x, $y2, $z)));
             }else{
               $event->getPlayer()->getLevel()->addParticle($this->getPlugin()->getParticles()->getTheParticle($p, new Vector3(mt_rand($x1, $x2), mt_rand(rand($y1, $y), rand($y2, $y3)), mt_rand($z1, $z2))));
             }
           }
         }
      }
   }
 }
 
 public function onJoin(PlayerJoinEvent $event){
   $t = $this->getPlugin()->getData()->getAll();
   if(!isset($t[$event->getPlayer()->getName()])){
     $t[$event->getPlayer()->getName()]["particle"][] = $this->getPlugin()->getConfig()->get("default-particle");
     $t[$event->getPlayer()->getName()]["amplifier"] = $this->getPlugin()->getConfig()->get("default-amplifier");
     $t[$event->getPlayer()->getName()]["display"] = $this->getPlugin()->getConfig()->get("default-display");
     $this->getPlugin()->getData()->setAll($t);
     $this->getPlugin()->getData()->save();
   }
 }
 
 /*public function onLogin(PlayerLoginEvent $event){
   if($this->getConfig()->get("give-menu-onJoin") !== false){
     $this->getPlugin()->giveMenu($event->getPlayer());
     $this->getPlugin()->getLogger()->info("Gave item 347 to ".$event->getPlayer()->getName());
   }
 }*/
 
}
?>
