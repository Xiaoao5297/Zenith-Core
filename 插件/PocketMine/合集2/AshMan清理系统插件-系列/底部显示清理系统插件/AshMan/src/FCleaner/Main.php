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
 if(!$this->cfg->exists("MobCleanDelay"))
 {
 $this->cfg->set("MobCleanDelay","600");
 $this->cfg->save();
 }
 if(!$this->cfg->exists("Alert1"))
 {
 $this->cfg->set("Alert1","1");
 $this->cfg->save();
 }
 if(!$this->cfg->exists("Alert2"))
 {
 $this->cfg->set("Alert2","1");
 $this->cfg->save();
 }
 $this->CleanDelay=$this->cfg->get("CleanDelay")*20;
 $this->MobCleanDelay=$this->cfg->get("MobCleanDelay")*20;
 $this->Alert1=$this->cfg->get("Alert1");
 $this->Alert2=$this->cfg->get("Alert2");
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
Server::getInstance()->broadcastPopup("§a[清理系统] 共清理{$i}个掉落物.");
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
 Server::getInstance()->broadcastPopup("§a[清理系统] 共杀死{$i}个生物.");
 }

}
class cleaner extends PluginTask
{
  public function __construct(Main $plugin)
 {
 parent::__construct($plugin);
 $this->plugin = $plugin;
 $this->tmp=$plugin->CleanDelay;
 $this->mtmp=$plugin->MobCleanDelay;
 $this->Alerta=$plugin->Alert1;
 $this->Alertb=$plugin->Alert2;
 }
 public function onRun($currentTick)
 {
 $this->plugin = $this->getOwner();
 $this->tmp--;
 $this->mtmp--;
 if($this->tmp<=0)
 {
 
 $this->plugin->clean();
 $this->tmp=$this->plugin->CleanDelay;
 }
 if($this->tmp==100 && $this->Alerta==1)
 {
 Server::getInstance()->broadcastPopup("§a[清理系统] 将在5秒后清除所有掉落物.");
 }
 if($this->tmp==200)
 {
 Server::getInstance()->broadcastPopup("§a[清理系统] 将在10秒后清除所有掉落物.");
 }
 if($this->tmp==400 && $this->Alerta==1)
 {
 Server::getInstance()->broadcastPopup("§a[清理系统] 即将进行世界清理!");
 }
 if($this->mtmp<=0)
 {
 $this->plugin->removeMobs();
 
 $this->mtmp=$this->plugin->MobCleanDelay;
 }
 if($this->mtmp==100 && $this->Alertb==1)
 {
 Server::getInstance()->broadcastPopup("§a[清理系统] 将在5秒后清除所有生物");
 }
 if($this->mtmp==200)
 {
 Server::getInstance()->broadcastPopup("§a[清理系统] 将在10秒后清除所有生物");
 }
 if($this->mtmp==400 && $this->Alertb==1)
 {
 Server::getInstance()->broadcastPopup("§a[清理系统] 即将进行世界清理!");
 }
 }

}

