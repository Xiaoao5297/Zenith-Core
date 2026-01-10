<?php
namespace SWorld;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as Font;
use pocketmine\level\position;
use pocketmine\level\Level;

class SWorld extends PluginBase implements CommandExecutor, Listener {

	public function onEnable() {
		$this->getServer()->getLogger()->info(Font::BLUE . "SWorld loaded!");
		$this->LoadAllLevels();
 	}

	public function LoadAllLevels() {
		$level = $this->getServer()->getDefaultLevel();
   		$path = $level->getFolderName();
   		//$p1 = dirname($path);
   		//$p2 = $p1."/worlds/";
        $p2 = $this->getServer()->getDataPath() . "worlds/";
   		$dirnowfile = scandir($p2, 1);
        foreach ($dirnowfile as $dirfile){
	    	if($dirfile != '.' && $dirfile != '..' && $dirfile != $path && is_dir($p2.$dirfile)) {
				if (!$this->getServer()->isLevelLoaded($dirfile)) {  //如果这个世界未加载
					$this->getLogger()->info(Font::YELLOW . "正在加载世界：$dirfile");
					$this->getServer()->generateLevel($dirfile);
					$this->getServer()->loadLevel($dirfile);
					$level = $this->getServer()->getLevelbyName($dirfile);
					if ($level->getName() != $dirfile) {  //温馨提示
						$this->getLogger()->info(Font::RED . "[温馨提示] 您加载的地图 $dirfile 的文件夹名与地图名 ".$level->getName()." 不符，可能会出现无法记录玩家位置的bug！");
					}
				}
			}
	  	}
	}
	
  	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
    	switch($cmd->getName()) {
			
			case "unload":
	 			if(isset($args[0])){
	    			$l = $args[0];
					if (!$this->getServer()->isLevelLoaded($l)) {  //如果这个世界未加载
						$sender->sendMessage(Font::RED . "§c[传送系统] 地图 $l 未被加载 , 无法卸载!");
					}
					else {
						$level = $this->getServer()->getLevelbyName($l);
						$ok = $this->getServer()->unloadLevel($level); 
						if($ok !== true){
							$sender->sendMessage(Font::RED . "§c[传送系统] 卸载地图 $l 失败!");
						}else{
							$sender->sendMessage(Font::GREEN . "§a[传送系统] 地图 $l 已被成功卸载!");
						}
					}
				}
	 		break;
	 
	 		case "load":
	 			if(isset($args[0])){
					$level = $this->getServer()->getDefaultLevel();
   					$path = $level->getFolderName();
   					//$p1 = dirname($path);
   					//$p2 = $p1."/worlds/";
                    $p2 = $this->getServer()->getDataPath() . "worlds/";
					$path = $p2;
					//$path = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "\\loadLevel\\";
					$l = $args[0];
					if ($this->getServer()->isLevelLoaded($l)) {  //如果这个世界已加载
						$sender->sendMessage(Font::RED . "§c[传送系统] 地图 ".$args[0]." 已被加载 , 无法再次加载!" );
					}
					elseif (is_dir($path.$l)){
						$sender->sendMessage(Font::YELLOW . "§a[传送系统] 正在加载地图 ".$args[0]."." );
						$this->getServer()->generateLevel($l);
						$ok = $this->getServer()->loadLevel($l);
						if ($ok === false) {
							$sender->sendMessage(Font::RED . "§c[传送系统] 地图 ".$args[0]." 加载失败!");
						}
						else {
							$sender->sendMessage(Font::GREEN . "§a[传送系统] 地图 ".$args[0]." 加载成功!");
						}
					}else{
						$sender->sendMessage(Font::RED . "§c[传送系统] 无法加载地图 ".$args[0]." , 地图文件不存在!");
					}
			 	}
	 		break;
	 
     		case "lw":
  				//$path = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "\\worlds\\";
  				//读取world文件夹
   				//$file = scandir($path);
				$levels = $this->getServer()->getLevels();
				$sender->sendMessage("==== 地图列表 ====");
       			foreach ($levels as $level){
	   				$sender->sendMessage(" - ".$level->getFolderName());
	  			}
	  		break;
	  
			case "w":
     			if ($sender instanceof Player){
					if(isset($args[0])){
	  					$l = $args[0];
	  					if ($this->getServer()->isLevelLoaded($l)) {  //如果这个世界已加载
        					//$sender->sendMessage("§a[传送系统] 正在传送...");
      						$sender->teleport(Server::getInstance()->getLevelByName($l)->getSafeSpawn());
							$sender->sendMessage("§a[传送系统] 你已被传送到世界: $l.");
            			}else{
     						$sender->sendMessage("§c[传送系统] 该世界 ".$l." 不存在!");
          				}
		  			}else{
   						$sender->sendMessage("§c[传送系统] 请输入世界名称.");
		  			}
	  			}else{
	  				$sender->sendMessage("§c[传送系统] 你只能在游戏中使用这个命令!");
	  			}
	  		break;
	  
  		}
	}
	
}
