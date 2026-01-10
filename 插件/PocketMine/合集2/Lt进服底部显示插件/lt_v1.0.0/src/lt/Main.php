<?php

namespace lt;

use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\tile\Tile;
use pocketmine\level\sound\DoorSound;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\Config;


class Main extends PluginBase implements Listener{

    public function onEnable(){
	$this->getServer()->getPluginManager()->registerEvents($this,$this);
	
	
	 $this->getLogger()->info("[加入退出Tip]加入退出提示开启！作者：FransicSteve");
	 @mkdir($this->getDataFolder(),0777,true);
		@mkdir($this->getDataFolder());
		 		$this->message = new Config($this->getDataFolder()."message.yml", Config::YAML, array("qt"=>"服务器" ,));

	}
	
	


public function onjoin(PlayerJoinEvent $event){

$player=$event->getPlayer();
$n=$player->getName();
$qt=$this->message->get("qt");
$event->setJoinMessage("");

 if ($player->isOp()) {
$q = "OP";
} else {
$q = "玩家";
}


$this->getServer()->broadcastPopup("§e$q §b$n §a加入了$qt");

	foreach ($this->getServer()->getOnlinePlayers() as $play) {


$play->getLevel()->addSound(new DoorSound($play));
}
}



public function onquit(PlayerQuitEvent $event){
$qt=$this->message->get("qt");
$player=$event->getPlayer();
$n=$player->getName();

 if ($player->isOp()) {
$q = "OP";
} else {
$q = "玩家";
}

$event->setQuitMessage("");

$this->getServer()->broadcastPopup("§e$q §b$n §c退出了$qt ");

	foreach ($this->getServer()->getOnlinePlayers() as $play) {


$play->getLevel()->addSound(new EndermanTeleportSound($play));
}

}





         public function onCommand(CommandSender $sender, Command $command, $label, array $args)
    {
    if ($sender instanceof Player) {
  switch ($command->getName()) {
         case 'qt':
  if(count($args) === 0) $sender->sendMessage("§a用法:§b/qt set 您的服务器名称");
  if($args[0]=="set"){
  if($sender instanceof Player){
$this->message->set("qt","$args[1]");
    $this->message->save();
    $sender->sendMessage("§b[加入退出Tip] §4您将服务器名称设置为§a $args[1] ！");
return true;
break;

}}
}}}}
