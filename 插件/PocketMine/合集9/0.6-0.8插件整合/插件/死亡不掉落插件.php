<?php

/*
 __PocketMine Plugin__
name=RespawnWithoutDrop
description=Respawn Without Droping Any Item(Back to spawn)
version=1.0
author=RapDoodle
class=RespawnWithoutDrop
apiversion=12
*/

class RespawnWithoutDrop implements Plugin{ 
	private $api;
	
	public function __construct(ServerAPI $api, $server =false){ 
		$this->api = $api;
		$this->server = ServerAPI::request();
	}
	
	public function init(){ 
		$this->api->addHandler("entity.health.change", array($this, "event"), 50);
	}
	
	public function event($data, $event){
		$player = $data["entity"]->player;
		if($player === false){
		
		}else{
			$hp = $data["health"];
			if($hp <= 0){
				$player = $data["entity"]->player;
				$player->entity->setHealth(20, "respawn", true);
				$data = array("player" => $player, "cause" => $data["cause"]);
				$player->teleport($this->server->spawn);
				$ms = "<server> ".$player." just respawn from dead.";
				$this->api->chat->broadcast($ms);
				return false;
			}
		//return false; 
		}
	}
	
	public function __destruct(){ 
		
	}
}