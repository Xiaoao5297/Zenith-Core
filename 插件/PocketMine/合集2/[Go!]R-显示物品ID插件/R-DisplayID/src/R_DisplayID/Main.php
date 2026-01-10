<?php

namespace R_DisplayID;

use pocketmine\plugin\Plugin;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerItemHeldEvent; 

class Main extends PluginBase implements Listener
{
	public function onEnable()
	{
	 @mkdir($this->getDataFolder(),0777,\true);
	 $this->config=new Config($this->getDataFolder()."config.yml",Config::YAML,array("Display-switch(all/op/off)"=>"all","Font-color"=>"§3","Tilt-switch(Y/N)"=>"Y","Bold-switch(Y/N)"=>"Y"));
	 $this->getServer()->getPluginManager()->registerEvents($this,$this);
	 $this->getLogger()->info("§6(๑•̀ω•́๑)欢迎使用R-DisplayID插件，作者:Rayark");
	}
	public function onItemHeld(PlayerItemHeldEvent $event)
	{
	 $d = $this->config->get("Display-switch(all/op/off)");
	 $f = $this->config->get("Font-color");
	 $t = $this->config->get("Tilt-switch(Y/N)");
	 $b = $this->config->get("Bold-switch(Y/N)");
	 $id = $event->getItem()->getID();
	 $da = $event->getItem()->getDamage();
	 $a = $id.":".$da;
	 $pl = $event->getPlayer();
	 if($d = "all")
	 {
	  if($t = "Y" and $b = "Y")
	  {
	   $event->getPlayer()->sendPopup($f."§l§oID: ".$a);
	  }
	  else if($t = "Y" and $b = "N")
	  {
	   $event->getPlayer()->sendPopup($f."§oID: ".$a);
	  }
	  else if($t = "N" and $b = "Y")
	  {
	   $event->getPlayer()->sendPopup($f."§lID: ".$a);
	  }
	  else if($t = "N" and $b = "N")
	  {
	   $event->getPlayer()->sendPopup($f."ID: ".$a);
	  }
	 }
	  else if($d = "op")
	  {
	   if($t = "Y" and $b = "Y")
	   {
	    $event->getPlayer()->sendPopup($f."§l§oID: ".$a);
	   }
	   else if($t = "Y" and $b = "N")
	   {
	    $event->getPlayer()->sendPopup($f."§oID: ".$a);
	   }
	   else if($t = "N" and $b = "Y")
	   {
	    $event->getPlayer()->sendPopup($f."§lID: ".$a);
	   }
	   else if($t = "N" and $b = "N")
	   {
	    $event->getPlayer()->sendPopup($f."ID: ".$a);
	   }
	  }
	  else
	  {
	  
	  }
	 }
 public function onDisable()
 {
  $this->getLogger()->info("§6(づ ●─● )づ感谢您的使用，再见");
 }
}