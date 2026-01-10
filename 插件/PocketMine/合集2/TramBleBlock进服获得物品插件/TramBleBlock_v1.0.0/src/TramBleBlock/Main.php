<?php

namespace TramBleBlock;

use pocketmine\plugin\Plugin;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector3;
use pocketmine\event\player\PlayerJoinEvent;
class Main extends PluginBase implements Listener
{
 public function onEnable()
  {
	   $this->path = $this->getDataFolder();
	 @mkdir($this->path);
	 $this->config = new Config($this->path . "config.yml", Config::YAML, array(
	             "踩物品ID" => 152,
            "添加的物品ID" => 1,
            "添加物品数量" => 1,
            "公告"=>"§b< 欢迎来到本服务器 >",
            "进服提示" => "Welcome to the server.",
));
		$this->getLogger()->info("=======================");
		$this->getLogger()->info("本插件由SM，GET插件编");
		$this->getLogger()->info("=======================");
		
		$this->getServer()->getPluginManager()->registerEvents($this,$this);

		}
		public function onDisable()
  {
  $this->getLogger()->info("欢迎使用TrampleBlock插件");
	}

public function onMove(PlayerMoveEvent $event) {
       $player=$event->getPlayer();
        $block = $player->getLevel()->getBlock($player->floor()->subtract(0, 1));
        $dt=$player->getLevel()->getName();
        $wj=count($this->getServer()->getOnlinePlayers());
      $fd = $block->getID();
      $qq=$this->getConfig()->get("公告");    
   
       if ($fd == $this->getConfig()->get("物品ID")){
      
      $player->sendTip("§b$qq");
       }
   }
   
   
   
   public function onJoin(PlayerJoinEvent $event){
   
   $zz=$this->getConfig()->get("添加的物品ID");
   $xx=$this->getConfig()->get("添加物品数量");
   $wj=count($this->getServer()->getOnlinePlayers());
   $event->getPlayer()->getInventory()->addItem(new Item($zz,0,$xx));
   
   $zz=$this->getConfig()->get("进服提示");
   
  
   $event->getPlayer()->sendMessage("§l§b$zz,§e< $wj >");
   }
   }