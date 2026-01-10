<?php

namespace HeadHunting;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use onebone\economyapi\EconomyAPI;

class HeadHunting extends PluginBase implements Listener{
	
	private $hunts;
	
	private $hunters;
	
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TextFormat::GREEN . "HeadHunting_v1.0.5 by 超级戴尔");
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder() . "data/");
		$huntyml = new Config($this->getDataFolder() . "data/" . "hunts.yml",Config::YAML);
		$huntersyml = new Config($this->getDataFolder() . "data/" . "hunters.yml",Config::YAML);
		$this->hunts = $huntyml->getAll();
		$this->hunters = $huntersyml->getAll();
        $showhuntyml = new Config($this->getDataFolder() . "data/" . "showhunt.yml",Config::YAML);
		$cfg = new Config($this->getDataFolder() . "config.yml",Config::YAML,array(
			"最低悬赏金额" => 100,
            "悬赏者违约金" => 100,
			"只有杀手可以杀人" => false,
			"杀手每次完成任务后增加的信誉值" => 10,
		));
    }
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
        $huntyml = new Config($this->getDataFolder() . "data/" . "hunts.yml",Config::YAML);
        $huntersyml = new Config($this->getDataFolder() . "data/" . "hunters.yml",Config::YAML);
		switch($command->getName()){
			case "hunt":
                $mymoney = EconomyAPI::getInstance()->myMoney($sender);
                if(count($args) != 2){
                    return false;
                }
                if(!$sender instanceof Player){
                    $sender->sendMessage(TextFormat::GREEN . "你不是一个玩家！");
                    return true;
                }
                if(!is_numeric($args[1])){
                    $sender->sendMessage(TextFormat::LIGHT_PURPLE . "请用数字来填写赏金金额");
                    return true;
                }
                if ($mymoney < $args[1]) {
                	$sender->sendMessage(TextFormat::GOLD . "你钱包里没有那么多钱！");
                	return true;
                }
                if($args[1] < $this->getCfg("最低悬赏金额")){
                    $sender->sendMessage(TextFormat::GREEN . "你的赏金太少了！");
                    return true;
                }
                if(isset($this->hunts[$args[0]])){
                    $sender->sendMessage(TextFormat::BLUE . "他正在被人悬赏中");
                    return true;
                }
                $this->hunts[$args[0]] = array(
                    "hunt-by" => $sender->getName(),
                    "money" => $args[1]
                );
                $huntyml->setAll($this->hunts);
                $huntyml->save();
                EconomyAPI::getInstance()->setMoney($sender,$mymoney - $args[1]);
                $sender->sendMessage(TextFormat::GREEN . "成功发布悬赏，抽取赏金: " . $args[1]);
                foreach ($this->getServer()->getOnlinePlayers() as $p) {
                	$p->sendMessage(TextFormat::GOLD . $sender->getName() . TextFormat::YELLOW . " 发布了一则悬赏！");
                	$p->sendMessage(TextFormat::BLUE . "赏金为: " . TextFormat::YELLOW . $args[1] . TextFormat::BLUE . "元");
                }
                unset($mymoney,$p);
                break;
            case "rm":
                $smoney = EconomyAPI::getInstance()->myMoney($sender);
                if($sender instanceof Player){
                    if(isset($args[0])){
                        if(isset($this->hunts[$args[0]])){
                            if($this->hunts[$args[0]]["hunt-by"] == $sender->getName()){
                                if($smoney < $this->getCfg("悬赏者违约金")){
                                    $sender->sendMessage(TextFormat::GREEN . "违约金不足，无法取消！");
                                    return true;
                                }
                                if (isset($this->hunts[$args[0]]["hunter"])) {
                                	$this->hunters[$this->hunts[$args[0]]["hunt-by"]]["mission"] = null;
                                	$this->saveHunters();
                                	$this->getServer()->getPlayer($this->hunts[$args[0]]["hunt-by"])->sendMessage(TextFormat::RED . "你的雇主已经取消了关于 " . TextFormat::GOLD . $args[0] . TextFormat::RED . " 的悬赏，但是你获得了 " . $this->getCfg("悬赏者违约金") . " 元违约金");
                                }
                                $this->hunts[$args[0]] = null;
                                unset($this->hunts[$args[0]]);
                                $huntyml->setAll($this->hunts);
                                $huntyml->save();
                                $sender->sendMessage(TextFormat::GOLD . "成功取消悬赏~,被抽取违约金 " . $this->getCfg("悬赏者违约金") . " 元");
                                EconomyAPI::getInstance()->setMoney($sender,$smoney - $this->getCfg("悬赏者违约金"));
                            }
                        }else {
                            $sender->sendMessage(TextFormat::RED . "该玩家没有被通缉");
                        }
                    }
                }else {
                    $sender->sendMessage(TextFormat::BOLD . "你不是一个玩家！");
                }
            break;
            case "gethunt":
                if(isset($args[0])){
                    if($sender instanceof Player){
                        if(!isset($this->hunters[$sender->getName()])){
                            $sender->sendMessage(TextFormat::GREEN . "你不是杀手，无法领取任务");
                            return true;
                        }
                        if(!isset($this->hunts[$args[0]])){
                            $sender->sendMessage(TextFormat::BLUE . "他没有被人悬赏！");
                            return true;
                        }
                        if(isset($this->hunters[$sender->getName()]["mission"])){
                            $sender->sendMessage(TextFormat::GOLD . "你已经接受过任务了！");
                            return true;
                        }
                        if(isset($this->hunts[$args[0]]["hunter"])){
                            $sender->sendMessage(TextFormat::GREEN . "他的任务已经被人领取了！");
                            return true;
                        }
                        if ($this->hunts[$args[0]]["hunt-by"] == $sender->getName()) {
                        	$sender->sendMessage(TextFormat::LIGHT_PURPLE . "不可以领取自己发布的任务！");
                        	return true;
                        }
                        $this->hunts[$args[0]]["hunter"] = $sender->getName();
                        $huntyml->setAll($this->hunts);
                        $huntyml->save();
                        $this->hunters[$sender->getName()]["mission"] = $args[0];
                        $huntersyml->setAll($this->hunters);
                        $huntersyml->save();
                        $sender->sendMessage(TextFormat::GREEN . "成功获得任务~");
                    }else {
                        $sender->sendMessage(TextFormat::BLUE . "你不是一个玩家");
                    }
                }
            break;
            case "joinhunter":
                if($sender instanceof Player){
                    if(isset($this->hunters[$sender->getName()])){
                        $sender->sendMessage(TextFormat::GREEN . "你已经是杀手了，请勿重复申请！");
                        return true;
                    }
                    $this->hunters[$sender->getName()] = array();
                    $huntersyml->setAll($this->hunters);
                    $huntersyml->save();
                    $sender->sendMessage(TextFormat::GREEN . "注册为杀手成功~");
                }
            break;
            case "check":
                if($sender instanceof Player){
                    if(isset($args[0])){
                        if(isset($this->hunts[$args[0]])){
                            $sender->sendMessage(TextFormat::GOLD . "他正在被悬赏");
                            $sender->sendMessage(TextFormat::GREEN . "悬赏他的人: " . $this->hunts[$args[0]]["hunt-by"]);
                            $sender->sendMessage(TextFormat::YELLOW . "悬赏金额: " . $this->hunts[$args[0]]["money"]);
                            if(isset($this->hunts[$args[0]]["hunter"])){
                                $sender->sendMessage(TextFormat::LIGHT_PURPLE . "接下任务者: " . $this->hunts[$args[0]]["hunter"]);
                            }else {
                                $sender->sendMessage(TextFormat::GREEN . "未有人接下该悬赏任务");
                            }
                        }else {
                            $sender->sendMessage(TextFormat::BLUE . "他没有被追杀~");
                        }
                    }
                }
            break;
        }
		return true;
	}
    public function onDeath(PlayerDeathEvent $event){
        $huntyml = new Config($this->getDataFolder() . "data/" . "hunts.yml",Config::YAML);
        $huntersyml = new Config($this->getDataFolder() . "data/" . "hunters.yml",Config::YAML);
        $entity = $event->getEntity();
        $cause = $entity->getLastDamageCause();
        if($cause instanceof EntityDamageByEntityEvent) {
            $killer = $cause->getDamager();
            if($killer instanceof Player && $entity instanceof Player){
                if($this->getCfg("只有杀手可以杀人") == true && !isset($this->hunters[$killer->getName()])) {
                    $event->setCancelled(true);
                    $killer->sendMessage(TextFormat::GREEN . "只有 " . TextFormat::RED . "杀手" . TextFormat::GREEN . "才可以杀人！");
                }
                if(isset($this->hunters[$killer->getName()]["mission"]) and isset($this->hunts[$entity->getName()]["hunter"])){
                    if($this->hunters[$killer->getName()]["mission"] == $entity->getName() and $this->hunts[$entity->getName()]["hunter"] == $killer->getName()){
                        $killer->sendMessage(TextFormat::BLUE . "恭喜完成追杀任务！获得赏金: " . $this->hunts[$entity->getName()]["money"]);
                        EconomyAPI::getInstance()->addMoney($killer,$this->hunters[$entity->getName()]["money"]);
                        $this->hunters[$killer->getName()]["mission"] = null;
                        $huntersyml->setAll($this->hunters);
                        $huntersyml->save();
                        $this->hunts[$entity->getName()] = null;
                        $huntyml->setAll($this->hunts);
                        $huntyml->save();
                        unset($this->hunters[$killer->getName()]["mission"],$this->hunts[$entity->getName()]);
                    }
                }
            }
        }
    }
	public function getCfg($key){
		$cfg = new Config($this->getDataFolder() . "config.yml",Config::YAML,array(
				"最低悬赏金额" => 100,
				"悬赏者违约金" => 100,
				"只有杀手可以杀人" => false,
				"杀手每次完成任务后增加的信誉值" => 10,
		));
		if($cfg->exists($key)){
			return $cfg->get($key);
		}
		return false;
	}
	public function addHunt($huntname,$huntby,$money) {
		$this->hunts[$huntname] = array(
				"hunt-by" => $huntby,
				"money" => $money
		);
		$huntyml->setAll($this->hunts);
		$huntyml->save();
		return true;
	}
	public function saveHunters() {
		$huntersyml = new Config($this->getDataFolder() . "data/" . "hunters.yml",Config::YAML);
		$huntersyml->setAll($this->hunters);
		$huntersyml->save();
	}
}