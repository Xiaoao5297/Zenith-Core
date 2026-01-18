<?php
namespace ITouchSign;

use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use pocketmine\event\Listener;

use ITouchSign\ITouchSign;

class ITouchSignTask extends PluginTask
{
	
	public function __construct(ITouchSign $plugin)
	{	
	    parent::__construct($plugin);
		$this->plugin = $plugin;
	}
	
	
	public function onRun($currentTicks)
	{
		$this->plugin->saveMapPlayers();
		foreach($this->plugin->work as $i=>$v){
			//public function loadSign(string $from,$value,$justUp=true)
			$this->plugin->loadSign($i,$v,true);
		}
	}
}