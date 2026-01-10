<?php
namespace vban;

use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\scheduler\CallbackTask;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\permission\BanEntry;

class VBan extends PluginBase implements Listener{
  private $con = [];
  private $v;
    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
       $this->getServer()->getScheduler()->scheduleRepeatingTask(new CallbackTask([$this,"check"]), 40);
    }
    private function isOnline($n){
		$n = strtolower($n);
		foreach( $this->getServer()->getOnlinePlayers() as $P ){
			if(strtolower($P->getName())==$n){
				return true;
			}
		}return false;
	}
   public function onCommand(CommandSender $sender, Command $command, $label, array $args){
    $name = strtolower($sender->getName());
    $cid = $sender->getClientId();
    switch($command->getName()){
    case "vban":
     if(isset($args[0])){
      
      $pr = $this->getServer()->getPlayer($args[0]);

      if(!$this->isOnline($args[0])){
       $sender->sendMessage("§a玩家".$args[0]."不在线");
       break;}
     if($args[0]==$name){
      $sender->sendMessage("§c不能t出自己");
      break;}
     if(isset($this->con["VBAN"])){
     $sender->sendMessage("§a有投票正在进行中");
      break;
     }
      #$vplayer[] = $cid;
      $pr = $this->getServer()->getPlayer($args[0]);
      $this->con["VBAN"] = [
      "banplayer" => strtolower($args[0]),
      "vplayer" =>array($cid),
      "t" => time()+60
      ];
      $this->v = $this->v+2;
      $this->getServer()->broadcastMessage("§a【投票系统】正在t出的玩家§f: $args[0] \n§a发起者: §d$name \n§a输入§f/f11§a即可投出一票\n§a时间:§e60秒");
      $pr->sendMessage("§c你正在被投票t出，请不要退出游戏");
      }
      break;
     case 'f11':
      if(isset($this->con["VBAN"]) and in_array($cid,$this->con["VBAN"]["vplayer"])){
   
      $sender->sendMessage("你已经投过票了");
      break;}
      
     if(!isset($this->con["VBAN"])){
     $sender->sendMessage("§a当前没有人发起投票");
     break;}
     
     foreach($this->getServer()->getOnlinePlayers() as $P){
						$n = strtolower($P->getName());
						$z = count($this->getServer()->getOnlinePlayers());
						if($n==$this->con["VBAN"]["banplayer"] and $this->v >= $z){
						 $P->kick("被踢出10分钟");
						 $this->getServer()->getNameBans()->addBan($bp,vban,$this->getDateTime(10));
						break;
						}}
						 $this->con["VBAN"]["vplayer"][] = $cid;
      $this->v = $this->v+2;
      $n = $this->v *0.5;
      $this->getServer()->broadcastMessage("§a目前同意票数:§d $n");
      $sender->sendMessage("§a成功投出一票");
      
    break;
    
    case 'cs':
     $n = $this->v;
     $sender->sendMessage("哈哈哈 $n");
     
     break;
    }}
    public function getDateTime($minute){
		$t = new \DateTime("@" . (time() + ($minute * 60)));
		$t->setTimezone(new \DateTimeZone(date_default_timezone_get()));
		$t->format(BanEntry::$format);
		return $t;
	}
    
    public function check(){
   if(isset($this->con["VBAN"])){
    $t = $this->con["VBAN"]["t"];
   if($t < time()){
    unset($this->con["VBAN"]);
    $this->v=0;
    $this->getServer()->broadcastMessage("§a投票结束");
   }}}
   
   public function qiangtui(PlayerQuitEvent $e){
   if(isset($this->con["VBAN"])){
   $name =strtolower($e->getPlayer()->getName());
   $d = $this->getDateTime(10);
   $bp = $this->con["VBAN"]["banplayer"];
  if($bp == $name){
   $this->getServer()->broadcastMessage("§a【投票系统】".$name."被踢出或强退，封号10分钟");
   $this->getServer()->getNameBans()->addBan($name,vban,$d);
   }}
 }}