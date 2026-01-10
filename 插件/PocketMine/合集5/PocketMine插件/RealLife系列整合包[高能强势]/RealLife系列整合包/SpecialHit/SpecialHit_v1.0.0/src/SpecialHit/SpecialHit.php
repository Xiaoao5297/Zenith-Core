<?php

namespace SpecialHit;


use pocketmine\block\Block;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\level\sound\DoorBumpSound;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;


class SpecialHit extends PluginBase implements Listener
{
    public function onEnable()
    {
        $this->getLogger()->info("SpecialHit----效果攻击");
        $this->getLogger()->info("Spiderman开发,请加入交流群哟~");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    public function onDamage(EntityDamageEvent $event)
    {
        $entity = $event->getEntity();
        if($event instanceof EntityDamageByEntityEvent)
        {
            $entity->getLevel()->addSound(new DoorBumpSound($entity));
            $entity->getLevel()->addParticle(new DestroyBlockParticle(new Vector3($entity->getX(), $entity->getY(), $entity->getZ()), Block::get(152)));
        }
    }
}
