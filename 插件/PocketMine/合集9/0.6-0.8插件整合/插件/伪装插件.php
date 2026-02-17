<?php

/*
 __PocketMine Plugin__
name=Disguise
description=Players can disguise as mobs or animals
version=1.0
author=unknow
class=Disguise
apiversion=11
*/

class Disguise implements Plugin{ 
	private $api; 
	
	public function __construct(ServerAPI $api, $server =false){ 
		$this->api =$api; 
	}
	
	public function init(){ 
		$this->api->console->register("d", "Disguise commands. ", array($this, "handleCommand")); 
		$this->api->addHandler("player.join", array($this, "eventHandler"), 6); 
	}
	
	public function __destruct(){
		
	}
	
	public function eventHandler($data, $event) {
		switch($event) {
			case "player.join":
				break; 
		}
	}
	
	public function handleCommand($cmd, $arg, $issuer, $alias){ 
		switch($cmd){ 
			case "d": 
				if(!($issuer instanceof Player)){ 
					console("Please run this command in-game."); 
					break; 
				}
				if(!($this->api->ban->isOp($issuer->iusername))) {
					return("Opps... you are not OP/Admin, So stop trying. "); 
				}
				switch(count($arg)) {
					case 0: 
						if($this->isMob[$issuer->username][0] == 1) {
							foreach($issuer->level->players as $p) {
								if(strtolower($p->eid) != strtolower($issuer->eid)) {
									$this->recreateEntity($p, $issuer); 
									$issuer->sendChat("You are now undisguised."); 
								}
							}
							$this->isMob[$issuer->username][0] =0; 
						}
						else{ 
							return("========Disguise======== You may disguise as: chicken, cow, sheep, zombie, creeper, skeleton, spider, pigzombie. "); 
						}
						break; 
					case 1: 
						$mobdataid =0x00; 
						switch(strtolower($arg[0])) {
							case "chicken": 
								$mobdataid =0x0a; 
								break; 
							case "cow": 
								$mobdataid =0x0b; 
								break; 
							case "pig": 
								$mobdataid =0x0c; 
								break; 
							case "sheep": 
								$mobdataid =0x0d; 
								break; 
							case "zombie": 
								$mobdataid =0x20; 
								break; 
							case "creeper": 
								$mobdataid =0x21; 
								break; 
							case "skeleton": 
								$mobdataid =0x22; 
								break; 
							case "spider": 
								$mobdataid =0x23; 
								break; 
							case "pigzombie": 
								$mobdataid =0x24; 
								break; 
							default: 
								return("Error: Unknown mob type. "); 
						}
						foreach($issuer->level->players as $p) {
							if(strtolower($p->eid) != strtolower($issuer->eid)) {
								$this->recreateEntityToMob($p, $mobdataid, $issuer); 
							}
						}
						$this->isMob[$issuer->username][0] =1; 
						$issuer->sendChat("你现在伪装成 " .$arg[0] .". 输入/d取消伪装"); 
				}
				break; 
		}
	}
	
	public function recreateEntity($p, $issuer) {
		$p->dataPacket(MC_REMOVE_ENTITY, array( "eid" => $issuer->eid ));
		$p->dataPacket(MC_ADD_PLAYER, array( "clientID" => $issuer->clientID, "username" => $issuer->username, "eid" => $issuer->eid, "x" => $issuer->entity->x, "y" => $issuer->entity->y, "z" => $issuer->entity->z, "yaw" => 0, "pitch" => 0, "unknown1" => 0, "unknown2" => 0, "metadata" => $issuer->entity->getMetadata())); 
	}
	
	public function recreateEntityToMob($p, $mobid, $issuer) {
		$p->dataPacket(MC_REMOVE_ENTITY, array( "eid" => $issuer->eid ));
		$flags = 0; 
		$flags |= $issuer->entity->fire > 0 ?1:0; 
		$flags |= ($issuer->entity->crouched === true ?0b10:0) << 1; 
		$flags |= ($this->entity->inAction === true ?0b10000:0); 
		$d = array( 0 => array("type" => 0, "value" => $flags), 1 => array("type" => 1, "value" => $issuer->entity->air), 16 => array("type" => 0, "value" => 0), 17 => array("type" => 6, "value" => array(0, 0, 0)), );
		if($mobid == 0x0d){ 
			$d[16]["value"] = (($this->data["Sheared"] == 1 ?1:0) << 4) |(mt_rand(0,15) &0x0F); 
		}
		$p->dataPacket(MC_ADD_MOB, array( "type" => $mobid, "eid" => $issuer->eid, "x" => $issuer->entity->x, "y" => $issuer->entity->y, "z" => $issuer->entity->z, "yaw" => 0, "pitch" => 0, "metadata" => $d ));
		$p->dataPacket(MC_SET_ENTITY_MOTION, array( "eid" => $issuer->eid, "speedX" => (int) ($issuer->entity->speedX *400), "speedY" => (int) ($issuer->entity->speedY *400), "speedZ" => (int) ($issuer->entity->speedZ *400) ));
	}
}