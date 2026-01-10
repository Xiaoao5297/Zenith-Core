<?php

namespace LuCactus;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\block\Cactus;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\utils\TextFormat as C;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryHolder;
class Main extends PluginBase implements Listener
{
	public function onEnable()
	{
		$this->getLogger()->info("RealLife系列之一 撸仙人掌得水瓶 已加载");
        $this->getLogger()->info("RealLife是Spiderman正在开发的防生功能插件,坚持做到与人们平时生活所遇到的一致");
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
	}
    public function onBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem()->getID();
        $block = $event->getBlock();
        $blockid = $block->getId();


        if($block instanceof Cactus)
        {
            $num = mt_rand(1,10);
            $event->setDrops(array(Item::get(373,0,$num)));
            $player->sendTitle("",C::GREEN . "§a你在仙人掌里找到了{$num}瓶水","2","2","20");
            unset($num);
        }
    }
}
