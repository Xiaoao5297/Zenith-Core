<?php
namespace Mian;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\Command;
use pocketmine\inventory;
use pocketmine\utils\Config;
use pocketmine\scheduler\CallbackTask;
use MGamesAPI\MGamesAPI;
class Mian extends PluginBase
implements Listener{
public function onDisable(){
$this->getLogger()->info("\n§e=======§9〖§4分割线§9〗§e==========\nGodLeaveWorld件关闭！\n§e=======§9〖§4分割线§9〗§e==========\n");}
public function onCommand(\pocketmine\command\CommandSender $sender, Command $command, $label, array $args){
$name=strtolower($sender->getName());
switch($command->getName()){
case "情侣留言":
if(!$sender instanceof Player){
$sender->sendMessage("§b[§d积分系统§b] §d控制台建议你去和机箱说话→_→");
return true;
}
if(isset($args[0])==false){
$sender->sendMessage("§b请输入： /留言 帮助  来获取帮助");
return true;
}
if($args[0]==null){
$sender->sendMessage("§b你还没有输入内容！");
return true;
}
$user=$sender->getName();
$id=strtolower($user);
if($this->SimpleMarry->isMarry($id)){
$sm=$this->SimpleMarry->YAMLR_Marry($user);
$sender->sendMessage("§b成功给情侣§e".$sm."§b留言！ ".$args[0]);
$this->con=new Config($this->getDataFolder()."ConSM.yml", Config::YAML, array());
$msg="§e你的伴侣: ".$sender->getName()."给你留言啦！===============《回车》".$args[0];
$this->con->set($sm,$msg);
$this->con->save();
return true;
}else{
$sender->sendMessage("§b你去给你的手留言吧(ಥ_ಥ)是在下输了");
}
return true;
case "留言帮助":
$sender->sendMessage("§e=====§d[§b帮助列表§d]§e=====\n§b/留言 玩家名 内容: 给玩家留言 玩家将在下次进入时发送给该玩家\n/情侣留言 内容: 给情侣留言，他(她)将在下次进入时发送给他(她)");
return true;
case "留言":
if(isset($args[0])==false){
$sender->sendMessage("§b请输入： /留言 帮助  来获取帮助");
return true;
}
if($args[0]==null){
$sender->sendMessage("§b你还没有输入玩家名！");
return true;
}
if($args[0]==$sender->getName()){
$sender->sendMessage("§b请不要给自己留言！");
return true;
}
if($args[1]==null){
$sender->sendMessage("§b你还没有输入留言的内容");
return true;
}
$sender->sendMessage("§b成功给§e".$args[0]."§b留言！ ".$args[1]);
$this->config=new Config($this->getDataFolder()."Config.yml", Config::YAML, array());
$nn=$sender->getName();
if(!$sender instanceof Player){
$msg="§e".$nn."给你留言啦！===============《回车》".$args[1];
}else{
$msg="§e控制台给你留言啦！===============《回车》".$args[1];
}


$this->config->set($args[0],$msg);
$this->config->save();
return true;
}
}
public function onEnable(){
$this->getServer()->getPluginManager()->registerEvents($this,$this);
@mkdir($this->getDataFolder(),0777,true);
$this->config=new Config($this->getDataFolder()."Config.yml", Config::YAML, array());
$this->con=new Config($this->getDataFolder()."ConSM.yml", Config::YAML, array());
$this->getLogger()->info("\n§e=====§d[§b帮助列表§d]§e=====\n§b留言系统启动！\n§d作者：§6小凯\n§9QQ：§e2508543202\n§e=====§d[§b帮助列表§d]§e=====\n");
$this->SimpleMarry=$this->getServer()->getPluginManager()->getPlugin("SimpleMarry");
}
public function onPlayerJoin(PlayerJoinEvent $event){
$hc="\n";
$player=$event->getPlayer();
$sender=$player;
$name=$player->getName();
if($this->con->get($name)!=null){
$sender->sendMessage(str_replace('《回车》',$hc,$this->con->get($name)));
$this->con->set($name,null);
$this->con->save();
}
if($this->config->get($name)!=null){
$sender->sendMessage(str_replace('《回车》',$hc,$this->config->get($name)));
$this->config->set($name,null);
$this->config->save();
}
}
}
?>
