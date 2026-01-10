<?php
namespace ChangeWorlds;

/*################
严禁倒卖，侵权死妈
################*/
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\Server;
use spl\BaseClassLoader;
use pocketmine\level\Level;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\position;

class ChangeWorlds extends PluginBase implements Listener{
	public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("§6ChangeWorlds插件开启！§3by §4Seaside §6QQ:§e2363935539 ");
		@mkdir($this->getDataFolder() . "bworlds/" ,0777,true);
}

public function replace($src,$des) {
    $dir = opendir($src);
    @mkdir($des);
    while(false !== ( $file = readdir($dir)) ) {
             if (( $file != '.' ) && ( $file != '..' )) {
                    if ( is_dir($src . '/' . $file) ) {
                            $this->replace($src . '/' . $file,$des . '/' . $file);
                    }  else  {
                            copy($src . '/' . $file,$des . '/' . $file);
                    }
            }
      }
   
    closedir($dir);
}


public function onCommand(CommandSender $sender, Command $command, $label, array $args){
  switch ($command->getName()){
          case 'CW':
		     if($sender->isOP()){
		       if(isset($args[0])){
                    $l = $args[0];
					$n = 0;
					foreach($this->getServer()->getOnlinePlayers() as $p){
						if($p->getLevel()->getFolderName() == $l){
							$p->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
							$p->sendMessage("§1[§bChangeWorlds§1] §3你所在地图正在替换ing，请稍后进入。");
							$n++;
						}
					}
					if (!$this->getServer()->isLevelLoaded($l)) {  //如果这个世界未加载
						$sender->sendMessage("§1[§bChangeWorlds§1] §3地图 $l 未被加载 , 无法卸载");
						return false;
					}
					else {
						$level = $this->getServer()->getLevelbyName($l);
						$ok = $this->getServer()->unloadLevel($level); 
						if($ok !== true){
							$sender->sendMessage("§1[§bChangeWorlds§1] §3卸载地图 $l 失败 ！ ");
							return false;
						}else{
							$sender->sendMessage("§1[§bChangeWorlds§1] §3地图 $l 已被成功卸载 ！ ");
						}
					}
                    $sender->sendMessage("§1[§bChangeWorlds§1] §3开始替换服务器世界");
                    $path1=$this->getServer()->getDataPath();
                    $this->replace($path1."plugins/ChangeWorlds/bworlds/".$l,"".$path1."worlds/".$l);
                    $level = $this->getServer()->getDefaultLevel();
   					$path = $level->getFolderName();
   					//$p1 = dirname($path);
   					//$p2 = $p1."/worlds/";
                    $p2 = $this->getServer()->getDataPath() . "worlds/";
					$path = $p2;
					//$path = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "\\loadLevel\\";
					if ($this->getServer()->isLevelLoaded($l)) {  //如果这个世界已加载
						$sender->sendMessage("§1[§bChangeWorlds§1] §4地图 ".$args[0]." 已被加载 , 无法再次加载" );
						return false;
					}
					elseif (is_dir($path.$l)){
						$sender->sendMessage("§1[§bChangeWorlds§1] §3正在加载地图 ".$args[0]."." );
						$this->getServer()->generateLevel($l);
						$ok = $this->getServer()->loadLevel($l);
						if ($ok === false) {
							$sender->sendMessage("§1[§bChangeWorlds§1] §4地图 ".$args[0]." 加载失败");
							return false;
						}
						else {
							$sender->sendMessage("§1[§bChangeWorlds§1] §3地图 ".$args[0]." 加载成功");
						}
					}else{
						$sender->sendMessage("§1[§bChangeWorlds§1] §4无法加载地图 ".$args[0]." , 地图文件不存在");
						return;
					}
					$sender->sendMessage("§1[§bChangeWorlds§1] §3已经有".$n."人被踢出所更换世界");
					unset($n);
			   }else{$sender->sendMessage("§1[§bChangeWorlds§1] §4请输入世界名称");}
			 }else{$sender->sendMessage("§1[§bChangeWorlds§1] §4然而你并不是op");}
return true;
  }
}
}