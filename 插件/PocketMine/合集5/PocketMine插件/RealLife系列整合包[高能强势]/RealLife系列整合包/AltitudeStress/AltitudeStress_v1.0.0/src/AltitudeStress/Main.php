<?php
/**
 * Created by PhpStorm.
 * User: ASUS-
 * Date: 2017/8/24
 * Time: 1:37
 */

namespace AltitudeStress;


use pocketmine\entity\Effect;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as C;

class Main extends PluginBase implements Listener
{
    public $high_80,$high_100,$high_120;
    public function onEnable()
    {
        $this->getLogger()->info("RealLife系列之四 高原反应 已加载");
        $this->getLogger()->info("RealLife是Spiderman正在开发的仿生功能插件,坚持做到与人们平时生活所遇到的一致");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        $y = $player->getY();

        $effect9 = Effect::getEffect(Effect::NAUSEA)->setVisible(false)->setAmplifier(0)->setDuration(20*120); //反胃
        $effect18 = Effect::getEffect(Effect::WEAKNESS)->setVisible(true)->setAmplifier(0)->setDuration(20*120);//变弱

        if ($y >= 80 AND $y <= 100)
        {
            $this->high_80++;
            if($this->high_80 == 5)
            {
                $player->addEffect($effect18);
                $player->sendTitle(C::AQUA."高原反应",C::GOLD . "开始使你变得虚弱,再高一点甚至会出现眩晕状况!","2","2","40");
            }
        }
        if ($y >= 100)
        {
            $this->high_100++;
            if($this->high_100 == 5)
            {
                $player->addEffect($effect9);
                $player->sendTitle(C::AQUA."高原反应",C::GOLD . "你已经出现眩晕状况,不能再往上走了!可能直接让你死亡!","2","2","40");
            }
        }
        if ($y >= 120)
        {
            $this->high_120++;
            if($this->high_120 == 5)
            {
                $player->sendTitle(C::AQUA."高原反应",C::GOLD . "高地极度缺氧让你死亡!","2","2","40");
                sleep(2);
                $player->kill();
            }
        }
        if ($y <= 80)
        {
            $this->high_80 = 0;
            $this->high_100 = 0;
            $this->high_120 = 0;
        }




    }
}