<?php
/*
__PocketMine Plugin__
 name=InfWorld
 description=Simple infinite world by loading a new world.
 version=1.3
 author=EkiFoX
 class=InfWorld
 apiversion=13
 */
class InfWorld implements Plugin {
	private $api;
	
	public $minworld = 2; //0
	public $maxworld = 12; //10
	public $lang = array(
		"newworld" => "You have been teleported in a new map: ",
		"oldworld" => "You have been teleported on an old map: ",
		"genworld" => "We generating a new map for you.",
		"maxcoord" => "You are at maximum coordinates. Walk back.",
		"mincoord" => "You are at minimum coordinates. Walk forward."
	);
	
	public function __construct(ServerAPI $api, $server = false) {
		$this->api = $api;
	}

	public function init() {
		$this->api->addHandler("player.move", array($this, "move"));
		$this->api->console->register("infinite", "Information of infinite world plugin", array($this, "command"));
		$this->api->ban->cmdWhitelist("infinite");
		console("[InfWorld] Loaded, version 1.3");
	}

	public function __destruct() {}
	
	public function command($cmd, $params, $issuer) {
		switch ($cmd) {
			case "infinite":
				$issuer->sendChat("Plugin made by EkiFoX");
				$issuer->sendChat("*For PocketMine Forum*");
				break;
		}
	}
	
	public function getSafeZone($xs, $ys, $zs, $lvl){
	//Code from PocketMine-MP/src/world/Level.php 
			$x = (int)round($xs);
			$y = (int)round($ys);
			$z = (int)round($zs);
			$lvl = (string)$lvl;
			
		$world = $this->api->level->get($lvl);
		if ($world != false){
			for(; $y > 0; --$y){
				$v = new Vector3($x, $y, $z);
				$b = $world->getBlock($v);
				if($b === false){
					return new Position($xs, $ys, $zs, $world);
				}elseif(!($b instanceof AirBlock)){
					break;
				}
			}
			for(; $y < 128; ++$y){
				$v = new Vector3($x, $y, $z);
				if($world->getBlock($v) instanceof AirBlock){
					return new Position($x, $y, $z, $world);
				}else{
					++$y;
				}
			}
			return new Position($x, $y, $z, $world);
		}else{
			console("Can't get a safe zone for teleport a player.");
			return false;
		}
	}
  
	public function move($data){
		$plobj = $this->api->player->get($data->name);
		$x = round($data->x);
		$z = round($data->z);
		if($x == 255){
			$world = $plobj->level->getName();
			$newworld = ((int)$world + 1);
			if(($newworld > $this->minworld) AND ($newworld < $this->maxworld)){
				if($this->api->level->loadLevel($newworld)){					
					$safe = $this->getSafeZone(2,128,$z,$newworld);
					if($safe != false){
					$plobj->teleport($safe);
					$plobj->sendChat("[InfWorld] ".$this->lang['newworld'].$newworld);
					}else $plobj->sendChat("[InfWorld] Failed.");
					
				}else{
					$plobj->sendChat("[InfWorld] ".$this->lang['genworld']);
					$this->api->level->generateLevel($newworld);
				}
			}else{
				$plobj->sendChat("[InfWorld] ".$this->lang['maxcoord']);
			}
		}
		if($x == 0){
			$world = $plobj->level->getName();
			$newworld = ((int)$world - 1);
			if(($newworld > $this->minworld) AND ($newworld < $this->maxworld)){
				if($this->api->level->loadLevel($newworld)){					
					$safe = $this->getSafeZone(254,128,$z,$newworld);
					if($safe != false){
					$plobj->teleport($safe);
					$plobj->sendChat("[InfWorld] ".$this->lang['oldworld'].$newworld);
					}else $plobj->sendChat("[InfWorld] Failed.");
					
				}else{
					$plobj->sendChat("[InfWorld] ".$this->lang['genworld']);
					$this->api->level->generateLevel($newworld);
				}
			}else{
				$plobj->sendChat("[InfWorld] ".$this->lang['mincoord']);
			}
		}
		if($z == 0){
			$world = $plobj->level->getName();
			$newworld = ((int)$world - 1);
			if(($newworld > $this->minworld) AND ($newworld < $this->maxworld)){
				if($this->api->level->loadLevel($newworld)){					
					$safe = $this->getSafeZone($x,128,254,$newworld);
					if($safe != false){
					$plobj->teleport($safe);
					
					$plobj->sendChat("[InfWorld] ".$this->lang['oldworld'].$newworld);
					}else $plobj->sendChat("[InfWorld] Failed.");
					
				}else{
					$plobj->sendChat("[InfWorld] ".$this->lang['genworld']);
					$this->api->level->generateLevel($newworld);
				}
			}else{
				$plobj->sendChat("[InfWorld] ".$this->lang['mincoord']);
			}
		}
		if($z == 255){
			$world = $plobj->level->getName();
			$newworld = ((int)$world + 1);
			if(($newworld > $this->minworld) AND ($newworld < $this->maxworld)){
				if($this->api->level->loadLevel($newworld)){					
					$safe = $this->getSafeZone($x,128,2,$newworld);
					if($safe != false){
					$plobj->teleport($safe);
					
					$plobj->sendChat("[InfWorld] ".$this->lang['newworld'].$newworld);
					}else $plobj->sendChat("[InfWorld] Failed.");
					
				}else{
					$plobj->sendChat("[InfWorld] ".$this->lang['genworld']);
					$this->api->level->generateLevel($newworld);
				}
			}else{
				$plobj->sendChat("[InfWorld] ".$this->lang['maxcoord']);
			}
		}
		return true;
	}
}