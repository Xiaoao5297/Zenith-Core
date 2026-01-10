<?php
namespace killsteve;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\level\Level;
use pocketmine\block\Block;
use pocketmine\math\Vector3;

class killsteve extends PluginBase implements Listener{
    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TextFormat::GREEN.'[killsteve]拒绝Steve,欢迎访问作者网站:www.pxloli.com');
    }
    public function onJoin(PlayerJoinEvent $event){
        $player=$event->getPlayer();
        $name=$player->getName();
        $name=$this->filter($name);
        if($name=='steve'){
            $player->kick('检测到你的游戏名称暂未修改,请修改你的游戏名称后再次进入服务器!');
        }
    }
    public function filter($name){
        $name=str_replace(' ', '', $name);
        $name=strtolower($name);
        return $name;
    }
}