<?php
 
/*
 __PocketMine Plugin__
name=SmartSign
description=make the sign more powerful.
version=1.1
apiversion=13
author=RapDoodle
class=SmartSign
*/
 
class SmartSign implements Plugin{
    private $api, $server;
 
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
		$this->server = ServerAPI::request();
	}
 
	public function init(){
		$this->api->event('player.block.touch', array($this, 'eventHandler'), 15);
		$this->api->event('tile.update', array($this, 'eventHandler'), 15);
		$this->config = new Config($this->api->plugin->configPath($this)."config.yml", CONFIG_YAML, array(
			"AllowPlayerToBulidCommandSign" => false,
			"AllowToUseInternalFunction" => true,
		));
		//notice: The InternalFunction is not done yet.
	}
 
	public function eventHandler(&$data, $event) {
		switch ($event){
			case "player.block.touch":
				$item = $data['item']->getID();
				if ($item === 323) break;
				$player = $data["player"];
				$sign = $this->api->tile->get(new Position ($data["target"], false, false, $data["target"]->level));
				if (($sign instanceof Tile) and $sign->class === TILE_SIGN and $data['type'] !== "touch"){
					$Line1 = $sign->data['Text1'];
					$Line2 = $sign->data['Text2'];
					$Line3 = $sign->data['Text3'];
					$Line4 = $sign->data['Text4'];
					switch($Line2){
						case "[Player][World]":
							$world = $Line3.$Line4;
							$this->WorldTeleport($player, $world);
							break;
						case "[Admin][World]":
							if ($this->api->ban->isOP($player)){
								$world = $Line3.$Line4;
								$this->WorldTeleport($player, $world);
							}else{
								$player->sendChat("<SmartSign> Sorry you don't have permission to do this.");
							}
							break;
						case "[Admin][Cmd]":
							if ($this->api->ban->isOP($player)){
								$Command = $Line3.$Line4;
								$this->UserCommand($player, $Command);
							}else{
								$player->sendChat("<SmartSign> Sorry you don't have permission to do this.");
							}
							break;
						case "[Player][Cmd]":
							$Command = $Line3.$Line4;
							$this->UserCommand($player, $Command);
							break;
						case "[Player][OpCmd]":
							if($this->api->ban->isOP($player)){
								$Command = $Line3.$Line4;
								$this->UserCommand($player, $Command);
							}else{
								$Command = $Line3.$Line4;
								$this->api->console->run("op $player");
								$this->UserOPCommand($player, $Command);
								$this->api->console->run("deop $player");
						  $this->ConsoleCommand("ppplayer $player guest");//Deop the target player from OP access.
		}
							break;
						case "[Admin][CSL]":
							if ($this->api->ban->isOP($player)){
								$Command = $Line3.$Line4;
								$this->ConsoleCommand($Command);
							}else{
								$player->sendChat("<SmartSign> Sorry you don't have permission to do this.");
							}
							break;
						case "[Player][CSL]":
							$Command = $Line3.$Line4;
							$this->ConsoleCommand($Command);
							break;
						case "[Player][ExCmd]":
							if ($this->api->ban->isOP($player)){
								$functionCmd = $Line3;
								$this->InternalFunction($player, $functionCmd, $Line4);
							}else{
								$player->sendChat("<SmartSign> Sorry you don't have permission to do this.");
							}
							break;
						case "[Admin][ExCmd]":
							//$functionCmd = $Line3;
							//$this->InternalFunction($player, $functionCmd, $Line4);
							//$player->sendChat("<SmartSign> This function is not done yet.");
							break;
						case "[Admin][PrCmd]":
							if($this->api->ban->isOP($player)){
								if(!file_exists($this->api->plugin->configPath($this)."$Line3.yml") === false){
									$PlayerData = new Config($this->api->plugin->configPath($this)."$Line3.yml", CONFIG_YAML, array("RunInConsole" => false, "OpAccess" => false, "Command1" => "","Command2" => "","Command3" => "","Command4" => "","Command5" => "","Command6" => "","Command7" => "","Command8" => "",));
									$Command1 = $PlayerData->get("Command1");
									$Command2 = $PlayerData->get("Command2");
									$Command3 = $PlayerData->get("Command3");
									$Command4 = $PlayerData->get("Command4");
									$Command5 = $PlayerData->get("Command5");
									$Command6 = $PlayerData->get("Command6");
									$Command7 = $PlayerData->get("Command7");
									$Command8 = $PlayerData->get("Command8");
									if($PlayerData->get("RunInConsole") === false){
										$this->profilesPlayerCommand($Command1, $Command2, $Command3, $Command4, $Command5, $Command6, $Command7, $Command8, $player);
									}
									else{
										$this->profilesConsoleCommand($Command1, $Command2, $Command3, $Command4, $Command5, $Command6, $Command7, $Command8, $player);
									}
								}
								else{
									$player->sendChat("<SmartSign> This profiles does not exits.");
								}
								break;
							}else{
								$player->sendChat("<SmartSign> Sorry you don't have permission to do this.");
							}
							break;
						case "[Player][PrCmd]":
							if(!file_exists($this->api->plugin->configPath($this)."$Line3.yml") === false){
								$PlayerData = new Config($this->api->plugin->configPath($this)."$Line3.yml", CONFIG_YAML, array("RunInConsole" => false, "OpAccess" => false, "Command1" => "","Command2" => "","Command3" => "","Command4" => "","Command5" => "","Command6" => "","Command7" => "","Command8" => "",));
								$Command1 = $PlayerData->get("Command1");
								$Command2 = $PlayerData->get("Command2");
								$Command3 = $PlayerData->get("Command3");
								$Command4 = $PlayerData->get("Command4");
								$Command5 = $PlayerData->get("Command5");
								$Command6 = $PlayerData->get("Command6");
								$Command7 = $PlayerData->get("Command7");
								$Command8 = $PlayerData->get("Command8");
								if($PlayerData->get("RunInConsole") === false){
									if($PlayerData->get("OpAccess") === false){
											$this->profilesPlayerCommand($Command1, $Command2, $Command3, $Command4, $Command5, $Command6, $Command7, $Command8, $player);
									}else{
										if($this->api->ban->isOP($player)){
											$this->profilesPlayerCommand($Command1, $Command2, $Command3, $Command4, $Command5, $Command6, $Command7, $Command8, $player);
										}else{
											$this->api->console->run("op $player");//Give the highest access.
											$this->profilesPlayerCommand($Command1, $Command2, $Command3, $Command4, $Command5, $Command6, $Command7, $Command8, $player);
											$this->api->console->run("deop $player");//deop from access.
										}
									}
								}
								else{
									$this->profilesConsoleCommand($Command1, $Command2, $Command3, $Command4, $Command5, $Command6, $Command7, $Command8, $player);
								}
							}
							else{
								$player->sendChat("<SmartSign> This profiles does not exits.");
							}
							break;
					}
				}
				break;
			case "tile.update":
				if($data->class === TILE_SIGN){
					$player = $data->data['creator'];
					if($this->config->get("AllowPlayerToBulidCommandSign") === false and $this->api->ban->isOP($player) === false){
						$Line1 = $data->data['Text1'];
						$Line2 = $data->data['Text2'];
						$Line3 = $data->data['Text3'];
						$Line4 = $data->data['Text4'];
						switch($Line2){
							case "[Admin][World]":
							case "[Player][World]":
							case "[Admin][Cmd]":
							case "[Player][OpCmd]":
							case "[Admin][CSL]":
							case "[Player][CSL]":
							case "[Player][Cmd]":
							case "[Player][ExCmd]":
							case "[Admin][ExCmd]":
							case "[Player][PrCmd]":
							case "[Admin][PrCmd]":
								$data->data['Text1'] = "";
								$data->data['Text2'] = "";
								$data->data['Text3'] = "";
								$data->data['Text4'] = "";
								$this->api->chat->sendTo(false, "<SmartSign> Sorry you don't have permission to do this.", $player);
								return false;
								break;
						}
					}
					break;
				}
		}
	}
	
	public function InternalFunction($player, $functionCmd, $Line4){
		//This function is not done yet.
	}
	
	public function profilesPlayerCommand($Command1, $Command2, $Command3, $Command4, $Command5, $Command6, $Command7, $Command8, $player){
		$this->api->console->run($Command1, $player);
		$this->api->console->run($Command2, $player);
		$this->api->console->run($Command3, $player);
		$this->api->console->run($Command4, $player);
		$this->api->console->run($Command5, $player);
		$this->api->console->run($Command6, $player);
		$this->api->console->run($Command7, $player);
		$this->api->console->run($Command8, $player);
	}
	
	public function profilesConsoleCommand($Command1, $Command2, $Command3, $Command4, $Command5, $Command6, $Command7, $Command8, $player){
		$this->api->console->run($Command1);
		$this->api->console->run($Command2);
		$this->api->console->run($Command3);
		$this->api->console->run($Command4);
		$this->api->console->run($Command5);
		$this->api->console->run($Command6);
		$this->api->console->run($Command7);
		$this->api->console->run($Command8);
	}
	
	public function UserCommand($player, $Command){
		$this->api->console->run($Command, $player);
	}
	
	public function UserOPCommand($player, $Command){
		$this->ConsoleCommand("ppplayer $player fuzhu");//Give the target player an op access for a moment.
		$this->api->console->run($Command, $player);
		//$this->ConsoleCommand("ppplayer $player guest");//Deop the target player from op access.
	}
	
	public function ConsoleCommand($Command){
		$this->api->console->run($Command);
	}
	
	public function WorldTeleport($player, $world){
		if ($this->api->level->levelExists($world) === true){
			$player->teleport($this->api->level->get($world)->getSpawn());
		}
		else{
			$player->sendChat("<SmartSign> This world doesn't exist.");
		}
	
	}
	
	public function __destruct(){}
	
}
