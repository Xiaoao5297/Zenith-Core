<?php

/*
 * This file is the main class of WalkingParticles.
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

namespace WalkingParticles;

use WalkingParticles\events\PlayerAddWPEvent;
use WalkingParticles\events\PlayerClearWPEvent;
use WalkingParticles\events\PlayerRemoveWPEvent;
use WalkingParticles\listeners\PlayerListener;
use WalkingParticles\listeners\SignListener;
use WalkingParticles\task\ParticleShowTask;
use WalkingParticles\Particles;
use WalkingParticles\commands\WplistCommand;
use WalkingParticles\commands\WpgetCommand;
use WalkingParticles\commands\AdminCommand;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\command\CommandExecutor;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class WalkingParticles extends PluginBase{
  
  const VERSION = "1.0.1";
  
  //Economy plugins
  public $economys = false;

 public function onEnable(){
   /*if($this->pluginLoaded("EconomyAPI") !== false){
     $this->economys = true;
     $this->getLogger()->info("Loaded with EconomyS!!");
   }*/
   $this->getLogger()->info("Loading resources..");
   if(!is_dir($this->getDataFolder())){
     mkdir($this->getDataFolder());
   }
   $this->saveDefaultConfig();
   $this->reloadConfig();
   $this->updateConfig();
   //$this->saveResource("messages.yml");
   $this->data = new Config($this->getDataFolder()."players.yml", Config::YAML, array());
   $this->getLogger()->info("Loading plugin..");
   $this->particles = new Particles($this);  
   $this->getServer()->getScheduler()->scheduleRepeatingTask(new ParticleShowTask($this), 13);  
   $this->getServer()->getPluginManager()->registerEvents(new PlayerListener($this), $this);
   $this->getServer()->getPluginManager()->registerEvents(new SignListener($this), $this);
   $this->getCommand("wplist")->setExecutor(new WplistCommand($this));
   $this->getCommand("wpget")->setExecutor(new WpgetCommand($this));
   $this->getCommand("walkingparticles")->setExecutor(new AdminCommand($this));
   $this->getLogger()->info("§aLoaded Successfully!");
 }
 
 private function pluginLoaded($plugin){
   return $plugin !== null;
 }
 
 private function updateConfig(){
   $this->getLogger()->info("Updating config file..");
   if($this->getConfig()->exists("v") !== true || $this->getConfig()->get("v") !== self::VERSION){
     unlink($this->getDataFolder()."config.yml");
     $this->saveDefaultConfig();
   }
 }
 
 public static function getInstance() {
   return $this;
 }
 
 public function getData(){
   return $this->data;
 }
 
 public function getParticles(){
   return $this->particles;
 }
 
 public function getMessage($config_key){
   $this->msg = new Config($this->getDataFolder()."messages.yml", Config::YAML);
   $t = $this->msg->getAll();
   return $this->msg->exists($config_key) ? $this->msg->get($config_key) : "string_key.".$config_key;
 }
 
 public function addPlayerParticle(Player $player, $particle){
   $this->getServer()->getPluginManager()->callEvent($event = new PlayerAddWPEvent($this, $player, $particle));
		if($event->isCancelled()){
			return false;
		}
   $t = $this->data->getAll();
   $t[$player->getName()]["particle"][] = $particle;
   $this->data->setAll($t);
   $this->data->save();
 }
 
 public function removePlayerParticle(Player $player, $particle){
   $this->getServer()->getPluginManager()->callEvent($event = new PlayerRemoveWPEvent($this, $player, $particle));
   if($event->isCancelled()){
     return false;
   }
   $t = $this->data->getAll();
   $p = array_search($particle, $t[$player->getName()]["particle"]);
   unset($t[$player->getName()]["particle"][$p]);
   $this->data->setAll($t);
   $this->data->save();
 }
 
 public function clearPlayerParticle(Player $player){
   $t = $this->data->getAll();
   $this->getServer()->getPluginManager()->callEvent($event = new PlayerClearWPEvent($this, $player, $t[$player->getName()]["particle"]));
   if($event->isCancelled()){
     return false;
   }
   unset($t[$player->getName()]["particle"]);
   $this->data->setAll($t);
   $this->data->save();
 }
 
 public function getAllPlayerParticles(Player $player){
   $t = $this->data->getAll();
   $particles = $t[$player->getName()]["particle"];
   $p = "";
   foreach($particles as $ps){
     $p .= $ps.", ";
   }
   return substr($p, 0, -2);
 }
 
 public function isCleared(Player $player){
   $t = $this->data->getAll();
   return !isset($t[$player->getName()]["particle"]);
 }
 
 public function setPlayerAmplifier(Player $player, $amplifier){
   $t = $this->data->getAll();
   $t[$player->getName()]["amplifier"] = $amplifier;
   $this->data->setAll($t);
   $this->data->save();
 }
 
 public function getPlayerAmplifier(Player $player){
   $t = $this->data->getAll();
   return $t[$player->getName()]["amplifier"];
 }
 
 public function setPlayerDisplay(Player $player, $display){
   $t = $this->data->getAll();
   $t[$player->getName()]["display"] = $display;
   $this->data->setAll($t);
   $this->data->save();
 }
 
 public function getPlayerDisplay(Player $player){
   $t = $this->data->getAll();
   return $t[$player->getName()]["display"];
 }
 
 //For random_mode soon
 public function changeParticle(Player $player){
   $this->clearPlayerParticle($player);
   $this->addPlayerParticle($player, $this->particles->getRandomParticle());
 }
 
}
?>
