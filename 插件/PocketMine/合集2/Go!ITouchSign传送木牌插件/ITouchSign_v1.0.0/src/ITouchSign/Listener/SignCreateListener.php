<?php
namespace ITouchSign\Listener;

use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;

use pocketmine\block\SignPost;
use ITouchSign\ITouchSign;

class SignCreateListener implements Listener{
    
	private $plugin;

    public function __construct(ITouchSign $plugin){
        $this->plugin = $plugin;
    }

	/*
		§9[ 传送世界牌子 ]/[ 传送地标牌子 ]
		§2传送至: §b$name §2世界/地标
		§2目标世界人数 [§a $map §2] 
		§2上次更新: §b23:25:45
		3 4行更新
	*/
	/*
		x:y:z:level:
			to:world/x:y:z:level
			name: xxx
	*/
	/*
		ITouchSign#worldname/x:y:z:l
	*/
    public function onSignChange(SignChangeEvent $event){
		$player = $event->getPlayer();
		$line1=$event->getLine(0);
		if($player->isOp() and stristr($line1,"ITouchSign")){
			$line1 = explode("#",$line1);
			$line1 = $line1[1];
			if($this->checkString($line1)){
				$this->plugin->initSign($line1,$event->getLine(1),$this->plugin->getIndex($event->getBlock()));
				$player->sendMessage("§4> §7木牌创建成功, 点击试一试吧");
			}else{
				$this->plugin->brFormat($player);
			}
		}
    }
	
	public function checkString($line){
		$divide = explode(":",$line);
		if(!isset($divide[1])){
			foreach($this->plugin->getServer()->getLevels() as $l){
				if($l->getName()==$divide[0])
					return true;
			}
			return false;
		}else{
			foreach($this->plugin->getServer()->getLevels() as $l){
				if($l->getName()==$divide[3])
					return true;
			}
			for($i=-1;$i<=2;$i++){
				if(!is_numeric($divide[$i]))
					return false;
			}
		}
		return true;
	}
}