<?php
/* 
*日志:
*关于本插件:
   BanCommand只是一个失败品，我本想完成一个权限插件
   但发现我的时间或许不够
   这个失败品便诞生了....
*关于日后计划...
   我或许会将它更新成一个权限组插件
   但这要看有没有时间了.....
*
 */
namespace BanCommands;

use pocketmine\utils\TextFormat as MT;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player; 
use pocketmine\server; 
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerCommandPreprocessEvent;


class MainClass extends PluginBase implements Listener
{
	private $config;
	
	public function onEnable()
	{
		if (!file_exists($this->getDataFolder()))
		{
            @mkdir($this->getDataFolder(), 0700, true);
        }
		$this->getLogger()->info(MT::BLUE . "[BANCOMMAND] I load :)". MT::RED . "by 18wyj2");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->trusts = new Config($this->getDataFolder()."trusts.txt", Config::ENUM);
		$this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML, array(
		"banned-command" => array(), 
		"msg-use" => "[BanCommand] 这个命令是被禁用的 !!!"
		));
	}
	
	public function PlayerRunCommand(PlayerCommandPreprocessEvent $event) 
	{
            $commandInfo = explode(" ", $event->getMessage());
            $command = substr(array_shift($commandInfo), 1);
			$player = $event->getPlayer();
			$name = $player->getDisplayName();
			if($command == in_array($command, $this->config->get("banned-command"))) 
			{
				if(!$this->trusts->exists($name))				
			{
				$player->sendMessage($this->config->get("msg-use"));
				$event->setCancelled();
					}
			}
	}	
	
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args)
	{
        switch($cmd->getName())
		{
		case "bancommand": 
		$config = $this->config->getAll();
        $cs = $config["banned-command"];
			if(isset($args[0]))
			{
				if($args[0] == "add")
				{
					if(isset($args[1]))
					{
						$banc = $args[1];

							if(!in_array($banc, $cs))
							{
								if(!is_array($cs))
								{
									$cs = array($banc);
								}else{
									$cs[] = $banc;
									$sender->sendMessage("[BC] 成功禁用指令 $banc ");
									$config["banned-command"] = $cs;
									$this->config->setAll($config);
									$this->config->save();
								}
							}else{
								$sender->sendMessage("[BC] 这个物品已经被禁用了");
							}           
					}else{
					$sender->sendMessage("[BC] 使用方法 : /bc <add/remove/trust> <指令/ID>");
					}
				}
				if($args[0] == "remove")
				{
					if(isset($args[1]))
					{
						$banc = $args[1];

							if(in_array($banc, $cs))
							{
								$key = array_search($banc, $cs);
								unset($cs[$key]);
								$sender->sendMessage("[BC] 取消禁用指令 $banc ");
								$config["banned-command"] = $cs;
								$this->config->setAll($config);
								$this->config->save();
							}else{
							$sender->sendMessage("[BC] 指令 $banc 的物品没有被禁用");
							}
						
					}else{
					$sender->sendMessage("[BC] 使用方法 : /bc <add/remove/trust> <指令/ID>");
					}
				}
				if($args[0] == 'list'){
				 foreach($this->config->get("banned-command") as $bc){
				 $sender->sendMessage('被禁用的指令:');
				 $sender->sendMessage($bc);
				 }
				 }
				 
				if($args[0] == "trust")
				{
					$playerName = $args[1];
					$this->trusts->set($playerName);
					$this->trusts->save();
					$sender->sendMessage("[BC]".$playerName." 被你信任.");
					Server::getInstance()->broadcastMessage($playerName."被信任");

				}
				if($args[0] == "detrust")
				{
					$playerName = $args[1];
					$this->trusts->remove($playerName);
					$this->trusts->save();
					$sender->sendMessage("[BC]".$playerName." 被你取消信任.");

				}
			}else{
			$sender->sendMessage("[BC] 使用方法 : /bc <add/remove/trust> <指令/ID>");
			}
		}
	}
}