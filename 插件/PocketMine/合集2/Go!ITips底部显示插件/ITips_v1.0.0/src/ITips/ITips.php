<?php

namespace ITips;


use onebone\economyapi\EconomyAPI;
use pocketmine\inventory\Inventory;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use ITips\ITipsTask;

use ICurrency\ICurrency;

class ITips extends PluginBase{
    protected $ICurrency;
    public function onEnable(){
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new ITipsTask($this), 20);
		$this->getLogger()->info(TextFormat::GREEN . 'ITips v1.0.2 For API3.0.0+ 已安全启动');
		$this->getLogger()->info(TextFormat::GREEN . 'All by ibook');
		$this->getLogger()->info(TextFormat::GREEN . '欢迎加群为我们提供创意和线索! Q群 546488737 ');
        $this->ICurrency = $this->getServer()->getPluginManager()->getPlugin('ICurrency');
    }
    public function tip(){
        date_default_timezone_set('Asia/Chongqing');
        foreach($this->getServer()->getOnlinePlayers() as $player){
            if($player->isOnline()){
	            $name=$player->getName();
                $m = EconomyAPI::getInstance()->myMoney($player->getName());
                $item = $player->getInventory()->getItemInHand();
                $id = $item->getID();
                $ts = $item->getDamage();
                if($player->isOp()) {
                    $quanxian = "[OP]";
                }else{
                    $quanxian = "[human]";
                }
	            $gold = $this->ICurrency->getCoin($name);
	            switch($id){
   	                case 0:
	                    $message ="       §f| §6{$this->ICurrency->config['货币名称']}: {$gold}枚  §f银币: {$m}枚  \n";
   	                    $message.="       §f| §b职位: $quanxian  §2时间".date("H")."点".date("i")."分".date("s")."秒\n";
                        break;
	                default:
	                    $message="§7手持: {$id}: {$ts} ";
                        break;	  
	            }
          	    $player->sendPopup($message);      
            }
        }
    } 
}
