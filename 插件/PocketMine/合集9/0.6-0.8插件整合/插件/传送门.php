<?php

/*
__PocketMine Plugin__
name=Portal
description=build your portal!hoho!
version=2.6
author=ljy
class=Portal
apiversion=10,11,12
*/

/* 
Changelog
===============

1.0
 -Initial release

2.0
 -Player past somewhere to teleport!
 -Fix some bugs

2.1:
 -Use ceil() replace round()
 -Add "player.join" handler
 -Fix some bugs

2.2
 -Now portal is a room not a point
 -Fix bug(PortalX is an illgel ........)
 -Add sensitivity

2.3
 -Optional broadcasting

2.3.1
 -Support PM Alpha 1.3.9

2.4:
 -fixed
 -Update /sp
 
2.5:
 -Rewrite the whole plugin
 
2.6:
 -Add "Edit mode"
 -Fix "If the world had not been loaded, and the portal's destination is it, when player enter portal, the server crash"
 -Shorten the commands
 -API update to 12
*/

define("MOVE", 0);
define("TOUCH", 1);

class Portal implements Plugin{
	private $api, $path, $portals, $config, $temp;
	
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	
	public function init(){
		$this->sss = 0;
		$this->temp = array();
		$this->path = $this->api->plugin->configPath($this);
		$this->api->addHandler("player.move", array($this, "move"));
		$this->api->console->register("portal", "", array($this, "command"));
		$this->api->console->alias("por", "portal");
		$this->config = new Config($this->path."config.yml", CONFIG_YAML, array(
			'Sensitivity' => 1,
			'Broadcast' => false,
			'Defaults' => array(
				'Disposable' => false,
				'Type' => MOVE,
			),
		));
		$this->config = $this->api->plugin->readYAML($this->path . "config.yml");
		$this->portals = array();
		if(file_exists($this->path . "portals.dat")){
			$file = file_get_contents($this->path . "portals.dat");
			$this->portals = json_decode($file, true);
		}
	}
	
	public function move($data, $event) {
		if(count($this->portals) == 0) return true;
		if($this->sss <= $this->config["Sensitivity"]){
			$this->sss = $this->sss + 1;
			return true;
		}
		else{
			$this->sss = 0;
		}
		$player = $data->player;
		$x = ceil($data->x);
		$y = ceil($data->y);
		$z = ceil($data->z);
		$level = $player->entity->level->getName();
		foreach($this->portals as $portalname => $portal){
			if($this->trigger($player, $portal, $x, $y, $z, $level, $portalname)){
				break;
			}
		}
	}
	
	private function trigger($player, $portal, $x, $y, $z, $level, $portalname){
		if($portal["type"] !== MOVE) return false;
		if($portal["trigger"]["pos1"]["level"] !== $level) return false;
		if($x >= (int)$portal["trigger"]["pos1"]["x"] and $x <= (int)$portal["trigger"]["pos2"]["x"]
			and $y >= (int)$portal["trigger"]["pos1"]["y"] and $y <= (int)$portal["trigger"]["pos2"]["y"]
			and $z >= (int)$portal["trigger"]["pos1"]["z"] and $z <= (int)$portal["trigger"]["pos2"]["z"]
		){
			if(isset($this->temp[$player->username]["edit"]) and $this->temp[$player->username]["edit"] == true){
				if($this->temp[$player->username]["inside"] != $portalname){
					$this->temp[$player->username]["inside"] = $portalname;
					$player->sendChat("[Portal]You enter [".$portalname."]");
				}
			}
			else{
				$targetLevel = $this->api->level->get($portal["target"]["level"]);
				if($targetLevel == false) return false;
				$targetX = (int)$portal["target"]["x"];
				$targetY = (int)$portal["target"]["y"];
				$targetZ = (int)$portal["target"]["z"];
				$pos = new Position($targetX, $targetY, $targetZ, $targetLevel);
				$player->teleport($pos);
				if($this->config["Broadcast"]){
					$builder = $portal["creator"];
					$playername = $player->username;
					$this->api->chat->broadcast("[Portal]".$playername." used portal ".$portalname." built by ".$builder." teleport to level : ".$targetLevel." x : ".ceil($targetX)." y : ".ceil($targetY)." z : ".ceil($targetZ));
				}
				if($portal["disposable"]) $this->delete($portalname);
			}
			$this->temp[$player->username]["inside"] = $portalname;
			return true;
		}
		else{
			if(isset($this->temp[$player->username]["edit"]) and $this->temp[$player->username]["edit"] == true and $this->temp[$player->username]["inside"] != null){
				$player->sendChat("[Portal]You leave [".$portalname."]");
			}
			$this->temp[$player->username]["inside"] = null;
			return false;
		}
	}
	
	private function delete($name){
		if(isset($this->portals[$name])){
			unset($this->portals[$name]);
			$this->save();
			return true;
		}
		else{
			return false;
		}
	}
	
	public function command($cmd, $params, $issuer, $alias){
		if(!($issuer instanceof Player)){
			$output = "[Portal]Please run this command in-game.";
			return $output;
		}
		$player = $issuer;
		$playername = $player->username;
		$x = ceil($player->entity->x);
		$y = ceil($player->entity->y);
		$z = ceil($player->entity->z);
		$level = $player->entity->level->getName();
		switch($params[0]){
			case "tar":
				if(isset($params[1])){
					$wn = $params[1];
					if($this->api->level->levelExists($wn)){
						$spawn = $this->api->level->get($wn)->getSafeSpawn();
						$this->temp[$playername]["target"]["level"] = $wn;
						$this->temp[$playername]["target"]["x"] = $spawn->x;
						$this->temp[$playername]["target"]["y"] = $spawn->y;
						$this->temp[$playername]["target"]["z"] = $spawn->z;
						$output = "[Portal]Target set. (world :$wn)";
					}
					else{
						$output = "[Portal]This world doesn't exist.";
					}
				}
				else{
					$this->temp[$playername]["target"]["level"] = $level;
					$this->temp[$playername]["target"]["x"] = $player->entity->x;
					$this->temp[$playername]["target"]["y"] = $player->entity->y;
					$this->temp[$playername]["target"]["z"] = $player->entity->z;
					$output = "[Portal]Target set. (".ceil($player->entity->x).",".ceil($player->entity->y).",".ceil($player->entity->z).",".$level.")";
				}
				return $output;
			case "cre":
				if($params[1] == ""){
					$output = "[Portal]Please input the portal's name.";
					return $output;
				}
				elseif(isset($this->portals[$params[1]])){
					$output = "[Portal]This name has been used.";
					return $output;
				}
				$temp = $this->gettemp($playername);
				if($temp == false){
					$output = "[Portal]Please set all things before you create.";
					return $output;
				}
				$temp["disposable"] = $this->config["Defaults"]["Disposable"];
				if(isset($params[2])){
					if($params[2] == "d"){
						$temp["disposable"] = true;
					}
					else{
						$output = "[Portal]Wrong parameter.";
						return $output;
					}
				}
				$temp["creator"] = $playername;
				$this->portals[$params[1]] = $temp;
				$this->save();
				$output = "[Portal]You create portal ".$params[1]." .";
				if($this->config["Broadcast"]){
					$this->api->chat->broadcast("[Portal]".$playername." built a portal ".$params[1]." at x : ".$x." y : ".$y." z : ".$z." level : ".$level);
				}
				return $output;
			case "p1":
				if(isset($this->temp[$playername]["trigger"]["pos2"])){
					if($this->temp[$playername]["trigger"]["pos2"]["level"] !== $level){
						$output = "[Portal]You are in different world.";
						return $output;
					}
				}
				$this->temp[$playername]["trigger"]["pos1"]["x"] = $x;
				$this->temp[$playername]["trigger"]["pos1"]["y"] = $y;
				$this->temp[$playername]["trigger"]["pos1"]["z"] = $z;
				$this->temp[$playername]["type"] = MOVE;
				$this->temp[$playername]["trigger"]["pos1"]["level"] = $level;
				$output = "[Portal]Pos1 set. ($x, $y, $z, $level)";
				return $output;
			case "p2":
				if(isset($this->temp[$playername]["trigger"]["pos1"])){
					if($this->temp[$playername]["trigger"]["pos1"]["level"] !== $level){
						$output = "[Portal]You are in different world.";
						return $output;
					}
				}
				$this->temp[$playername]["trigger"]["pos2"]["x"] = $x;
				$this->temp[$playername]["trigger"]["pos2"]["y"] = $y;
				$this->temp[$playername]["trigger"]["pos2"]["z"] = $z;
				$this->temp[$playername]["type"] = MOVE;
				$this->temp[$playername]["trigger"]["pos2"]["level"] = $level;
				$output = "[Portal]Pos2 set. ($x, $y, $z, $level)";
				return $output;
			case "del":
				if(isset($params[1]) == false){
					if(isset($this->temp[$playername]["edit"]) and $this->temp[$playername]["edit"] == true){
						$params[1] = $this->temp[$player->username]["inside"];
					}
					else{
						$output = "[Portal]Please input the portal's name.";
						return $output;
					}
				}
				if($this->delete($params[1])){
					$output = "[Portal]Deleting successfully.";
					if($this->config["Broadcast"]){
						$this->api->chat->broadcast("[Portal]".$player->iusername." deleted portal ".$params[1]);
					}
				}
				else{
					$output = "[Portal]Not exist.";
				}
				return $output;
			case "ed":
				if(isset($this->temp[$playername]["edit"]) and $this->temp[$playername]["edit"] == true){
					$this->temp[$playername]["edit"] = false;
					return "[Portal]Exit edit mode";
				}
				else{
					$this->temp[$playername]["edit"] = true;
					return "[Portal]Enter edit mode";
				}
			default:
				$output = "[Portal]Need help?\nljyloo's email address: 245158949@qq.com";
				return $output;
		}
	}
	
	private function gettemp($playername){
		if(isset($this->temp[$playername]) == false) return false;
		
		if(isset($this->temp[$playername]["target"])){
			if($this->temp[$playername]["type"] == MOVE){
				if(isset($this->temp[$playername]["trigger"]["pos1"])
					and isset($this->temp[$playername]["trigger"]["pos2"])
				){
					$temp = $this->temp[$playername];
					$max["x"] = max($temp["trigger"]["pos1"]["x"],$temp["trigger"]["pos2"]["x"]);
					$max["y"] = max($temp["trigger"]["pos1"]["y"],$temp["trigger"]["pos2"]["y"]);
					$max["z"] = max($temp["trigger"]["pos1"]["z"],$temp["trigger"]["pos2"]["z"]);
					$min["x"] = min($temp["trigger"]["pos1"]["x"],$temp["trigger"]["pos2"]["x"]);
					$min["y"] = min($temp["trigger"]["pos1"]["y"],$temp["trigger"]["pos2"]["y"]);
					$min["z"] = min($temp["trigger"]["pos1"]["z"],$temp["trigger"]["pos2"]["z"]);
					$temp["trigger"]["pos1"]["x"] = $min["x"];
					$temp["trigger"]["pos1"]["y"] = $min["y"];
					$temp["trigger"]["pos1"]["z"] = $min["z"];
					$temp["trigger"]["pos2"]["x"] = $max["x"];
					$temp["trigger"]["pos2"]["y"] = $max["y"];
					$temp["trigger"]["pos2"]["z"] = $max["z"];
					unset($this->temp[$playername]);
					return $temp;
				}
				else{
					return false;
				}
			}
			elseif(isset($this->temp[$playername]["trigger"])){
				$temp = $this->temp[$playername];
				unset($this->temp[$playername]);
				return $temp;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
	
	private function save(){
		$file = json_encode($this->portals);
		file_put_contents($this->path . "portals.dat", $file);
	}
	
	public function __destruct(){
	
	}
}