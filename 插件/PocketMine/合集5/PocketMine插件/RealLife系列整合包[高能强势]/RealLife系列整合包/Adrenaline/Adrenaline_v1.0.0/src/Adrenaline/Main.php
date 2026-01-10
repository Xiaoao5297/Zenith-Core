<?php
/**
 * Created by PhpStorm.
 * User: ASUS-
 * Date: 2017/8/25
 * Time: 14:31
 */

namespace Adrenaline;


use pocketmine\entity\Effect;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as C;

class Main extends PluginBase implements Listener
{
    public function onEnable()
    {
        $this->getLogger()->info("RealLife系列之六 肾上腺素 已加载");
        $this->getLogger()->info("RealLife是Spiderman正在开发的仿生功能插件,坚持做到与人们平时生活所遇到的一致");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    public function onPlayerInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        switch($event->getItem()->getId())
        {
            case 437:
                $player->sendTitle(C::GOLD . "你打了肾上激素",C::GREEN."加强速度,挖掘速度,力量","2","2","20*2");
                $player->addEffect(Effect::getEffect(Effect::SPEED)->setVisible(true)->setAmplifier(3)->setDuration(20*60*2));
                $player->addEffect(Effect::getEffect(Effect::HASTE)->setVisible(true)->setAmplifier(3)->setDuration(20*60*2));
                $player->addEffect(Effect::getEffect(Effect::STRENGTH)->setVisible(true)->setAmplifier(3)->setDuration(20*60*2));
                $player->getInventory()->removeItem(new Item(437, 0, 1));
                break;
        }
    }

}