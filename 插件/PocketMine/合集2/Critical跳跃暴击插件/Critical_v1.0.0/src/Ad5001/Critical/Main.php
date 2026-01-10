<?php
namespace Ad5001\Critical ; 
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\math\Vector3;
use pocketmine\entity\Effect;
use pocketmine\Server;
use pocketmine\utils\Random;
use pocketmine\utils\TextFormat as C;
use pocketmine\level\particle\CriticalParticle;
 use pocketmine\Player;


class Main extends PluginBase implements Listener{
public function onEnable(){
$this->getServer()->getPluginManager()->registerEvents($this, $this);
 }
public function onHurt(EntityDamageEvent $event){
	if($event instanceof EntityDamageByEntityEvent) {
		if($event->getDamager() instanceof Player) {
			$pl = $event->getDamager();
			$air = $pl->getLevel()->getBlock(new Vector3($pl->x, $pl->y - 0.75, $pl->z))->getId();
			$air2 = $pl->getLevel()->getBlock(new Vector3($pl->x, $pl->y, $pl->z))->getId();
			if($air === 0 and $air2 === 0 and !$pl->hasEffect(Effect::BLINDNESS)) {
				$et = $event->getEntity();
				$ev = new CriticalEvent($pl, $et);
				$this->getServer()->getPluginManager()->callEvent($ev);
				if(!$ev->isCancelled()) {
				$pl->sendPopup(C::RED."Critical !");
				$event->setDamage($event->getDamage(EntityDamageByEntityEvent::MODIFIER_BASE) * 1.5);
				$particle = new CriticalParticle(new Vector3($et->x, $et->y + 1, $et->z));
					$random = new Random((int) (microtime(true) * 1000) + mt_rand());
					for($i = 0; $i < 60; ++$i){
						$particle->setComponents(
						$et->x + $random->nextSignedFloat() * $et->x,
						$et->y + 1.5 + $random->nextSignedFloat() * $et->y + 1.5,
						$et->z + $random->nextSignedFloat() * $et->z
						);
			      $pl->getLevel()->addParticle($particle);
				  }
				}
			}
		}
	}
}
 public function onCommand(CommandSender $issuer, Command $cmd, $label, array $params){
switch($cmd->getName()){
}
return false;
 }
}