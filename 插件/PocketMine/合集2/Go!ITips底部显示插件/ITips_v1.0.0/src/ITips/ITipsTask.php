<?php
namespace ITips;

use pocketmine\scheduler\PluginTask;
use ITips\ITips;

class ITipsTask extends pluginTask
{
	
	public function __construct(ITips $plugin)
	{
		parent::__construct($plugin);
		$this->plugin = $plugin;
	}

	public function onRun($currentTicks)
	{
		$this->plugin->tip();
	}
}
?>
