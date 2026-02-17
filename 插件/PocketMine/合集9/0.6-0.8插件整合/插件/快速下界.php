<?php
/*
__PocketMine Plugin__
name=NetherQuick
description=Tap on Nether Reactor to go to the second world loaded or to the default world if already in second world
version=2.3
author=Glitchmaster_PE and wies
class=NetherQuick
apiversion=9,10
*/

class NetherQuick implements Plugin{
	private $api;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}

	public function init(){
		if(!file_exists('./worlds/Nether/')){
			$file = @file_get_contents("http://forums.pocketmine.net/index.php?attachments/nether-zip.359/");
            if($file === false){
				console('[ERROR][NetherQuick] Failed downloading the world, check your internet connection or download the map manualy');
				return false;
			}else{
				file_put_contents('./worlds/netherzip.zip', $file);
				$zip = new ZipArchive;
				$result = $zip->open('./worlds/netherzip.zip');
				$zip->extractTo('./worlds/');
				$zip->close();
				unlink('./worlds/netherzip.zip');
			}
		}
		$this->api->level->loadLevel("Nether");
		$this->api->addHandler("player.block.touch", array($this, "touchHandler"));
		$this->config = new Config($this->api->plugin->configPath($this) . "config.yml", CONFIG_YAML, array("ItemID" => "247", "RequireCorrectPattern" => true));
		$this->netherReactorIds = array(4,4,4,4,4,41,41,41,41,0,0,0,0,4,4,4,4,4,4,4,4,4,0,0,0,0);
		$this->netherReactorPattern = array(array(0,-1,0),array(1,-1,0),array(-1,-1,0),array(0,-1,1),array(0,-1,-1),array(1,-1,1),array(1,-1,-1),array(-1,-1,1),array(-1,-1,-1),
											array(1,0,0),array(-1,0,0),array(0,0,1),array(0,0,-1),array(1,0,1),array(1,0,-1),array(-1,0,1),array(-1,0,-1),
											array(0,1,0),array(1,1,0),array(-1,1,0),array(0,1,1),array(0,1,-1),array(1,1,1),array(1,1,-1),array(-1,1,1),array(-1,1,-1));
    }

	public function touchHandler($data){
		if($data["target"]->getID() === $this->config->get("ItemID")){
		    $player = $data["player"];
			if($this->config->get("RequireCorrectPattern") == true){
				$x = $data["target"]->x;
				$y = $data["target"]->y;
				$z = $data["target"]->z;
				$level = $player->level;
				$blocks = array();
				foreach($this->netherReactorPattern as $val){
					$blocks[] = $level->getBlock(new Vector3($x + $val[0], $y + $val[1], $z + $val[2]))->getID();
				}
				if($this->netherReactorIds !== $blocks){
					$player->sendChat("Incorrect pattern!");
					return false;
				}
			}
			if($level === $this->api->level->get("Nether")){
				$player->teleport($this->api->level->getDefault()->getSpawn());
			}else{
				$player->teleport($this->api->level->get("Nether")->getSpawn());
			}
			return false;
		}
	}

	public function __destruct(){}
}
?>

