<?php

namespace Ceku\Epe;
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
		
		$this->getLogger()->info("插件名称:CEpe 作者:Ceku");
		
		
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
	}
	
	public function onDisable()
	{
		
		
	}
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $arg)
	{
		
	}
	
	public function onPlayerInteract(PlayerInteractEvent $event)
	{
		if($event->getBlock()->getY()<0)
		{
			return;
		}
		if($event->getItem()->getId()!=388)
		{
			return;
		}
		$player=$event->getPlayer();
		$block=$event->getBlock();
		$level=$player->getLevel();
		$explosion=new Explosion(new Position($block->getX(),$block->getY(),$block->getZ(),$level),4);
		$explosion->explode();
  $player>sendMessage("§b你使用§a绿宝石§b来进行了爆炸！");
	}
}
