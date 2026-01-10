<?php
/**
 * Created by PhpStorm.
 * User: ASUS-
 * Date: 2017/8/31
 * Time: 13:31
 */

namespace CutIce;

use pocketmine\block\Ice;
use pocketmine\block\PackedIce;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat as C;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener
{
    public function onEnable()
    {
        $this->getLogger()->info("RealLife系列十 刀撸冰块掉落 已加载");
        $this->getLogger()->info("RealLife是Spiderman正在开发的仿生功能插件,坚持做到与人们平时生活所遇到的一致");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    public function OnBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand()->getId();
        $block = $event->getBlock();
        if($block instanceof Ice)
        {
            if($item == "267" || $item == "272" || $item == "283" || $item == "276" || $item == "268" )
            {
                $event->setDrops(array(Item::get(79,0,1)));
                $player->sendTitle("",C::GREEN ."刀撸冰块掉落");
            }
        }
        if($block instanceof PackedIce)
        {
            if($item == "267" || $item == "272" || $item == "283" || $item == "276" || $item == "268" )
            {
                $event->setDrops(array(Item::get(174,0,1)));
                $player->sendTitle("",C::GREEN ."刀撸冰块掉落");
            }
        }

    }

}