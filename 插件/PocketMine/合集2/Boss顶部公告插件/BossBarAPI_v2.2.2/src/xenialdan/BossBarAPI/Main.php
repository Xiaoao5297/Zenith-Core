<?php

/*
 * BossBarAPI
 * A plugin by XenialDan aka thebigsmileXD
 * http://github.com/thebigsmileXD/BossBarAPI
 * Sending the Bossbar independ from the Server software
 */
namespace xenialdan\BossBarAPI;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\network\protocol\SetEntityDataPacket;

class Main extends PluginBase implements Listener{
	private static $instance = null;

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getNetwork()->registerPacket(BossEventPacket::NETWORK_ID, BossEventPacket::class);
		$this->getServer()->getNetwork()->registerPacket(UpdateAttributesPacket::NETWORK_ID, UpdateAttributesPacket::class);
		$this->getServer()->getNetwork()->registerPacket(SetEntityDataPacket::NETWORK_ID, SetEntityDataPacket::class);
	}

	public static function getInstance(){
		return self::$instance;
	}

	public function onLoad(){
		self::$instance = $this;
	}
}