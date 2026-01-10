<?php
namespace Randomspawn;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat as MT;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\level\Position;

 class RandomSpawn extends PluginBase implements Listener {
	 
	 public function onEnable(){// (^_^) 
       $this->getLogger()->info(MT::LIGHT_PURPLE."[随机出生]加载成功");
	   $this->getServer()->getPluginManager()->registerEvents($this, $this);
	   $this->path = $this->getDataFolder();
	   @mkdir($this->path);
       $conf = new Config($this->path."Config.yml",Config::YAML,array(
	   "Spawn1"=>array("X"=>0,"Y"=>0,"Z"=>0),
	   "Spawn2"=>array("X"=>0,"Y"=>0,"Z"=>0),
	   "Spawn3"=>array("X"=>0,"Y"=>0,"Z"=>0),
	   "Spawn4"=>array("X"=>0,"Y"=>0,"Z"=>0)	   
	   ));
     }
    public function OnJoin(PlayerJoinEvent $event) {
		$player = $event->getPlayer();
		$conf = new Config($this->path."Config.yml",Config::YAML,array());
		$spawn1 = $conf->get("Spawn1");
		if($spawn1["X"] == 0 AND $player->isOp() == true){$player->sendMessage("随机出生点未设置QWQ\n请输入 /setspawn1 设置第一个出生点");}
		else{
			$num = mt_rand(1,4);
			$spawn{$num} = $conf->get("Spawn{$num}");
		    if($spawn{$num}["X"] !== 0){
			$player->teleport(new Vector3($spawn{$num}["X"],$spawn{$num}["Y"],$spawn{$num}["Z"]));
			$player->sendPopup("§a *出生在[{$num}]号出生点");}
			else{$player->sendMessage("未进行随机出生点设置,使用默认出生点");}
		}
		}
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		switch($cmd->getName()){
			case "setspawn1":
			$this->hehe(1,$sender);
			break;
		    case "setspawn2":
			$this->hehe(2,$sender);
			break;
			case "setspawn3":
			$this->hehe(3,$sender);
			break;
			case "setspawn4":
            $this->hehe(4,$sender);	
            return true;			
	    }	 
	}
   public function fuck(PlayerRespawnEvent $event) {
		$player = $event->getPlayer();
		$conf = new Config($this->path."Config.yml",Config::YAML,array());
		$spawn1 = $conf->get("Spawn1");
		if($spawn1["X"] == 0 AND $player->isOp() == true){$player->sendMessage("随机出生点未设置QWQ\n请输入 /setspawn1 设置第一个出生点");}
		else{
			$num = mt_rand(1,4);
			$spawn{$num} = $conf->get("Spawn{$num}");
		    if($spawn{$num}["X"] !== 0){
			$event->setRespawnPosition(new Position($spawn{$num}["X"],$spawn{$num}["Y"],$spawn{$num}["Z"],$this->getServer()->getDefaultLevel()));
			$player->sendPopup("§a *出生在[{$num}]号出生点");}
			else{$player->sendMessage("未进行随机出生点设置,使用默认出生点");}
		}
		}
	public function hehe($num,$sender){
		        $conf = new Config($this->path."Config.yml",Config::YAML,array());
	            $spawn{$num} = $conf->get("Spawn{$num}");//获取数据
				$spawn{$num}["X"] = $sender->getX();
				$spawn{$num}["Y"] = $sender->getY();
				$spawn{$num}["Z"] = $sender->getZ();//设置玩家当前坐标为出生点$num
				$conf->set("Spawn{$num}",$spawn{$num});$conf->save();
				$sender->sendMessage("第{$num}个出生点设置成功 ！");
				return true;
}
	 
 }	 