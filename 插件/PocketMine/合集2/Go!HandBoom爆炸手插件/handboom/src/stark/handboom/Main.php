<?php

namespace stark\handboom;

use pocketmine\plugin\Plugin;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\level\Position;
use pocketmine\level\Explosion;

class Main extends PluginBase implements Listener
{
	public function onEnable()
	{
		$this->getLogger()->info("欢迎使用爆炸手!");
		
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
	}
	
	public function onDisable()
	{
		
	}
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $arg)
	{
		
		if(strtolower($cmd->getName())=="boom")
		{
			$this->getLogger()->info("爆炸吧骚年");
		}
		return true;
	}
	
	public function onPlayerInteract(PlayerInteractEvent $event)
	{
		if($event->getBlock()->getY()<0)
		{
			return;
		}
		if($event->getItem()->getId()!=0)
		{
			return;
		}
		$player=$event->getPlayer();
		$block=$event->getBlock();
		$level=$player->getLevel();
		$explosion=new Explosion(new Position($block->getX(),$block->getY(),$block->getZ(),$level),4);
		$explosion->explode();
		$player->sendMessage("把服务器炸了吧骚年嘎嘎嘎");
	}
}
