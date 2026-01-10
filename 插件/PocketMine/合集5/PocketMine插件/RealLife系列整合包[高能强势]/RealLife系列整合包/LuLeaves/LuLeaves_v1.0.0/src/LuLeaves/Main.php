<?php
/**
 * Created by PhpStorm.
 * User: ASUS-
 * Date: 2017/8/28
 * Time: 10:59
 */

namespace LuLeaves;


use pocketmine\block\Leaves;
use pocketmine\block\Leaves2;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as C;

class Main extends PluginBase implements Listener
{
    public function onEnable()
    {
        $this->getLogger()->info("RealLife系列之七 手撸树叶掉落 已加载");
        $this->getLogger()->info("RealLife是Spiderman正在开发的仿生功能插件,坚持做到与人们平时生活所遇到的一致");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function OnBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand()->getId();
        $block = $event->getBlock();
        if( ($block instanceof Leaves) OR ($block instanceof Leaves2) )
        {
            if($item == "0")
            {
                $blockid = $block->getId();
                $blockide = $block->getDamage();
                $event->setDrops(array(Item::get($blockid,$blockide,1)));
                $player->sendMessage(C::GREEN ."手撸树叶掉落 ");
            }
        }
    }
}