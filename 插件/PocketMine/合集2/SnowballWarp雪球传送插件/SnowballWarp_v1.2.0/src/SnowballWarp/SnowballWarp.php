<?php
namespace SnowballWarp;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\entity\Snowball;
use pocketmine\utils\TextFormat as MT;
use pocketmine\Player;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
/*
*v1.2.0 增加彩色显示以及内存回收机制
*v1.0.0 基本传送功能
*/
class SnowballWarp extends PluginBase implements Listener {
	
	public function onEnable(){  
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("§a >雪球瞬移【开启】!");
	}
	public function onSnowballLaunch(ProjectileLaunchEvent $event){  
		$entity = $event->getEntity();
		$shooter = $entity->shootingEntity;
		if($entity instanceof Snowball && $shooter instanceof Player){
		  $shooter->sendMessage("§a雪球已发射，你将瞬移到落地点 ！");
		  }
   }
	public function onSnowballDown(EntityDespawnEvent $event){
		if($event->getType() === 81){	//81=Snowball
			$entity = $event->getEntity();
			$shooter = $entity->shootingEntity;
			$x = $entity->getX();
			$y = $entity->getY();
			$z = $entity->getZ();
			$posTo = new Vector3($x,$y + 1,$z);
			$shooter->teleport($posTo);
			$shooter->sendMessage("§b安全着陆 ! ");
			unset($x,$y,$z,$posTo,$shooter);
		   }
	}
	public function onDisable(){
		$this->getLogger()->info(">雪球瞬移【关闭】");
	}
}

