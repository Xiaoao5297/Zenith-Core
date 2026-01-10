<?php
namespace MTNT;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\level\Level;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\entity\Entity;
use pocketmine\entity\PrimedTNT;//tnt
use pocketmine\level\Position;
use pocketmine\level\ChunkManager;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\level\sound\TNTPrimeSound;

use MTNT\entity\Fireball;

class Main extends PluginBase implements Listener{
	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this ,$this);
		Entity::registerEntity(Fireball::class);
		$this->getServer()->getLogger()->info("§7[§3手扔TNT§7] §c用TNT和火球对着空气长按…");
		$this->getServer()->getLogger()->info("§7[§3手扔TNT§7] §c作者§7: §6anseEND");
	}

	public function onPlayerTouch(PlayerInteractEvent $event){
		$block = $event->getBlock();
		$player = $event->getPlayer();
		$v3 = new Vector3($player->getX(),($player->getY())+2.7,$player->getZ());
		$level=$block->getLevel();
		if($event->getItem()->getId()==46){
		if($player->isOp()){
		$event->setCancelled();
	$PrimedTNT = new PrimedTNT($level->getChunk($v3->x >> 4, $v3->z >> 4), new CompoundTag("", [
			"Pos" => new ListTag("Pos", [
				new DoubleTag("", $v3->x),
				new DoubleTag("", $v3->y),
				new DoubleTag("", $v3->z)
			]),
			"Motion" => new ListTag("Motion", [
				new DoubleTag("", -sin($player->yaw / 180 * M_PI)  * cos($player->pitch / 180 * M_PI)),
				new DoubleTag("", -sin($player->pitch / 180 * M_PI)),
				new DoubleTag("", cos($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI))
			]),
			"Rotation" => new ListTag("Rotation", [
				new FloatTag("", $player->yaw),
				new FloatTag("", $player->pitch)
			]),
			]));
			$PrimedTNT->setPosition($v3);
			$PrimedTNT->spawnToAll();
			$level->addSound(new TNTPrimeSound($v3));
			}else{
			$event->setCancelled();
			}
			}
			if($event->getItem()->getId()==385){
			$Fireball = new Fireball($level->getChunk($v3->x >> 4, $v3->z >> 4), new CompoundTag("", [
			"Pos" => new ListTag("Pos", [
				new DoubleTag("", $v3->x),
				new DoubleTag("", $v3->y),
				new DoubleTag("", $v3->z)
			]),
			"Motion" => new ListTag("Motion", [
				new DoubleTag("", -sin($player->yaw / 180 * M_PI)  * cos($player->pitch / 180 * M_PI)),
				new DoubleTag("", -sin($player->pitch / 180 * M_PI)),
				new DoubleTag("", cos($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI))
			]),
			"Rotation" => new ListTag("Rotation", [
				new FloatTag("", $player->yaw),
				new FloatTag("", $player->pitch)
			]),
			]));
			$Fireball->setPosition($v3);
			$Fireball->spawnToAll();
			}
}




}
























