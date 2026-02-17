<?php
/*
__PocketMine Plugin__
 name=BackDeath
 description=Back to death zone.
 version=1.0
 author=EkiFoX
 class=BackDeath
 apiversion=12,13
 */
class BackDeath implements Plugin {
	private $api;
	public $death = array();
	
	public function __construct(ServerAPI $api, $server = false) {
		$this->api = $api;
	}

	public function init() {
		$this->api->addHandler("player.death", array($this, "death"));
		$this->api->console->register("back", "Back to Death point", array($this, "command"));
		$this->api->ban->cmdWhitelist("back");
	}

	public function __destruct() {}
	
	public function command($cmd, $args, $issuer){
		switch($cmd){
			case "back":
				if($this->death[$issuer->username] instanceof Position){
					$issuer->teleport($this->death[$issuer->username]);
					$output = "[BackDeath] You have been teleported to death location.";
					unset($this->death[$issuer->username]);
				}else{
					$output = "[BackDeath] Failed.";
				}
				return $output;
				break;
		}
	}
	
	public function death($data, $event){
		$this->death[$data["player"]->username] = new Position(
			round($data["player"]->entity->x),
			round($data["player"]->entity->y),
			round($data["player"]->entity->z),
			$data["player"]->level
		);
		return true;
	}
}