<?php
namespace top\CleanData;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TM;
use pocketmine\Player;

class CleanData extends \pocketmine\plugin\PluginBase implements \pocketmine\event\Listener
{
	public function onEnable()
	{
		$this->getServer()->getLogger()->info(TM::GREEN . "Plugin Has been Enable!");
		@mkdir($this->getDataFolder());
		$this->saveResource("config.yml");
		$this->config = new Config($this->getDataFolder() . "config.yml");
		$this->getServer()->getLogger()->info(TM::GREEN . "成功清理过期 " . $this->config->get("day") . "天以上的玩家资料  " . $this->cleanPlayersData() . " 个.");
	}
	public function cleanPlayersData()
	{
		$ps_dir = scandir($this->getServer()->getDataPath() . "players/");
		unset($ps_dir[0],$ps_dir[1]);
		$x = 0;
		foreach($ps_dir as $f)
		{
			$file = $this->getServer()->getDataPath() . "players/" . $f ;
			//var_dump($file);
			$time = filemtime($file);
			//var_dump(date('Y-m-H',$time));
			if(($time + (24*60*60 * $this->config->get("day"))) < strtotime("now"))
			{
				unlink($file);
				$x++;
				//echo "delete\n";
			}
		}
		return $x;
	}
	
	
	
}//end




?>