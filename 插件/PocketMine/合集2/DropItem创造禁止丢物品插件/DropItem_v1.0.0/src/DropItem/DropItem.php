<?php
namespace DropItem;


use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\utils\TextFormat;

class DropItem extends PluginBase implements Listener{

public function onEnable(){
$this->getLogger()->info(TextFormat::RED."创造禁止丢弃物品插件加载成功!");
$this->getLogger()->info(TextFormat::GREEN."作者:Scc");
$this->getServer()->getPluginManager()->registerEvents ( $this, $this );
}
public function onScc(PlayerDropItemEvent $e){
$p=$e->getPlayer();
if($e->getPlayer()->getGamemode(1)){
$e->setCancelled(true);
$p->sendMessage("§a创造模式禁止丢弃物品");}
   }
}
?>