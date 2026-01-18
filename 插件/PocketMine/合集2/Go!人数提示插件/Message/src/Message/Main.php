<?php

namespace Message;

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
		@mkdir($this->getDataFolder(),0777,true);
		$this->config=new Config($this->getDataFolder()."config.yml",Config::YAML,array());
		$this->config->set("Joinmessage","");
		$this->config->set("Quitmessage","");
		$this->config->save();
	//以上为创建配置文件
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->getLogger()->info("欢迎使用本插件，作者:来自极致工作室的:Rayark");
	}
	
//玩家进服时运行这个函数
	public function onJoin(PlayerJoinEvent $event)
	{
		$event->setJoinMessage(null);
		$player = $event->getPlayer();
		$pn = $player->getName();
		$c = count($this->getServer()->getOnlinePlayers());
		$this->getServer()->broadcastMessage($this->config->get("Joinmessage")."§b[系统提示] 当前在线人数: ".$c);
	}
	
//玩家退服时运行这个函数
public function PlayerQuit(PlayerQuitEvent $event)
	{
		$event->setQuitMessage(null);
		$player = $event->getPlayer();
		$pn = $player->getName();
		$c = count($this->getServer()->getOnlinePlayers());
		$this->getServer()->broadcastMessage($this->config->get("Quitmessage")."§b[系统提示] 当前在线人数: ".$c);
	}
	
//插件结束时运行这个函数
	public function onDisable()
	{
		$this->getLogger()->info("感谢你使用本插件!");
	}
}