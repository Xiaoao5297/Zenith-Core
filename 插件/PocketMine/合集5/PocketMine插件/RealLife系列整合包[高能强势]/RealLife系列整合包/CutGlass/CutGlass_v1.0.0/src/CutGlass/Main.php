<?php
/**
 * Created by PhpStorm.
 * User: ASUS-
 * Date: 2017/8/31
 * Time: 0:34
 */

namespace CutGlass;

use pocketmine\block\Glass;
use pocketmine\block\GlassPane;
use pocketmine\block\StainedGlass;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as C;

class Main extends PluginBase implements Listener
{
    public function onEnable()
    {
        $this->getLogger()->info("RealLife系列之八 刀撸玻璃掉落 已加载");
        $this->getLogger()->info("RealLife是Spiderman正在开发的仿生功能插件,坚持做到与人们平时生活所遇到的一致");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function OnBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand()->getId();
        $block = $event->getBlock();
        if($block instanceof Glass)
        {
            if($item == "267" || $item == "272" || $item == "283" || $item == "276" || $item == "268" || $item == "359" )
            {
                $event->setDrops(array(Item::get(20,0,1)));
                $player->sendTitle("",C::GREEN ."刀撸玻璃掉落");
            }
        }
        if($block instanceof GlassPane)
        {
            if($item == "267" || $item == "272" || $item == "283" || $item == "276" || $item == "268" || $item == "359" )
            {
                $event->setDrops(array(Item::get(102,0,1)));
                $player->sendTitle("",C::GREEN ."刀撸玻璃掉落");
            }
        }
    }

}