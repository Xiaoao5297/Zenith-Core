<?php
namespace ITouchSign\Listener;

use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\Listener;

use pocketmine\level\Position;
use pocketmine\block\SignPost;
use ITouchSign\ITouchSign;

class PlayerTouchListener implements Listener{
    
	private $plugin;

    public function __construct(ITouchSign $plugin){
        $this->plugin = $plugin;
    }
	
	/*
		x:y:z:level:
			to:world/x:y:z:level
			name: xxx
	*/
    public function onPlayerTouch(PlayerInteractEvent $event){
		$block = $event->getBlock();
		$player=$event->getPlayer();
		if($block instanceof SignPost){
			$date = $this->plugin->dat->get($this->plugin->getIndex($block));
			if($date!=null){
				$name = $date["name"];
				$date = explode(":",$date["to"]);//X Y Z L
				if(!isset($date[3])){//==world
					$pos = $this->plugin->getServer()->getLevelByName($date[0])->getSpawnLocation();
					$pos = new Position($pos->getX(),$pos->getY(),$pos->getY(),$pos->getLevel());
					$player->teleport($pos);
					$player->sendMessage("§4> §6传送成功, 到达目的地 §b".$name." §6世界");
				}else{//==xyzl
					$pos = new Position($date[0],$date[1],$date[2],$this->plugin->getServer()->getLevelByName($date[3]));
					$player->teleport($pos);
					$player->sendMessage("§4> §6传送成功, 到达目的地 §b".$name." §6地标");
				}
			}
		}
    }
}