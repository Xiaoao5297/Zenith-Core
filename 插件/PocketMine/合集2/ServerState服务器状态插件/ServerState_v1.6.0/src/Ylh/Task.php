<?php
namespace Ylh;
use pocketmine\scheduler\PluginTask;
use pocketmine\Server; 
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\tile\Sign;
use pocketmine\utils\TextFormat as YLH;

class Task extends PluginTask{
	private $plugin;
	private $countable;
	public function __construct(main $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
		$this->countable = 0;
	}

    public function onRun($currentTick){
    	$val = $this->plugin->sign->get("sign")["enabled"];
		if($val == "true" || $val == true){
			$x = $this->plugin->sign->get("sign")["x"];
			$y = $this->plugin->sign->get("sign")["y"];
			$z = $this->plugin->sign->get("sign")["z"];
			$lvz = $this->plugin->sign->get("sign")["level"];
			$tps = Server::getInstance()->getTicksPerSecondAverage();
			$maxnum = Server::getInstance()->getMaxPlayers ();
		    $online = count(Server::getInstance()->getOnlinePlayers());
			$load = Server::getInstance()->getTickUsageAverage();
			$level = Server::getInstance()->getLevelByName($lvz);
            if($level instanceof Level) {
                $sign = $level->getTile(new Vector3($x, $y, $z));
                if ($sign instanceof Sign) {
                    $sign->setText(YLH::AQUA."[服务器状态]", YLH::AQUA."Tps指数: [".$tps."]", YLH::AQUA."在线人数: ".YLH::AQUA.$online.YLH::AQUA."/".YLH::AQUA.$maxnum."", YLH::AQUA.'服务器负载: '.YLH::AQUA.$load."%");
                }
            }
		}
    }
}