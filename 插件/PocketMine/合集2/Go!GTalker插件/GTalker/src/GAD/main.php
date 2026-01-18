<?php

namespace GAD;
/*
■■■■■Extreme studio■■■■■
      author:GAD
     工作室:GO!极致工作室
     技术群:暂不开放
  

*/

use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;
use pocketmine\event\Listener;
use pocketmine\item\item;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\playerdeathevent;
use pocketmine\utils\Config;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamagebyentityEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\command\Command;
use pocketmine\command\Commandsender;

#use引用结束

class main extends PluginBase implements Listener{
/*
public
  $this->1="§1"
  $this->2="§2"
  $this->3="§3"
  $this->4="§4"
  $this->5="§5"
  $this->6="§6"
  $this->7="§7"
  $this->8="§8"
  $this->9="§9"
  $this->a="§a"
  $this->b="§b"
  $this->c="§c"
  $this->d="§d"
  $this->o="§o"
*/
  public function onEnable(){
   $this->getLogger()->info("GTalker插件正在加载");
   #config文件创建
   
   @mkdir($this->getDataFolder(),0777,true);
		$this->config=new Config($this->getDataFolder()."Chat.yml",Config::YAML,array());
		#称号文件创建
		
		 @mkdir($this->getDataFolder(),0777,true);
		$this->ch=new Config($this->getDataFolder()."ch.yml",Config::YAML,array());
		#事件注册
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		}
		#插件死机action
		public function onDisable(){
		$this->getLogger()->info("GTalker插件关闭中\n\n\n\nGAD说\n\n\n\n");
		}
		#登录提示
		public function onJoin(PlayerJoinEvent $e){
		$p=$e->getPlayer();
		$n=$p->getName();
		$p->sendMessage("§f1=§1■, §f2=§2■,§f3=§3■,§f4=§4■,§f5=§5■, §f6=§6■,§f7=§7■,§f8=§8■,§f9=§9■,§fa=§a■,§fb=§b■,§fc=§c■,§fd=§d■");
	if(	!$this->config->exists($n)){
	$this->config->set($n,§f);
	}
		#玩家文件创建
		#GAD
		
		}
		#指令系统
		 public function onCommand(CommandSender $sender, Command $command, $label, array $arg){
		 $name=$sender->getName();
		 if($sender instanceof Player){
  if(($command->getName()) == "cg"){
  if($sender->isOp()){
  if(isset($arg[0])){
  if($this->config->exists($arg[0])){
  if(isset($arg[1])and is_numeric($arg[1])and$arg[1]<=9){
  if($arg[1]==1){ 
  $this->config->set($arg[0],"§1");}elseif ($arg[1]==2){ 
  $this->config->set($arg[0],"§2");}elseif ($arg[1]==3){ 
  $this->config->set($arg[0],"§3");}elseif ($arg[1]==4){ 
  $this->config->set($arg[0],"§4");}elseif ($arg[1]==5){ 
  $this->config->set($arg[0],"§5");}elseif ($arg[1]==6){ 
  $this->config->set($arg[0],"§6");}elseif ($arg[1]==7){ 
  $this->config->set($arg[0],"§7");}elseif ($arg[1]==8){ 
  $this->config->set($arg[0],"§8");}elseif ($arg[1]==9){ 
  $this->config->set($arg[0],"§9");}elseif ($arg[1]==a){ 
  $this->config->set($arg[0],"§a");}elseif ($arg[1]==c){ 
  $this->config->set($arg[0],"§c");}elseif ($arg[1]==b){ 
  $this->config->set($arg[0],"§b");}elseif ($arg[1]==d){ 
  $this->config->set($arg[0],"§d");}
  $sender->sendMessage("a[GTalk]成功把玩家$arg[0]的Chat颜色改为§$arg[1] ■■■");
  $this->config->save();
  return true;
  }else{
  $sender->sendMessage("§4[GTalker]你没有设置颜色代号或代号输入错误 §f1=§1■, §f2=§2■,§f3=§3■,§f4=§4■,§f5=§5■, §f6=§6■,§f7=§7■,§f8=§8■,§f9=§9■,§fa=§a■,§fb=§b■,§fc=§c■,§fd=§d■");
  return true;
  }
  }else{
  $sender->sendMessage("§c玩家$arg[0]不存在!");}
  }else{
  $sender->sendMessage("§c[GTalker]你没有设置玩家");
  return;
 
  }
  }else{
  $sender->sendMessage("§4[GTalker]你不是op");
  return true;
  }
  }
  }else{
  $sender->sendMessage("§4[GTalker]请在游戏里使用");
  }
  
  ######二级####
  if(($command->getName()) == "cgset"){
  switch($arg[0]){
  case "info":
  if($sender instanceof Player){
  $sender->sendMessage("§a§oGTalker §b§ov1.0.0Bata");
  $sender->sendMessage("§e作者:GAD");
  $sender->sendMessage("§c创建日期:10.30/21:30");
  $sender->sendMessage("§6Go!§e极致工作组，欢迎加入我们!");
  return true;
  }
  case "sz":
  if(isset($arg[1]) and $this->config->exists($arg[1])){
  if($sender->isOp()){
  if(isset($arg[2])){ 
  if(isset($arg[3])){ if(is_numeric($arg[3])and$arg[3]<=9){$this->ch->set($arg[1],$arg[2])[$arg[3]];
  $sender->sendMessage("§a[GTalker]成功设置玩家§a$arg[1]§a的称号§f$arg[3]$arg[2]
"); 
$this->ch->save();
return true;
 }else{
   $sender->sendMessage("§c[GTalker]使用错误!颜色代号不是数字或不是小于9的正整数!使用方法:§6/cgset sz 玩家ID 称号 颜色代号");
   return;
   }
   }else{
   $this->ch->set($arg[1],$arg[2],"§f");
   $this->ch->save();
   $sender->sendMessage("§a[GTalker] §a[GTalker]成功设置玩家§a$arg[1]§a的称号§f$arg[2]，§c由于没有设置颜色代号，默认为§f白色");
   return true;
   }
   }else{
   $sender->sendMessage("§c[GTalker]你没有设置称号,请使用§b/cgset ch 玩家ID 玩家称号 颜色代号 §c进行设置");
   return true;
   }
   }else{
   $sender->sendMessage("§4[GTalker]你不是op");
   return true;
   }
   }else{
   $sender->sendMessage("§4[GTalker]错误!没有设置玩家ID或玩家§a$arg[1]§4不存在!");
   return true;
   }
   }
   }
   
  
  
  
  }
  #说话
  public function onPlayer(PlayerChatEvent $e){
		$e->setCancelled();
		$msg=$e->getMessage();
		$name=$e->getPlayer()->getName();
		 
		 $c=$this->config->get($name); 
		 if($this->config->exists($name)){
		 $ch=$this->ch->get($name);
		 $this->getServer()->broadcastMessage("[ $ch §f]$c $name>>$msg");
		 }else{
		 $this->getServer()->broadcastMessage("[无称号]§f$c $name>>$msg");
		 }
		 }
		 }
  
  
		 