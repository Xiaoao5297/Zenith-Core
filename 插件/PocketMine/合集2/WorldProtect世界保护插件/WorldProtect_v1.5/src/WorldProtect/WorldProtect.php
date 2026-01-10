<?php
namespace WorldProtect;

use pocketmine\utils\Config;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;

class WorldProtect extends PluginBase implements Listener{
	
	private $path,$conf;
	
	public function onEnable(){ 
		$this->path = $this->getDataFolder();
		@mkdir($this->path);
		$this->conf = new Config($this->path."Config.yml", Config::YAML,array(
				"protect-world"=>array(),
				"protect-world-admin"=>array(),
				"msg"=>"§c[世界保护] 对不起,这个世界被保护了!",
				"item-touch"=>array(259,325,351,291,292,293,294)
				));
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->getLogger()->info("世界保护插件加载成功!作者：开心点~ 本插件由极致·人生优化");
	}
	
	//public function itemheld(PlayerItemHeldEvent  $event){
	//    $this->permission($event);
	//}
	public function playerblockBreak(BlockBreakEvent $event) {
	    $this->permission($event);
	}
	
	public function playerinteract(PlayerInteractEvent  $event){
		$player = $event->getPlayer();
	  $user = $player->getName();
	  $itemid = $event->getItem()->getID();
	  $itemtouch = $this->conf->get("item-touch");
	  if(in_array($itemid,$itemtouch)){
	  	$this->permission($event);
	  }
	}
	
	public function playerblockPlace(BlockPlaceEvent  $event){
	    $this->permission($event);
	}	
	
	public function permission($event){
	  $player = $event->getPlayer();
	  $user = $player->getName();
		$level = $player->getLevel()->getName();
		$protect_world = $this->conf->get("protect-world");
		$admin = $this->conf->get("protect-world-admin");
		$msg = $this->conf->get("msg");
	    if((in_array($level,$protect_world)) and (!in_array($user,$admin))){
		$player->sendMessage($msg);
		$event->setCancelled(true);
		}
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		$user = $sender->getName();
		switch($command->getName()){
			case "wp":
				if(isset($args[0])){
					switch($args[0]){
					case "world":
						if(isset($args[1])){
								$protect_world = $this->conf->get("protect-world");
								if(!in_array($args[1],$protect_world)){
									$protect_world[]=$args[1];
									$this->conf->set("protect-world",$protect_world);
									$this->conf->save();
									$sender->sendMessage("§a[世界保护] 成功保护世界：$args[1] ");
								}else{
									$inv = array_search($args[1], $protect_world);
									$inv = array_splice($protect_world, $inv, 1); 
									$this->conf->set("protect-world",$protect_world);
									$this->conf->save();
									$sender->sendMessage("§a[世界保护] 成功取消保护世界: $args[1] ");
								}
						}else{
							$sender->sendMessage("§a[世界保护] 用法: /wp world [世界名字] ");
						}
						return true;
					case "admin":
						if(isset($args[1])){
							$protect_world_admin = $this->conf->get("protect-world-admin");
							if(!in_array($args[1],$protect_world_admin)){
								$protect_world_admin[] = $args[1];
								$this->conf->set("protect-world-admin",$protect_world_admin);
								$this->conf->save();
								$sender->sendMessage("§a[世界保护] 成功添加管理员: $args[1] ");
							}else{
								$inv = array_search($args[1], $protect_world_admin);
								$inv = array_splice($protect_world_admin, $inv, 1); 
								$this->conf->set("protect-world-admin",$protect_world_admin);
								$this->conf->save();
								$sender->sendMessage("§a[世界保护] 成功删除管理员: $args[1] ");
							}
						}else{
							$sender->sendMessage("§a[世界保护] 用法: /wp admin [玩家名称] ");
						}
						return true;
					case "list":
					  $protect_world = $this->conf->get("protect-world");
						$protect_world_admin = $this->conf->get("protect-world-admin");					
					  $protectworld="§a世界保护列表: ";
						$protectworldadmin="§a管理员列表: ";
						$protectworld .=implode(",",$protect_world);
						$protectworldadmin .=implode(",",$protect_world_admin);
						$sender->sendMessage("§a[世界保护] $protectworld");
						$sender->sendMessage("§a[世界保护] $protectworldadmin");
					  return true;
					}
					
				
				}		
		}
	}
}