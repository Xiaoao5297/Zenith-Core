<?php
namespace EnderPerl;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\entity\EntityDespawnEvent;

use pocketmine\entity\Snowball;
use pocketmine\Player;
use pocketmine\level\Position;

class Main extends PluginBase implements Listener{

	/** @var Array */
	private $order;
	
	public function __construct(){
		$this->order = array();
	}
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		
		$this->getLogger()->info("EnderPerl loaded!");
	}
	
	public function onProjectileLaunch(ProjectileLaunchEvent $event){
		$entity = $event->getEntity();
		if($entity instanceof Snowball){
			$shooter = $entity->shootingEntity;
			if($shooter instanceof Player){
				$id = $shooter->getId();
//				$this->getLogger()->info($id);
				if( array_key_exists($id,$this->order) ){$this->order[$id]++;}
					else{$this->order += array($id => 1);}
			}
		}
	}
	
	public function onPlayerDeath(PlayerDeathEvent $event){
		$entity = $event->getEntity();
		if($entity instanceof Player){
			$id = $entity->getId();
//			$this->getLogger()->info($entity->getName()."is dead");
			if(array_key_exists($id,$this->order)){$this->order[$id]=0;}
		}
	}
	
	public function onEntityClose(EntityDespawnEvent $event){
		if($event->getType() === 81){	//81=Snowball
			$entity = $event->getEntity();
			$shooter = $entity->shootingEntity;
			$posTo = $entity->getPosition();
			
			if($shooter instanceof Player && $posTo instanceof Position){
				$id = $shooter->getId();
				if(array_key_exists($id,$this->order) && $this->order[$id]>0){
					$this->order[$id]--;
					$posFrom = $shooter->getPosition();
//					$this->getLogger()->info($shooter->getName()." is at ".$posTo->__toString() );
					$shooter->teleport($posTo);
					$shooter->attack(5);
				}
			}
		}
	}

	public function onDisable(){
		
		$this->getLogger()->info("EnderPerl unloaded!");
	}
	
}

