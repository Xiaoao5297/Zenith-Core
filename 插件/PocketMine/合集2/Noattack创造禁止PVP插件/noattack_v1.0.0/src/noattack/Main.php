<?php
/*
king <定制>
*/
namespace noattack;

use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\Server;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageByEntity;
use pocketmine\event\entity\EntityDamageEvent;
class Main extends PluginBase implements Listener {
public function onEnable() {
	$this->getLogger()->info(TextFormat::GREEN ."创造模式禁止PVP_插件_by:king 载入成功! 本插件由 极致·人生 优化!");
	$this->getServer()->getPluginManager()->registerEvents($this, $this);
}
public function onEntityDamageByEntity(EntityDamageEvent $event){
        $entity = $event->getEntity();
        if($event instanceof EntityDamageByEntityEvent){
            $damager = $event->getDamager();
            if($entity instanceof Player && $damager instanceof Player){
			if($damager->isCreative()==true){
			 $event->setCancelled(true);
             $damager->sendMessage("§c[系统提示] 创造模式禁止PVP,请切换回生存模式进行PVP!");
			}
			}
		}
}
}
?>
