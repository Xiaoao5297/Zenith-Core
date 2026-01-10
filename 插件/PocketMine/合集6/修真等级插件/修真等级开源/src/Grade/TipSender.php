<?php

namespace Grade;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\scheduler\Task;

use Grade\Grade;
use PocketVIP\PocketVIP;
use onebone\economyapi\EconomyAPI;
use onebone\economyland\EconomyLand;
class TipSender extends Task{
	private $plugin;

	public function __construct(Grade $plugin){
		$this->plugin = $plugin;
	}

	public function onRun($currentTick){  //当前时间
		if ($currentTick % 600 == 0){ //60*5*20 = 600 (5 min)
			$this->plugin->save();
			$this->plugin->getLogger()->info(TextFormat::GRAY."已自动保存");
		}

		foreach ($this->plugin->getServer()->getOnlinePlayers() as $key => $value) {
			$exp = $this->plugin->getEXP($value);
			$grade = $this->plugin->getGrade($value);
			

			$str = "\n\n";
			$str .= TextFormat::AQUA."仙石:".EconomyAPI::getInstance()->myMoney($value);
			$str .= TextFormat::LIGHT_PURPLE."仙域人数:".count($this->plugin->getServer()->getOnlinePlayers())."人";
			
			$str .= TextFormat::AQUA."  仙阶.".$grade;
			$str .= " ".$this->plugin->getTitle($grade);

			$str .= "\n";
			$str .= TextFormat::YELLOW."空间:".$value->getLevel()->getFolderName();


			$inven = $value->getInventory();
			if (!$inven == null){
				$item = $inven->getItemInHand();
				$str .= TextFormat::GREEN."  手持".$item->getId().":".$item->getDamage();
			} else {
				$str .= TextFormat::GREEN."  手持0:0";
			}
			
			$str .= TextFormat::GOLD."  ";
			//$str .= TextFormat::GOLD."  ";


			if (PocketVIP::getInstance()->isSVIP($value->getName())){
				$str .= "超级至尊";
			} elseif (PocketVIP::getInstance()->isVIP($value->getName())){
				$str .= "至尊";
			} else {
				if ($value->isOp()){
					$str .= "至尊主宰";
				} else {
					$str .= "仙人";
				}
			}

			$str .= TextFormat::WHITE."  神石:";

			$str .= $this->plugin->getMI($value);
			
			$str .= "\n";
			///////////////////////////
			//$str .= "等级：".TextFormat::YELLOW.$grade."\n";
			if ($exp >= $this->plugin->getMaxEXP($value)){
				//$this->plugin->setEXP($value, $exp - $this->plugin->getMaxEXP($value), false);
				$this->plugin->setEXP($value, $exp - $this->plugin->getMaxEXP($value), true);
				$this->plugin->addGrade($value, 1);
				
				$str .= TextFormat::GRAY."      渡劫中,仙元条不显示.请稍后...";
				$value->sendTip($str);
				continue;
			} else {
				if ($exp < 0){
					$exp = 0;
					$this->plugin->setEXP($value, 0, false);
				}
		
				$show = (int)$this->plugin->getShowEXP($exp, $this->plugin->getMaxEXP($value));
				for ($i=0; $i < $show; $i++) {
					$str = $str.TextFormat::GREEN."▃";
				}
				
				for ($i=0; $i < (20 - $show); $i++) {    //$i < 
					$str = $str.TextFormat::GRAY."▃";
				}
			}

			
			///////////////////////////
			
			//$value->sendPopup($str);
			$value->sendTip($str);
		}
		/*
		foreach ($this->plugin->needUpdate as $key => $value) {
			$this->plugin->addGrade($value,1);
			$this->plugin->setEXP($value, $this->plugin->getEXP($value) - $this->plugin->getMaxEXP($value), false);
			unset($this->plugin->needUpdate[$key]);
			break;
		}
		*/
	}
}
