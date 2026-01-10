<?php

namespace RHelp;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\Server;
use pocketmine\event\player\PlayerItemHeldEvent;

class RHelp extends PluginBase implements Listener{

public $config;
public function onEnable(){
$this->getServer()->getPluginManager()->registerEvents($this, $this);
$this->getLogger()->info("§5RHelp自定义帮助插件启动成功，作者：Ra158 感谢你的支持");
@mkdir($this->getDataFolder(),0777,true);
$this->config=new Config($this->getDataFolder()."Config.yml",Config::YAML,array());
$this->item=new Config($this->getDataFolder()."Tip.yml",Config::YAML,array());
}
public function onCommand (CommandSender $sender, Command $command, $label, array $args){
if($command == "rhelp"){
if(!$sender->isOp()){
					$sender->sendMessage("§4[RHelp]您没有权限使用此命令");
					return true;
}else{
if(isset($args[0])){
switch($args[0]){
case "set":
if(isset($args[1])){
if(isset($args[2])){

$this->config->set($args[1],$args[2]);
$sender->sendMessage("§b[RHelp]已经将帮助<$args[1]>的内容设置为<$args[2]>");
$this->config->save();
}else{return false;}
}else{return false;}
break;
case "remove":
if(isset($args[1])){
$this->config->remove($args[1]);
$this->config->save();
$sender->sendMessage("§5[RHelp]已删除为[".$args[1]."]的帮助");
return true;
}
break;
case "item":
if(isset($args[1])&&isset($args[2])){
if($args[1]>0){
if(!$this->item->exists($args[1])){
$this->item->set($args[1],$args[2]);
$this->item->save();
$sender->sendMessage("§e[RHelp]ID为$args[1]的物品提示语已设置为$args[2]");
}else{
$this->item->remove($args[1]);
$this->item->save();
$sender->sendMessage("§e[RHelp]ID为$args[1]的物品提示语已删除！");
}
}else{$sender->sendMessage("§4[RHelp]请输入有效物品ID");}
}
return true;
break;
}
}
}


if($command == "phelp"){

if(isset($args[0])){
if($this->config->exists($args[0])){
$ghelp = $this->getConfig()->get ($args[0]);
	$ghelp = str_replace( "%换行","\n", $ghelp);
$sender->sendMessage("§e[RHelp] [$args[0]]的帮助内容：\n$ghelp");
return true;
}else{$sender->sendMessage("§4[RHelp]没有为[".$args[0]."]的帮助");return true;}
}
else{
$sender->sendMessage("§2[RHelp]请输入/phelp <帮助名称> 查看帮助");
return true;}
}
}
}
public function onPlayerItemHeld(PlayerItemHeldEvent $event){
$player = $event->getPlayer();
$item = $event->getItem()->getID();
$tip = $this->item->get($item);
if($this->item->exists($item)){
$player->sendTip($tip);
}
}

}?>