<?php
/**
 * Created by PhpStorm.
 * User: ASUS-
 * Date: 2017/8/24
 * Time: 1:03
 */

namespace BoreWoodFire;


use pocketmine\block\Wood;
use pocketmine\block\Wood2;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as C;

class Main extends PluginBase implements Listener
{
    public function onEnable()
    {
        $this->getLogger()->info("RealLife系列之三 钻木取火 已加载");
        $this->getLogger()->info("RealLife是Spiderman正在开发的仿生功能插件,坚持做到与人们平时生活所遇到的一致");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    public function onPlayerInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $inventory = $player->getInventory();
        switch($event->getItem()->getId())
        {
            case 280:
                if(($event->getBlock() instanceof Wood) OR ($event->getBlock() instanceof Wood2))
                {
                    $num = mt_rand(0,250);
                    if($num <= 30)
                    {
                        $inventory->removeItem(new Item(280, 0, 1));
                        $inventory->addItem(new Item(50,0,1));
                        $player->sendMessage(C::RED. "钻木取火点燃木棒");
                        unset($num);
                    }
                }
                break;
        }
    }

}