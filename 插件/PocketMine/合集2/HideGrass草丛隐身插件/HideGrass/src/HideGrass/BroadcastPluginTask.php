<?php

namespace HideGrass;

use pocketmine\Player;
use pocketmine\scheduler\PluginTask;
use pocketmine\Server;

class BroadcastPluginTask extends PluginTask{

	public function onRun($currentTick){
		Server::getInstance()->broadcastMessage("");
	}
}
