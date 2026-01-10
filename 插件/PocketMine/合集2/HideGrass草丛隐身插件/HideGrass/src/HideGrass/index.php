<?php

namespace HideGrass;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\Player;
use pocketmine\entity\Effect;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class MainClass extends PluginBase implements Listener{

	public function onLoad(){
		$this->getLogger()->info(TextFormat::WHITE . "I've been loaded!");
	}

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new BroadcastPluginTask($this), 200000);

    }

	public function onDisable(){

	}



	/**
	 * @param PlayerMoveEvent $event
	 *
	 * @priority        NORMAL
	 * @ignoreCancelled false
	 */
	public function onSpawn(PlayerMoveEvent $event){
		$p=$event->getPlayer()->getPosition();
		$pp=$event->getPlayer();
$x=$p->x;
$y=$p->y;
$z=$p->z;
 $id1=$pp->getLevel()->getBlock(new Vector3($x+1,$y,$z))->getId();
 $id2=$pp->getLevel()->getBlock(new Vector3($x-1,$y,$z))->getId();
 $id3=$pp->getLevel()->getBlock(new Vector3($x,$y,$z+1))->getId();
 $id4=$pp->getLevel()->getBlock(new Vector3($x,$y,$z-1))->getId();

 
if(($id1==37 || $id1==38 || $id1==31 || $id1==175) || ($id2==37 || $id2==38 || $id2==31 || $id2==175) || ($id3==37 || $id3==38 || $id3==31 || $id3==175) || ($id4==37 || $id4==38 || $id4==31 || $id4==175)){

		$effect = Effect::getEffect(Effect::INVISIBILITY);
		$effect->setDuration(20 * 10);
        $effect->setVisible(false);
		$pp->addEffect($effect);  


}else{
		$pp->removeEffect(Effect::INVISIBILITY);

}
}



}
