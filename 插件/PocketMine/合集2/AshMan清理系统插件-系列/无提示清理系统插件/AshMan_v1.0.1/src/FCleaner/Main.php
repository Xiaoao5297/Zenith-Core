<?php
namespace FCleaner;

use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\event\Listener;
use pocketmine\utils\MainLogger;
use pocketmine\utils\Config;
use pocketmine\level\Level;
use pocketmine\scheduler\PluginTask;
use pocketmine\entity\Entity;
use pocketmine\entity\DroppedItem;
use pocketmine\entity\Creature;
use pocketmine\entity\Human;

class Main extends PluginBase implements Listener
{
	public function onEnable()
	{
		@mkdir($this->getDataFolder());
 		$this->cfg=new Config($this->getDataFolder()."config.yml",Config::YAML,array());
		if(!$this->cfg->exists("CleanDelay"))
		{
			$this->cfg->set("CleanDelay","300");
			$this->cfg->save();
		}
		$this->CleanDelay=$this->cfg->get("CleanDelay")*20;
		$this->cleaner=new cleaner($this);
		$this->getServer()->getScheduler()->scheduleRepeatingTask($this->cleaner, 1);
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
	}
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $arg)
	{
		if(!isset($arg[0])){unset($sender,$cmd,$label,$arg);return false;};
		switch($arg[0])
		{
		case "clean":
		case "cl":
		case "c":
			$this->removeMobs();
			$this->clean();
			break;
		case "reload":
			$this->cfg->reload();
			if(!$this->cfg->exists("CleanDelay"))
			{
				$this->cfg->set("CleanDelay","300");
				$this->cfg->save();
			}
			$this->CleanDelay=$this->cfg->get("CleanDelay")*20;
			$this->cleaner->tmp=$this->CleanDelay;
			$sender->sendMessage("§a[清理系统] 重载完成!");
			break;
		default:
			unset($sender,$cmd,$label,$arg);
			return false;
			break;
		}
		unset($player,$killer,$event,$name1,$name2);
		return true;
	}

    public function clean(){
        $i = 0;
        foreach($this->getServer()->getLevels() as $level){
            foreach($level->getEntities() as $entity){
                if(!$this->isEntityExempted($entity) && !($entity instanceof Creature)){
                    $entity->close();
                    $i++;
                }
            }
        }
Server::getInstance()->broadcastMessage("");
      unset($i,$entity);
    }

	    public function exemptEntity(Entity $entity){
        $this->exemptedEntities[$entity->getID()] = $entity;
    }
    
    public function isEntityExempted(Entity $entity){
        return isset($this->exemptedEntities[$entity->getID()]);
    }
	public function removeMobs(){
        $i = 0;
        foreach($this->getServer()->getLevels() as $level){
            foreach($level->getEntities() as $entity){
                if(!$this->isEntityExempted($entity) && $entity instanceof Creature && !($entity instanceof Human)){
                    $entity->close();
                    $i++;
                }
            }
        }
        Server::getInstance()->broadcastMessage("");
    }

}
class cleaner extends PluginTask
{
	//public players;
    public function __construct(Main $plugin)
    {
        parent::__construct($plugin);
        $this->plugin = $plugin;
        $this->tmp=$plugin->CleanDelay;
    }
    public function onRun($currentTick)
    {
    	$this->plugin = $this->getOwner();
    	$this->tmp--;
    	if($this->tmp<=0)
    	{
    		$this->plugin->removeMobs();
    		$this->plugin->clean();
    		$this->tmp=$this->plugin->CleanDelay;
    	}
    	if($this->tmp==100)
    	{
    		Server::getInstance()->broadcastMessage("");
    	}
    	if($this->tmp==200)
    	{
    		Server::getInstance()->broadcastMessage("");
    	}
    	if($this->tmp==400)
    	{
    		Server::getInstance()->broadcastMessage("");
    	}
    }

}
?>
