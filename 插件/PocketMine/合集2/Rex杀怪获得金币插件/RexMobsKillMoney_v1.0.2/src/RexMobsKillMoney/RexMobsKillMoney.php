<?php

namespace RexMobsKillMoney;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityCombustEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use onebone\economyapi\EconomyAPI;//經濟

class RexMobsKillMoney extends PluginBase implements Listener{

	private $list = [];
	private $status = [];
	private $config;

	public function onLoad(){
		$this->getLogger()->info(TextFormat::WHITE . "RexMobsKillMoney load done");
	}

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info(TextFormat::DARK_GREEN . "RexMobsKillMoney enable！");
    }

	public function onDisable(){
		$this->getLogger()->info(TextFormat::DARK_RED . "RexMobsKillMoney disable！");
	}

public function onEntityDamage(EntityDamageEvent $event){
		$entity = $event->getEntity();
    $damager = $event->getDamager();
    EconomyAPI::getInstance()->addMoney($damager,30);
    $damager->getPlayer()->sendMessage("[ RexMobsKillMoney ]你殺死了Mob 獲得30元");
				}
			}
