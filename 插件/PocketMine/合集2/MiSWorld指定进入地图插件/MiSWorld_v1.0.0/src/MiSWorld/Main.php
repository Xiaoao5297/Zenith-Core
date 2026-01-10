<?php
namespace MiSWorld;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\event\entity\EntityTeleportEvent;
class Main extends PluginBase implements Listener{
public function onEnable(){
$this->getLogger()->info("§6MiSWorld加载成功 作者mikasa,qq1244456115 贴吧id:rodeahorse");
$this->getServer()->getPluginManager()->registerEvents($this, $this);
@mkdir($this->getDataFolder());
$this->world=new Config($this->getDataFolder()."world.yml", Config::YAML, array());
$this->trust=new Config($this->getDataFolder()."trust.yml", Config::YAML, array());
$this->admin=new Config($this->getDataFolder()."admin.yml", Config::YAML, array());
$this->gm=new Config($this->getDataFolder()."levelgamemode.yml", Config::YAML, array());
}
public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
switch(strtolower($cmd->getname())){
case "miw":
if(!isset($args[0])){
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]添加/取消禁止进入世界:/miw world <世界名> 设置/取消管理:/miw admin <玩家名> 设置/取消信任玩家:/miw trust <玩家名> 查看地图列表/miw list 传送地图/miw tp <地图名> 更改地图模式/miw gm <地图名> <创造/生存>");
return true;
}
switch($args[0]){
case "tp":
if($sender instanceof player){
if(!isset($args[1])){
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]用法/miw tp <世界>");
return true;
}else{
$w=$args[1];
if($this->getServer()->getLevelByName($w)==null){
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]不存在该世界!");
}else{
if($this->world->get($w)==null){
$sender->teleport(Server::getinstance()->getlevelbyname($w)->getsafespawn());
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]已将你传送到{$w}!");
if($this->gm->get($w)=="1"){
$sender->setgamemode(1);
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]该地图为创造模式,已将你的模式设为创造!");
if($this->gm->get($w)=="0"){
$sender->setgamemode(0);
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]该地图为生存模式,已将你的模式设为生存!");
}
}
}else{
$an=$sender->getName();
if(!$this->admin->exists($an) and !$this->trust->exists($an)){
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]该地图禁止进入!");
}else{
$sender->teleport(Server::getinstance()->getlevelbyname($w)->getsafespawn());
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]已将你传送到{$w}!");
if($this->gm->get($w)=="1" and !$sender->isOP() and !$this->admin->exists($an)){
$sender->setgamemode(1);
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]该地图为创造模式,已将你的模式设为创造!");
if($this->gm->get($w)=="0" and !$sender->isOP() and !$this->admin->exists($an)){
$sender->setgamemode(0);
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]该地图为生存模式,已将你的模式设为生存!");
}
}
}
}
return true;
}
}
}else{
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]你在后台传你妈嗨!");
}
return true;
case "list":
$sender->sendMessage(TextFormat::RED . "----------[MiSWorld]----------");
$sender->sendMessage(TextFormat::GREEN . "--------服务器地图--------");
foreach(Server::getinstance()->getlevels() as $dt){
$sender->sendMessage(TextFormat::GOLD .$dt->getfoldername());
}
$sender->sendMessage(TextFormat::RED . "----------[MiSWorld]----------");
return true;
break;
case "world":
if($sender->isOP()){
if(!isset($args[1])){
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]用法/miw world <世界>");
return true;
}else{
$w=$args[1];
if($this->getServer()->getLevelByName($w)==null){
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]不存在该世界!");
}else{
if(!$this->world->exists($w)){
$this->world->set($w);
$this->world->save();
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]添加地图{$w}成功!");
}else{
$this->world->remove($w);
$this->world->save();
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]取消地图{$w}成功!");
}
}
}
}else{
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]你不是op 无法使用该命令!");
}
return true;
case "gm":
if($sender->isOP()){
if(!isset($args[1])){
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]用法/miw gm <世界> <创造/生存>");
return true;
}else{
$w=$args[1];
if($this->getServer()->getLevelByName($w)==null){
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]不存在该世界!");
}else{
if(!isset($args[2])){
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]用法/miw gm <世界> <创造/生存>");
return true;
}else{
switch($args[2]){
case "创造":
if($this->gm->get($w)==null or $this->gm->get($w)=="0"){
$this->gm->set($w,"1");
$this->gm->save();
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]添加地图{$w}为创造模式成功!");
}else{
if($this->gm->get($w)=="1"){
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]地图{$w}已经是创造模式了!");
}
}
return true;
case "生存":
if($this->gm->get($w)==null or $this->gm->get($w)=="1"){
$this->gm->set($w,"0");
$this->gm->save();
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]添加地图{$w}为生存模式成功!");
}else{
if($this->gm->get($w)=="0"){
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]地图{$w}已经是生存模式了!");
}
}
return true;
}
break;
}
}
}
}else{
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]你不是op 无法使用该命令!");
}
return true;
case "admin":
if($sender->isOP()){
if(!isset($args[1])){
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]用法/miw admin <玩家名>");
return true;
}else{
$aname=$args[1];
if(!$this->admin->exists($aname)){
$this->admin->set($aname);
$this->admin->save();
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]添加管理员{$aname}成功!");
}else{
$this->admin->remove($aname);
$this->admin->save();
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]取消管理员{$aname}成功!");
}
}
}else{
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]你不是op 无法使用该命令!");
}
return true;
case "trust":
if($sender->isOP()){
if(!isset($args[1])){
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]用法/miw trust <玩家名>");
return true;
}else{
$aname=$args[1];
if(!$this->trust->exists($aname)){
$this->trust->set($aname);
$this->trust->save();
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]添加信任{$aname}成功!");
return true;
}else{
$this->trust->remove($aname);
$this->trust->save();
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]取消信任{$aname}成功!");
return true;
}
}
}else{
$sender->sendMessage(TextFormat::GOLD . "[MiSWorld]你不是op 无法使用该命令!");
}
break;
return true;
}
}
}
}