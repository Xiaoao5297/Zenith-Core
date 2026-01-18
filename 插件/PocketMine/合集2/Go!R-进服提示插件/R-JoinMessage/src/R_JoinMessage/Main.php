<?php

namespace R_JoinMessage;

use pocketmine\plugin\Plugin;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class Main extends PluginBase implements Listener
{
//插件运行时执行这个函数
	public function onEnable()
	{
		$this->saveDefaultConfig();
		@mkdir($this->getDataFolder(),0777,true);
		$this->config=new Config($this->getDataFolder()."config.yml",Config::YAML,array("Display-switch(ON/OFF)"=>"ON","JoinMessage"=>"§6欢迎来到服务器！"));
	//以上为创建配置文件
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->getLogger()->info("§6(๑•̀ω•́๑)欢迎使用R-JoinMessage插件，作者:Rayark");
	}
	
//玩家进服时运行这个函数
	public function onJoin(PlayerJoinEvent $event)
	{
	 $s = $this->config->get("Display-switch(ON/OFF)");
	 if($s == "ON")
	 {
	 	$event->setJoinMessage(null);
		 $player = $event->getPlayer();
 		$pn = $player->getName();
 		$c = count($this->getServer()->getOnlinePlayers());
	 	$this->getServer()->broadcastMessage($this->config->get("JoinMessage"));
			$this->getServer()->broadcastMessage("§b当前服务器人数: ".$c);
	 }
		else
		{
			
		}
	}
	
//玩家退服时运行这个函数
public function PlayerQuit(PlayerQuitEvent $event)
	{
	 $s = $this->config->get("Display-switch(ON/OFF)");
	 if($s == "ON")
	 {
	 	$player = $event->getPlayer();
	 	$pn = $player->getName();
	 	$c = count($this->getServer()->getOnlinePlayers());
	 	$this->getServer()->broadcastMessage("§b当前服务器人数: ".$c);
	 }
	 else
	 {
	 
	 }
	}
	
//插件结束时运行这个函数
	public function onDisable()
	{
		$this->getLogger()->info("§6(づ ●─● )づ感谢您的使用，再见");
	}
}