<?php
namespace KLockCid;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerPreLoginEvent;



class Main extends PluginBase implements Listener{
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
                $this->getLogger()->info("§6<绑定设备插件>作者：Knight");
				@mkdir($this->getDataFolder(),0777,true);

		$this->players = new Config($this->getDataFolder()."Players.yml",Config::YAML,array());
		
	    $this->admin = new Config($this->getDataFolder()."Admin.yml",Config::YAML,array());
				
		$this->unlock = new Config($this->getDataFolder()."Unlock.yml",Config::YAML,array());
		
		$this->conf = new Config($this->getDataFolder()."Config.yml",Config::YAML,array(
		"进服绑定设备"=>true,
		"Msg"=>"§6该账号已绑定设备"
		));
	}
	
public function onJoin(PlayerJoinEvent $event){             

$player = $event->getPlayer();

$name = $player->getName();

$cid = $player->getClientId();

if(!$this->unlock->exists(strtolower($name)) && $this->conf->get("进服绑定设备") == true){

$this->players->set(strtolower($name),$cid);

$this->players->save(true);

}

}

public function onPreLogin(PlayerPreLoginEvent $event){
	
$player = $event->getPlayer();

$name = $player->getName();

$cid = $player->getClientId();

$msg = $this->conf->get("Msg");

if($this->players->exists(strtolower($name))){
	
if($this->players->get(strtolower($name))!=$cid){

$event->setCancelled();

$event->setKickMessage($msg);
	
}
	
}

}


public function onCommand(CommandSender $sender, Command $command, $label, array $args){

if($command->getName() == "lock"){
	
if(!isset($args[0])){

if($this->admin->exists(strtolower($sender->getName())) || $sender->getName() == "CONSOLE"){
	
$sender->sendMessage("§a========§e[KLockCid §6Help§e]§a========");
	
$sender->sendMessage("§b/lock <remove> <玩家名> -- 强制解绑某玩家");

$sender->sendMessage("§b/lock admin <玩家名> -- 添加一个管理员");

$sender->sendMessage("§b/lock del -- 重置你的cid");

$sender->sendMessage("§b/lock unlock -- 关闭进服绑定设备");

$sender->sendMessage("§b/lock lock -- 开启进服绑定设备");

$sender->sendMessage("§b/lock see <玩家名> -- 查看玩家的cid");

}else{
	
$sender->sendMessage("§a========§e[KLockCid §6Help§e]§a========");

$sender->sendMessage("§b/lock del -- 重置你的cid");

$sender->sendMessage("§b/lock unlock -- 关闭进服绑定设备");

$sender->sendMessage("§b/lock lock -- 开启进服绑定设备");

}

}

if(isset($args[0])){
	
switch($args[0]){

case "remove":

if($this->admin->exists(strtolower($sender->getName())) || $sender->getName() == "CONSOLE"){
	
if(isset($args[1])){
		
if($this->players->exists(strtolower($args[1]))){
			
$this->players->remove(strtolower($args[1]));
			
$this->players->save(true);

$sender->sendMessage("§e[KLockCid]§a你已强制解绑玩家$args[1]的设备");
			
}else{
	
$sender->sendMessage("§e[KLockCid]§c该玩家没有绑定过设备");
	
}

}else{
	
$sender->sendMessage("§e[KLockCid]§a用法/lock remove <玩家名>");
	
}

}else{
	
$sender->sendMessage("§e[KLockCid]§c你没有权限使用这个指令");
	
}

break;

case "admin":

if($sender->getName() == "CONSOLE"){
	
if(isset($args[1])){
	
if($this->admin->exists(strtolower($args[1]))){
	
$this->admin->remove(strtolower($args[1]));

$this->admin->save(true);

$sender->sendMessage("§e[KLockCid]§6成功删除管理员$args[1]");

}else{

$this->admin->set(strtolower($args[1]),true);

$this->admin->save(true);

$sender->sendMessage("§e[KLockCid]§a成功添加管理员$args[1]");

}

}else{
	
$sender->sendMessage("§e[KLockCid]§a用法/lock admin <玩家名>");
	
}
	
}else{
	
$sender->sendMessage("§e[KLockCid]§c请在控制台使用该指令");
	
}

break;

case "del":

if($sender instanceof Player){
	
if($this->players->exists(strtolower($sender->getName()))){
	
$this->players->remove(strtolower($sender->getName()));

$this->players->save(true);

$sender->sendMessage("§e[KLockCid]§6你已重置cid,重新登录即可绑定设备");
	
}else{

$sender->sendMessage("§e[KLockCid]§c你并没有绑定设备,无需重置cid");

}
	
}else{
	
$sender->sendMessage("§e[KLockCid]§c请在游戏里使用该指令");
	
}

break;

case "unlock":

if($sender instanceof Player){
	
if($this->players->exists(strtolower($sender->getName()))){
	
$sender->sendMessage("§e[KLockCid]§c请先重置cid再使用该指令");

}else{

if(!$this->unlock->exists(strtolower($sender->getName()))){
	
$this->unlock->set(strtolower($sender->getName()),true);

$this->unlock->save(true);

$sender->sendMessage("§e[KLockCid]§6你已关闭进服绑定设备功能");
	
}else{

$sender->sendMessage("§e[KLockCid]§c检测到你已经关闭该功能,请勿重复关闭");

}
}

}else{
	
$sender->sendMessage("§e[KLockCid]§c请在游戏里使用该指令");
	
}

break;

case "lock":

if($sender instanceof Player){
	
if($this->unlock->exists(strtolower($sender->getName()))){
	
$this->unlock->remove(strtolower($sender->getName()));

$this->unlock->save(true);	

$sender->sendMessage("§e[KLockCid]§a你已开启进服绑定设备功能,请重新登录绑定设备");

}else{
	
$sender->sendMessage("§e[KLockCid]§c检测到你已经开启该功能,请勿重复开启");
	
}

}else{
	
$sender->sendMessage("§e[KLockCid]§c请在游戏里使用该指令");
	
}

break;

case "see":

if($sender->getName() == "CONSOLE" || $this->admin->exists(strtolower($sender->getName()))){

if(isset($args[1])){

$cid = $this->players->get(strtolower($args[1]));

$sender->sendMessage("§a========§e[Player §6Info§e]§a========");

$sender->sendMessage("§e玩家名: $args[1]");

$sender->sendMessage("§aCid:" .$cid);

}else{

$sender->sendMessage("§e[KLockCid]§a用法/lock see <游戏名>");

}

}else{

$sender->sendMessage("§e[KLockCid]§c你没有权限使用这个指令");

}

}

}

return true;

}

}

}

