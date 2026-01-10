<?php
/*************插件作者:叫兽 租服加Q:977782528*************/
/*************本插件开源,禁止用于任何商业用途!*************/
/*************声明:本插件部分代码参考网络**************/
namespace Ylh;
use pocketmine\plugin\Plugin;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\Utils;
use pocketmine\utils\TextFormat as YLH;
use pocketmine\item\Item;
use pocketmine\utils\Config;
use pocketmine\tile\Sign;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\block\BlockBreakEvent;
class main extends PluginBase implements Listener {
    public $sign;
    public $config;
    public $prefix = "[服务器状态]";
	public function onEnable() {
		if(!is_dir($this->getDataFolder())){
            @mkdir($this->getDataFolder());
        }

        $this->saveResource("sign.yml");
        $this->saveResource("config.yml");

        $this->sign = new Config($this->getDataFolder()."sign.yml", Config::YAML); //FIXED !
        $this->config = new Config($this->getDataFolder()."config.yml",Config::YAML);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $time = $this->config->get("time");
        if(!(is_numeric($time))){
            $time = 20;
            $this->getLogger()->alert("无法读取默认配置文件! 请修改config.yml时间为: ".YLH::AQUA." 1 ".YLH::WHITE."秒!");
        }else{ $time = $time * 20; }
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new Task($this), $time);
		$this->getLogger()->info("§b服务器状态!by 叫兽. 极致优化");
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		/*$tps = $this->getServer()->getTicksPerSecondAverage();
		$maxnum = $this->getServer()->getMaxPlayers ();
		$online = count($this->getServer()->getOnlinePlayers());
		$load = $this->getServer()->getTickUsageAverage();
		$u = Utils::getMemoryUsage(\true);
		$usage = \round(($u[0] / 1024) / 1024, 2) . "/" . \round(($u[1] / 1024) / 1024, 2) . "/".\round(($u[2] / 1024) / 1024, 2)." MB @ " . Utils::getThreadCount() . " threads";
 		$this->getLogger()->info("Tps:". $tps );
        $this->getLogger()->info("Online:".$online. "/".$maxnum);
        $this->getLogger()->info("Load:". $load . "%");
        $this->getLogger()->info("Memory:".$usage);*/
	}
	public function enabled(){
        return $this->sign->get("sign")['enabled'];
    }
	
    public function level(){
        return $this->sign->get("sign")['level'];
    }

    public function getThisSignX(){
        return $this->sign->get("sign")['x'];
    }

    public function getThisSignY(){
        return $this->sign->get("sign")['y'];
    }

    public function getThisSignZ(){
        return $this->sign->get("sign")['z'];
    }



    public function onSignChange(SignChangeEvent $event){
        $player = $event->getPlayer();
        if(strtolower(trim($event->getLine(0))) == "服务器状态" || strtolower(trim($event->getLine(0))) == "[服务器状态]"){
            if($player->hasPermission("signstatus")){
                $tps = $this->getServer()->getTicksPerSecondAverage();//获取Tps
                $maxnum = $this->getServer()->getMaxPlayers ();//获取总人数
		        $online = count($this->getServer()->getOnlinePlayers());//获取在线人数
                $level = $event->getBlock()->getLevel()->getName();
				$load = $this->getServer()->getTickUsageAverage();//获取load
                $event->setLine(0,YLH::AQUA."[服务器状态]");
                $event->setLine(1,YLH::AQUA."Tps指数: [".$tps."]");
                $event->setLine(2,YLH::AQUA."在线人数: ".YLH::AQUA.$online.YLH::AQUA."/".YLH::AQUA.$maxnum."");
                $event->setLine(3,YLH::AQUA."服务器负载: " . YLH::AQUA.$load . '%');

                $this->sign->setNested("sign.x", $event->getBlock()->getX());
                $this->sign->setNested("sign.y", $event->getBlock()->getY());
                $this->sign->setNested("sign.z", $event->getBlock()->getZ());
                $this->sign->setNested("sign.enabled", true);
                $this->sign->setNested("sign.level", $level);
                $this->sign->save();
                $this->sign->reload();
            }else{
                $event->setCancelled();
            }
        }
    }


    public function onPlayerBreakBlock(BlockBreakEvent $event){
        if ($event->getBlock()->getID() == Item::SIGN || $event->getBlock()->getID() == Item::WALL_SIGN || $event->getBlock()->getID() == Item::SIGN_POST) {
            $signt = $event->getBlock();
            if (($tile = $signt->getLevel()->getTile($signt))){
                if($tile instanceof Sign) {
                    if ($event->getBlock()->getX() == $this->sign->getNested("sign.x") || $event->getBlock()->getY() == $this->sign->getNested("sign.y") || $event->getBlock()->getZ() == $this->sign->getNested("sign.z")) {
                        if($event->getPlayer()->hasPermission("signstatus.break")) {
                            $this->sign->setNested("sign.x", $event->getBlock()->getX());
                            $this->sign->setNested("sign.y", $event->getBlock()->getY());
                            $this->sign->setNested("sign.z", $event->getBlock()->getZ());
                            $this->sign->setNested("sign.enabled", false);
                            $this->sign->setNested("sign.level", "world");
                            $this->sign->save();
                            $this->sign->reload();
                        }else{
                            $event->setCancelled();
                        }
                    }
                }
            }
        }
    }

	public function onDisable() {
		$this->getLogger()->info("§4状态插件卸载!");
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $arg)
	{
		if(strtolower($cmd->getName())=="getload")
		{
		$tps = $this->getServer()->getTicksPerSecondAverage();//获取Tps
		$maxnum = $this->getServer()->getMaxPlayers ();//获取总人数
		$online = count($this->getServer()->getOnlinePlayers());//获取在线人数
		$u = Utils::getMemoryUsage(\true);//获取已用内存
		$usage = \round(($u[0] / 1024) / 1024, 2) . "/" . \round(($u[1] / 1024) / 1024, 2) . "/".\round(($u[2] / 1024) / 1024, 2)." MB" ;//返回内存信息
		$threads = Utils::getThreadCount();
		$load = $this->getServer()->getTickUsageAverage();//获取load
		$version=(string) Server::getInstance()->getPocketMineVersion();
		$sender->sendMessage("§b==========服务器状态==========");
		$sender->sendMessage("§b在线人数: ".$online. "/".$maxnum);
 		$sender->sendMessage("§bTps指数: ". $tps );
        $sender->sendMessage("§bCPU负荷: ". $load . "%");
		$sender->sendMessage("§b已用内存: ".$usage);
		$sender->sendMessage("§b线程指数: ".$threads);
		$sender->sendMessage("§b当前版本: ".$version);
		$sender->sendMessage("§b==========一一一一一==========");
		}
		return true;
	
}
}
	