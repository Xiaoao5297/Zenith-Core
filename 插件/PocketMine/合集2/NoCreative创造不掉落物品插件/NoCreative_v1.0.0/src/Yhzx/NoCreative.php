<?php

namespace Yhzx;
 
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\level\Level;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\utils\TextFormat;
class NoCreative extends PluginBase implements Listener{
	
	public $prefix = TextFormat::GRAY."[".TextFormat::GREEN."NoCreative".TextFormat::GRAY."]";
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getLogger()->info(TextFormat::GREEN."====================");
		$this->getServer()->getLogger()->info(TextFormat::GREEN."欢迎使用NoCreative");
		$this->getServer()->getLogger()->info(TextFormat::GREEN."此插件适用卖创造的服");
		$this->getServer()->getLogger()->info(TextFormat::GREEN."使得创造方块不掉落");
		$this->getServer()->getLogger()->info(TextFormat::GREEN."作者：yhzx[xzhy]");
		$this->getServer()->getLogger()->info(TextFormat::GREEN."====================");
		if(!is_dir($this->getDataFolder()))
			mkdir($this->getDataFolder());
		$s = new CreativeBlocks($this->getDataFolder()."Blocks.sqlite3",$this);
		$this->sql = $s;
	}

	public function onBreak(BlockBreakEvent $event){
		$player = $event->getPlayer();
		if(!$event->isCancelled()){
			$block = $event->getBlock();
			$x = $block->getX();
			$y = $block->getY();
			$z = $block->getZ();
			$level = $block->getLevel()->getName();
			if($this->sql->iscreativeblock($x, $y, $z, $level)){
				$this->sql->delblock($x, $y, $z, $level);
				if(!$player->isCreative()){
					$event->setDrops(array());
					$player->sendTip($this->prefix.TextFormat::RED."这是创造方块！");
				}
			}
		}
	}
	
	public function onPlace(BlockPlaceEvent $event){
		$player = $event->getPlayer();
		if(!$event->isCancelled()){
			if($player->isCreative()){
				$block = $event->getBlock();
				$x = $block->getX();
				$y = $block->getY();
				$z = $block->getZ();
				$level = $block->getLevel()->getName();
				$this->sql->add($x, $y, $z, $level);
			}
		}
	}
	
}