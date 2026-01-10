<?php
/**
 * Created by PhpStorm.
 * User: ASUS-
 * Date: 2017/8/31
 * Time: 13:26
 */

namespace LuVines;


use pocketmine\block\Vine;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as C;

class Main extends PluginBase implements Listener
{
    public function onEnable()
    {
        $this->getLogger()->info("RealLife系列之九 手撸藤蔓掉落 已加载");
        $this->getLogger()->info("RealLife是Spiderman正在开发的仿生功能插件,坚持做到与人们平时生活所遇到的一致");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function OnBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand()->getId();
        $block = $event->getBlock();
        if($block instanceof Vine )
        {
            if($item == "0")
            {
                $event->setDrops(array(Item::get(106,0,1)));
                $player->sendMessage(C::GREEN ."手撸藤蔓掉落 ");
            }
        }
    }

}