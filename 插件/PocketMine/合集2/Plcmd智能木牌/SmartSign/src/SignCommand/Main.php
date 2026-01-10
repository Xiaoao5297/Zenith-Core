<?php

namespace SignCommand;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\tile\Sign;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\tile\Tile;
use pocketmine\command\ConsoleCommandSender;

class Main extends PluginBase implements Listener{
    private $api, $server, $path;

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function playerBlockTouch(PlayerInteractEvent $event){
        if($event->getBlock()->getID() == 323 || $event->getBlock()->getID() == 63 || $event->getBlock()->getID() == 68){
            $sign = $event->getPlayer()->getLevel()->getTile($event->getBlock());
            if(!($sign instanceof Sign)){
                return;
            }
            $sign = $sign->getText();
            if($sign[1] == 'plcmd' or $sign[1] == 'plcmd'){
                if(isset($sign[2])){
                    $command = $sign[2].$sign[3];
                    $event->getPlayer()->sendMessage("");
					$this->getServer()->dispatchCommand($event->getPlayer(), $command);
                    
                }
            }
        }
    }
}
