<?php
namespace qd;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use onebone\economyapi\EconomyAPI;


class qd extends PluginBase implements Listener
	{
		private $path,$conf;
		
		public $money;

		
		public function onEnable()
		{ 
		    
			@mkdir($this->getDataFolder());
			$this->cfg=new Config($this->getDataFolder()."config.yml",Config::YAML,array());
			if(!$this->cfg->exists("Money"))
				{
					$this->cfg->set("Money","50");
					$this->cfg->save();
				}
			if(!$this->cfg->exists("QianZhui"))
				{
					$this->cfg->set("QianZhui","签到插件");
					$this->cfg->save();
				}
			$this->Money=$this->cfg->get("Money");
			$this->QZ=$this->cfg->get("QianZhui");
			$this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->getLogger()->info("欢迎使用由labi编写的签到插件 !");
			$this->getLogger()->info("本插件完全免费，如果你是买的（那你就被坑了2333） !");
		}
	
	
		public function onCommand(CommandSender $sender, Command $command, $label, array $args)
		{
			$user = $sender->getName();
			$ny = date("Y");
			$nm = date("m");
			$nd = date("d");
			$m = $this->Money;
			$qz = $this->QZ;
			if ($sender instanceof Player) 
			{
				switch($command->getName())
				{
					case "qd":
						if(!file_exists($this->getDataFolder() ."$ny.$nm.$nd./")) 
						{
							@mkdir($this->getDataFolder() ."$ny.$nm.$nd./");
							file_put_contents($this->getDataFolder() ."$ny.$nm.$nd./".$user . ".yml",$this->getResource("player.yml"));
							EconomyAPI::getInstance()->addMoney($user, $m);
							$sender->sendMessage("[$qz]签到成功，$m 游戏币已经发到你的账户");

						}
						elseif(!file_exists($this->getDataFolder() ."$ny.$nm.$nd./$user.yml"))
						{
							file_put_contents($this->getDataFolder() ."$ny.$nm.$nd./".$user . ".yml",$this->getResource("player.yml"));
							EconomyAPI::getInstance()->addMoney($user, $m);
							$sender->sendMessage("[$qz]签到成功，$m 游戏币已经发到你的账户");
					
						}
						elseif(file_exists($this->getDataFolder() ."$ny.$nm.$nd./$user.yml"))				
						{
							$sender->sendMessage("[$qz]你已经签到过了，请不要重复签到！");

						}
				}
				return true;
			}
			else
			{
				$sender->sendMessage(TextFormat::RED."请在游戏中使用此命令");
				return true;
			}
		}
	}
						
	
	