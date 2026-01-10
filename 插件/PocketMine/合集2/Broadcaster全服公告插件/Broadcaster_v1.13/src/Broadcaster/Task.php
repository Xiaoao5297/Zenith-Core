<?php

/*
 * Broadcaster (v1.13) by EvolSoft
 * Developer: EvolSoft (Flavius12)
 * Website: http://www.evolsoft.tk
 * Date: 09/11/2014 3:03 PM (GMT)
 * Copyright & License: (C) EvolSoft. All Rights Reserved.
 */


namespace Broadcaster;

use pocketmine\Server;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

class Task extends PluginTask{

    public function __construct(Main $plugin){
        parent::__construct($plugin);
        $this->plugin = $plugin;
		$this->length = -1;
    }

    public function onRun($currentTick){
    	$this->plugin = $this->getOwner();
    	$this->plugin->cfg = $this->plugin->getConfig()->getAll();
    	if($this->plugin->cfg["broadcast-enabled"]==true){
    		$this->length=$this->length+1;
    		$messages = $this->plugin->cfg["messages"];
    		$messagekey = $this->length;
    		$message = $messages[$messagekey];
    		if($this->length==count($messages)-1) $this->length = -1;
    		Server::getInstance()->broadcastMessage($this->plugin->translateColors("&", $this->plugin->broadcast($this->plugin->cfg, $message)));
    	}
    }

}
?>