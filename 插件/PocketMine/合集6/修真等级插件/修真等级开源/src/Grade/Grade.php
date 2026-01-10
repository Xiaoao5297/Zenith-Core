<?php

namespace Grade;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

use pocketmine\item\Item;
use pocketmine\utils\Config;

use Grade\TipSender;
use PocketVIP\PocketVIP;
use Grade\Titles;
use onebone\economyapi\EconomyAPI;

//event
use pocketmine\event\block\BlockBreakEvent as BreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;  //被某玩家杀死
use pocketmine\event\entity\EntityDamageEvent;

use pocketmine\event\player\PlayerChatEvent; //聊天
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerKickEvent;
class Grade extends PluginBase implements Listener{
	private $exp = [
		"PlayerName" => [
			"exp" => 0,
			"grade" => 0,
			"maxHealth" => 20,
			"MI" => 0,
			"fix" => "",
		],
	];

	private $initialConfig = [
		"InitialEXP" => 1,
		"InitialGrade" => 1,
		"Title" => 1,
		"Kill_AddEXP" => 0, //杀人得仙元
	];

	private $config=[];
	public $path;

	/**
	 * 获取目标玩家的仙元值
	 * @param  Player/string $player
	 * @return int EXP
	 */
	public function getEXP($player){
		if ($player instanceof Player){
			$player = strtolower($player->getName());
		}

		//$player = strtolower($player);

		if (isset($this->exp[$player]["exp"]) == false){
			$this->createPlayer($player);
		}

		return $this->exp[$player]["exp"];
	}

	/**
	 * 设置目标玩家的仙元值
	 * @param  Player/string $player
	 * @param  int EXP
	 * @param  bool $disposeUpdate 处理升级
	 * @return int EXP
	 */
	public function setEXP($player, $exp, $disposeUpdate = true){
		if ($player instanceof Player){
			$player = strtolower($player->getName());
		}

		//$player = strtolower($player);

		if (isset($this->exp[$player]["exp"]) == false){
			$this->createPlayer($player);
		}

		$this->exp[$player]["exp"] = $exp;
		if ($disposeUpdate){
			/*
			while ($this->exp[$player]["exp"] >= $this->getMaxEXP($player)){  //仙元值满了
				//升级
				//$this->needUpdate[] = $player;
				
				$this->addGrade($player,1);
				$this->setEXP($player,$this->exp[$player]["exp"] - $this->getMaxEXP($player));
			}
			*/
		}
		
		return $exp;
	}


	/**
	 * 获取目标玩家的神石
	 * @param  Player/string $player
	 * @return int MI
	 */
	public function getMI($player){
		if ($player instanceof Player){
			$player = strtolower($player->getName());
		}

		//$player = strtolower($player);

		if (isset($this->exp[$player]["MI"]) == false){
			$this->createPlayer($player);
		}

		return $this->exp[$player]["MI"];
	}

	/**
	 * 设置目标玩家的神石
	 * @param  Player/string $player
	 * @param  int MI
	 * @return int MI
	 */
	public function setMI($player, $MI){
		if ($player instanceof Player){
			$player = strtolower($player->getName());
		}

		//$player = strtolower($player);

		if (isset($this->exp[$player]["MI"]) == false){
			$this->createPlayer($player);
		}

		$this->exp[$player]["MI"] = $MI;
		return $MI;
	}

	/**
	 * 增加目标玩家的神石
	 * @param  Player/string $player
	 * @param  int $MI
	 * @return int MI
	 */
	public function addMI($player, $MI){
		if ($player instanceof Player){
			$player = strtolower($player->getName());
		}

		//$player = strtolower($player);

		if (isset($this->exp[$player]["MI"]) == false){
			$this->createPlayer($player);
		}

		$this->exp[$player]["MI"] += (int)$MI;
		return $this->exp[$player]["MI"];
	}

	/**
	 * 获取目标玩家的最大生命值
	 * @param  Player/string $player
	 * @return int Health
	 */
	public function getMaxHealth($player){
		if ($player instanceof Player){
			$player = strtolower($player->getName());
		}

		//$player = strtolower($player);

		if (isset($this->exp[$player]["maxHealth"]) == false){
			$this->createPlayer($player);
		}

		return $this->exp[$player]["maxHealth"];
	}

	/**
	 * 设置目标玩家的最大生命值
	 * @param  Player/string $player
	 * @param  int MaxHealth
	 * @return int MaxHealth
	 */
	public function setMaxHealth($player, $maxHealth){
		if ($player instanceof Player){
			$player = strtolower($player->getName());
		}

		//$player = strtolower($player);

		if (isset($this->exp[$player]["maxHealth"]) == false){
			$this->createPlayer($player);
		}

		$this->exp[$player]["maxHealth"] = $maxHealth;
		return $maxHealth;
	}

	/**
	 * 设置目标玩家的仙阶
	 * @param  Player/string $player
	 * @param  int Grade
	 * @return int Grade
	 */
	public function setGrade($player, $grade){
		if ($player instanceof Player){
			$player = strtolower($player->getName());
		}

		//$player = strtolower($player);

		if (isset($this->exp[$player]["grade"]) == false){
			$this->createPlayer($player);
		}

		$this->exp[$player]["grade"] = $grade;
		$this->exp[$player]["maxHealth"] = 20 + $grade - 1;
		$p = $this->getPlayer($player);
		if ($p != null){
			$this->player_setMaxHealth($p);
			$p->setHealth($p->getMaxHealth());
		}

		$this->setMaxHealth($player, 20+(($grade-1)*2));
		return $grade;
	}

	/**
	 * 从在线列表中获取玩家
	 * @param  string $name
	 * @return Player
	 */
	public function getPlayer($name){
		$name = strtolower($name);
		foreach ($this->getServer()->getOnlinePlayers() as $key => $value) {
			if ($name == strtolower($value->getName())){
				return $value;
			}
		}

		return null;
	}

	/**
	 * 增加目标玩家的仙元值
	 * @param  Player/string $player
	 * @param  int $exp
	 * @return int EXP
	 */
	public function addEXP($player2, $exp){
		if ($player2 instanceof Player){
			$player = strtolower($player2->getName());
		} else {
			$player = $player2;
		}

		//$player = strtolower($player);

		if (isset($this->exp[$player]["exp"]) == false){
			$this->createPlayer($player);
		}

		$this->exp[$player]["exp"] += (int)$exp;
		while ($this->exp[$player]["exp"] >= $this->getMaxEXP($player)){  //仙元值满了
			//升级
			

			/*
			$this->setEXP($player,$this->exp[$player]["exp"] - $this->getMaxEXP($player));
			$this->addGrade($player,1);
			if ($player2 instanceof Player){
				$this->player_setMaxHealth($player2);
			}
			*/
			$this->setMaxHealth($player, 20+(2*($this->getGrade($player)-1)));
			$this->addGrade($player, 1, false);
			$this->setEXP($player,$this->exp[$player]["exp"] - $this->getMaxEXP($player));
			$this->updatePlayer($player2);
		}

		return $this->exp[$player]["exp"];
	}

	/**
	 * 玩家升级后调用
	 * @param  Player/string $player
	 * @param  bool $hide 隐藏提示
	 * @return bool
	 */
	public function updatePlayer($player2, $hide = false){ //升级	
		if ($player2 instanceof Player){
			$player = strtolower($player2->getName());
		} else {
			$player = $player2;
			//$player = strtolower($player2);
		}

		$this->addMI($player,100);
		if (!$hide){
			$this->getServer()->broadcastMessage(TextFormat::ITALIC.TextFormat::GREEN."[{$player2->getDisplayName()}]讲过漫无止境的修真,渡劫到 Lv.{$this->getGrade($player)} !!"); //倾斜.绿色
			$this->getServer()->broadcastMessage(TextFormat::ITALIC.TextFormat::GRAY."获得 1 格血量上限"); //倾斜.绿色
		}

		if ($player2 instanceof Player){
			//$player2->setNameTag(TextFormat::AQUA."Lv.".$this->getGrade($player)." ".$this->getTitle($player).TextFormat::GOLD." ".$player2->getDisplayName());
			$player2->setMaxHealth($this->getMaxHealth($player));
			$player2->setHealth($this->getMaxHealth($player));
			$player2->setNameTag(TextFormat::AQUA."Lv.".$this->getGrade($player)." ".$this->getTitle($player).TextFormat::GOLD." ".$player2->getDisplayName());
		}

		return true;
	}

	/**
	 * 增加目标玩家的仙阶(该函数不设置玩家仙元)
	 * @param  Player/string $player
	 * @param  int $grade
	 * @return int Grade
	 */
	public function addGrade($player, $grade, $disposeUpdate = true){
		if ($player instanceof Player){
			$player = $player->getName();
		}

		$player = strtolower($player);

		if (isset($this->exp[$player]["grade"]) == false){
			$this->createPlayer($player);
		}

		$this->exp[$player]["grade"] += (int)$grade;
		//$player->setNameTag(TextFormat::AQUA."Lv.".$this->getGrade($player)." ".$this->getTitle($player).TextFormat::GOLD." ".$player->getDisplayName());
		$this->setMaxHealth($player, 20+(2*($this->getGrade($player)-1)));
		
		if($disposeUpdate){
			for ($i=0; $i < $grade; $i++) {
				$this->updatePlayer($player, true);
			}
		}
		
		return $this->exp[$player]["grade"];
	}

	/**
	 * 获取目标玩家当前仙阶最大仙元值
	 * @param  Player/string $player
	 * @return int EXP
	 */
	public function getMaxEXP($player){
		if ($player instanceof Player){
			$player = strtolower($player->getName());
		}

		$grade = $this->getGrade($player);

		return ((int)(
			3*($grade-1)+10
		));
	}

	/**
	 * 获取头衔
	 * @param  Player/int $grade
	 * @return string Tile
	 */
	public function getTitle($grade){
		if ($grade instanceof Player){
			$grade = $this->getGrade($grade);
		}

		$t = Titles::$Titles[$this->config["Title"]];
		$last = 0;
		$l = $t[0];
		foreach ($t as $key => $value) {
			if ($grade >= $last and $grade <= $key){
				return $l;
			}

			$last = $key;
			$l = $value;
		}
	
		return $l;
	}

	/**
	 * 取显示的仙元格数
	 * @param  int $exp
	 * @param  int $max
	 * @return int (<= 20)
	 */
	public function getShowEXP($exp, $max){
		if ($max <= 20){

			return ($max*($exp/$max));
		}

		$a = $max/20;
		return ((int)$exp/$a);
	}
	/**
	 * 获取目标玩家的仙阶
	 * @param  Player/string $player
	 * @return int Grade
	 */
	public function getGrade($player){
		if ($player instanceof Player){
			$player = strtolower($player->getName());
		}

		$player = strtolower($player);

		if (isset($this->exp[$player]["grade"]) == false){
			$this->createPlayer($player);
		}

		return $this->exp[$player]["grade"];
	}

	/**
	 * 为玩家创建资料
	 * @param  Player/string $player
	 * @return true
	 */
	public function createPlayer($player){
		if ($player instanceof Player){
			$player = strtolower($player->getName());
		}

		$player = strtolower($player);

		$this->exp[$player] = [
			"exp" => $this->config["InitialEXP"],
			"grade" => $this->config["InitialGrade"],
			"maxHealth" => 20,
			"MI" => 0, //神石
			"fix" => "",
		];

		return true;
	}

	/**
	 * 自动根据config设置玩家最大血量
	 * @param Player $player
	 * @return null
	 */
	public function player_setMaxHealth(Player $player){

		$player->setMaxHealth($this->getMaxHealth($player));
	}

	/**
	 * 用于玩家挖掉矿物后.获取需要增加的仙元值..如56(钻石块)返回 6
	 * @param  int/string $id
	 * @return int EXP
	 */
	public function getEXPByID($id){
		switch ($id) {
			case 56: //钻石矿
			return 6;
			
			case 15: //铁矿
			return 3;

			case 14: //金矿
			return 4;

			case 16: //煤矿
			return 2;

			case 4: //石头
			return 1;
			
			case "59:3": //成熟小麦
			case "141:3": //成熟胡萝卜
			case "142:3": //成熟土豆
			return 1;

			default:
			return 0;
		}
	}

	public function disposeConfig(){
		foreach ($this->initialConfig as $key => $value) {
			if (isset($this->config[$key]) == false){
				$this->config[$key] = $value;
			}
		}
	}

	public function save(){
		$c = new Config($this->path."exp.yml",Config::YAML,[]);
		$c->setAll($this->exp);
		$c->save();

		/*
		$c = new Config($this->path."config.yml",Config::YAML,$this->initialConfig);
		$c->setAll($this->config);
		$c->save();
		*/
	}

	/**
	 * 用神石兑换物品
	 * @param  Player $player   玩家
	 * @param  int $id          兑换物品ID
	 * @param  int $count       数量
	 * @return bool
	 */
	public function convert(Player $player, $id, $count){
		$name = strtolower($player->getName());
		$id = (int)$id;
		$count = (int)$count;

		if ($count <= 0){
			$player->sendMessage(TextFormat::AQUA."数量不可以小于0 !!");
			return false;
		}

		$inventory = $player->getInventory();
		if ($inventory == null){
			$player->sendMessage(TextFormat::AQUA."兑换失败 !!");
			return false;
		}

		$MI = $this->getMI($player);
		//get($id, $meta = 0, $count = 1)
		switch($id){
			case 1:
				//钻石
				$num = 1000*$count;
				if ($num > $MI){
					$player->sendMessage(TextFormat::AQUA."{$count}颗钻石需要{$num}神石,你只有{$MI}神石");
					return false;
				}
				
				$item = Item::get(Item::DIAMOND, 0, $count);
			break;

			case 2:
				//仙石
				$num = $count;
				if ($num > $MI){
					$player->sendMessage(TextFormat::AQUA."{$count}元仙石需要{$num}神石,你只有{$MI}神石");
					return false;
				}

				EconomyAPI::getInstance()->addMoney($player, $count);
				$player->sendMessage(TextFormat::AQUA."兑换成功 !!");
				$this->addMI($player,-1*$num);
				return true;
			break;

			case 3:
				//铁块
				$num = 500*$count;
				if ($num > $MI){
					$player->sendMessage(TextFormat::AQUA."{$count}块铁块需要{$num}神石,你只有{$MI}神石");
					return false;
				}

				$item = Item::get(Item::IRON_BLOCK, 0, $count);
			break;

			case 4:
				//金块
				$num = 800*$count;
				if ($num > $MI){
					$player->sendMessage(TextFormat::AQUA."{$count}块金块需要{$num}神石,你只有{$MI}神石");
					return false;
				}

				$item = Item::get(Item::GOLD_BLOCK, 0, $count);
			break;

			case 5:
				//仙元值
				$num = 10*$count;
				if ($num > $MI){
					$player->sendMessage(TextFormat::AQUA."{$count}点仙元值需要{$num}神石,你只有{$MI}神石");
					return false;
				}

				$this->addEXP($player, $count);
				$this->addMI($player,-1*$num);
				$player->sendMessage(TextFormat::AQUA."兑换成功 !!");
				return true;
			break;

			default:
				$player->sendMessage(TextFormat::AQUA."无效的物品ID !!请使用以下ID.");
				$player->sendMessage(TextFormat::YELLOW.
					"1.钻石(1000神石/颗); ".
					"2.仙石(1神石/1经济单位); ".
					"3.铁块(500神石/块); ".
					"4.金块(800神石/块); ".
					"5.仙元值(10神石/点)"
				);
			break;
		}

		if($inventory->canAddItem($item) == false){
			$player->sendMessage(TextFormat::RED."兑换失败!背包已满!");
			return false;
		}

		$this->addMI($player,-1*$num);
		$inventory->addItem($item);
		$player->sendMessage(TextFormat::AQUA."兑换成功 !!");
		return true;
	}

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new TipSender($this), 15);
		$this->path = $this->getDataFolder();
		@mkdir($this->path);
		$this->exp = (new Config($this->path."exp.yml",Config::YAML,[]))->getAll();
		$this->config = (new Config($this->path."config.yml",Config::YAML,$this->initialConfig))->getAll();
		$this->disposeConfig();
		$this->save();

		/*
		if ($this->getServer()->getPluginManager()->getPlugin("PocketVIP") === \null){
			$this->vip = null;
		} else {
			$this->vip = true;
		}
		*/
	
		$this->getLogger()->info(TextFormat::AQUA . "Enabled !!");
	}

	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		$name = strtolower($sender->getName());

		switch($command->getName()){
			case "exp":
				if($sender->isOp() == false){
					return false;
				}

				if (isset($args[0]) == false){
					$sender->sendMessage(TextFormat::RED."使用/exp <setgrade | setexp | setmi>");
					return true;
				}

				switch ($args[0]) {
					case "setgrade":
						if (isset($args[1]) == false or isset($args[2]) == false){
							$sender->sendMessage(TextFormat::RED."使用/exp setgrade <目标仙人> <仙阶>");
							return true;
						}

						$this->setGrade($args[1], (int)$args[2]);
						$sender->sendMessage(TextFormat::AQUA."设置成功!");
					return true;
		
					case "setexp":
						if (isset($args[1]) == false or isset($args[2]) == false){
							$sender->sendMessage(TextFormat::RED."使用/exp setexp <目标仙人> <仙元>");
							return true;
						}

						$this->setEXP($args[1], (int)$args[2]);
						//$this->updatePlayer($args[1]);
						$sender->sendMessage(TextFormat::AQUA."设置成功!");
					return true;

					case "setmi":
						if (isset($args[1]) == false or isset($args[2]) == false){
							$sender->sendMessage(TextFormat::RED."使用/exp setmi <目标仙人> <神石>");
							return true;
						}

						$this->setMI($args[1], (int)$args[2]);
						$sender->sendMessage(TextFormat::AQUA."设置成功!");
					return true;

					default:
						$sender->sendMessage(TextFormat::RED."使用/exp <setgrade | setexp>");
					return true;
				}
			return true;

			case "convert":
			case "兑换":
				if (isset($args[0]) == false or isset($args[1]) == false){
					$sender->sendMessage(TextFormat::RED."使用/convert <物品编号> <数量>");
					return true;
				}

				if (!$sender instanceof Player){
					//控制台
					$sender->sendMessage(TextFormat::RED."请在仙界内使用");
					return true;
				}

				$this->convert($sender, (int)$args[0], (int)$args[1]);
			return true;

			case "setfix":
				if ($sender->isOp() == false){
					return false;
				}

				if (isset($args[0]) == false or isset($args[1]) == false){
					$sender->sendMessage(TextFormat::RED."使用/setfix <目标仙人> [昵称]");
					return true;
				}

				$player = $this->getServer()->getPlayer($args[0]);
				if ($player instanceof Player){
					$player->setDisplayName($args[1]);
					$this->exp[strtolower($player->getName())]["fix"] = $args[1];
					$player->setNameTag(TextFormat::AQUA."仙阶.".$this->getGrade($player)." ".$this->getTitle($player).TextFormat::GOLD." ".$player->getDisplayName());
					$sender->sendMessage(TextFormat::AQUA."设置成功 !!");
				} else {
					$sender->sendMessage(TextFormat::RED."该仙人不存在");
				}
			return true;

			default:
			return false;
		}
	}

	public function onDisable(){

		$this->save();
	}

	public function onChat(PlayerChatEvent $event){ 
		$event->setCancelled(true);

		$player = $event->getPlayer();
		$level = $player->getLevel()->getFolderName();
		$grade = $this->getGrade($player);
		$name = $player->getDisplayName();
		$title = $this->getTitle($grade);
		$msg = TextFormat::GOLD."<{$level}>".TextFormat::GREEN."[仙阶.{$grade} {$title}]".TextFormat::YELLOW." {$name}: ".TextFormat::AQUA.$event->getMessage();
		$this->getServer()->broadcastMessage($msg);
	}

	public function onBreak(BreakEvent $event){
		if ($event->isCancelled()){
			return;
		}

		$player = $event->getPlayer();
		if ($player->getgamemode() == 1){ //创造
			return;
		}

		$block = $event->getBlock();
		//if ($this->vip === true){
			if (PocketVIP::getInstance()->isAProtectiveBlock($block->getX(), $block->getY(), $block->getZ(), $block->getLevel()->getFolderName(), $block->getId())){
				//是被保护的方块
				return;
			}
		//}

		
		//$this->addEXP($player,$this->config["BreakBlock_AddEXP"]);
		$id = $block->getId();
		if ($block->getDamage() != 0){
			$id = (string)$id.$block->getDamage();
		}

		$this->addEXP($player,$this->getEXPByID($id));
	}

	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		if (isset($this->exp[strtolower($player->getName())]["fix"])){
			if ($this->exp[strtolower($player->getName())]["fix"] !== ""){
				$player->setDisplayName($this->exp[strtolower($player->getName())]["fix"]);
			}
		}

		$event->setJoinMessage(TextFormat::YELLOW."传说中的".TextFormat::AQUA." 仙阶.".$this->getGrade($player)." ".$this->getTitle($player).TextFormat::GOLD." ".$player->getDisplayName().TextFormat::YELLOW." 来到了仙界");
		$this->player_setMaxHealth($event->getPlayer());
		$player->setNameTag(TextFormat::AQUA."Lv.".$this->getGrade($player)." ".$this->getTitle($player).TextFormat::GOLD." ".$player->getDisplayName());
		//$event->getPlayer()->setMaxHealth($this->getMaxHealth($event->getPlayer()));
	}

	public function onRespawn(PlayerRespawnEvent $event){

		$this->player_setMaxHealth($event->getPlayer());
		//$event->getPlayer()->setHealth($event->getPlayer()->getMaxHealth());
		//$event->getPlayer()->setMaxHealth($this->getMaxHealth($event->getPlayer()));
	}

	public function onQuit(PlayerQuitEvent $event){
		$player = $event->getPlayer();
		$event->setQuitMessage(TextFormat::YELLOW."来自传说中的".TextFormat::AQUA." 仙阶.".$this->getGrade($player)." ".$this->getTitle($player).TextFormat::GOLD." ".$player->getDisplayName().TextFormat::YELLOW." 离开了仙界");
	}

	public function onKick(PlayerKickEvent $event){
		$player = $event->getPlayer();
		$event->setQuitMessage(TextFormat::AQUA." 仙阶.".$this->getGrade($player)." ".$this->getTitle($player).TextFormat::GOLD." ".$player->getDisplayName().TextFormat::YELLOW." 被至尊主宰击杀！");
	}

	public function onDamage(EntityDamageEvent $event){
		if (!$event instanceof EntityDamageByEntityEvent){
			return;
		}

		if ($event->isCancelled()){
			return;
		}

		if ($this->config["Kill_AddEXP"] == 0){
			return;
		}

		$player = $event->getEntity();
		$killer = $event->getDamager();

		if($player->getHealth() - $event->getDamage() <= 0){
			//被打的死了
			$this->addEXP($killer, $this->config["Kill_AddEXP"]); //0.1*$this->getMaxEXP($killer)
		}
	}
}

?>