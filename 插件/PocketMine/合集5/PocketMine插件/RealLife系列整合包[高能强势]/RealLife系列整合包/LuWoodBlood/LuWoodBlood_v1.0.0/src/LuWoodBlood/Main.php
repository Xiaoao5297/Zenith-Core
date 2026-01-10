<?php
/**
 * Created by PhpStorm.
 * User: ASUS-
 * Date: 2017/8/23
 * Time: 23:50
 */

namespace LuWoodBlood;


use pocketmine\block\Wood;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as C;

class Main extends PluginBase implements Listener
{

    public function onEnable()
    {
        $this->getLogger()->info("RealLife系列之二 手撸木头扣血 已加载");
        $this->getLogger()->info("RealLife是Spiderman正在开发的防生功能插件,坚持做到与人们平时生活所遇到的一致");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function OnBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand()->getId();
        $block = $event->getBlock();
        $health = $player->getHealth();
        if($block instanceof Wood)
        {
            if($item == "0")
            {
                $player->sendTitle("",C::GRAY . "手撸木头,会伤害手哦~","2","2","20");
                $player->setHealth($health-1);
            }
        }
    }

}