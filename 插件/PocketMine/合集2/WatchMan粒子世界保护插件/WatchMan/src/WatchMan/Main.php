<?php
namespace WatchMan;
use pocketmine\nbt\tag\NamedTag;
use pocketmine\Server;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Utils\Config;
use pocketmine\Utils\TextFormat;
use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\entity\Effect;
use pocketmine\entity\Arrow;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\item\Tool;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\inventory;
use pocketmine\item\Item;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
use pocketmine\scheduler\CallbackTask;
use pocketmine\inventory\PlayerInventory;
use pocketmine\event\Cancellable;
use pocketmine\level\Level;
use pocketmine\scheduler\PluginTask;
use pocketmine\event\player\PlayerExperienceChangeEvent;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\block\Block;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
class Main extends PluginBase implements Listener {
	
		public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("WatchMan鍔犺浇鎴愬姛");
		$this->path = $this->getDataFolder();
		@mkdir($this->path);
		@mkdir($this->getDataFolder() . "players/",0777,true);
		$this->config = new Config($this->path."Config.yml", Config::YAML,array());
		}
		public function onCommand(CommandSender $sender,Command $cmd,$label,array $args){
			switch($cmd){
				case "setWM":
				if(isset($args[0])){
					$this->config->set($args[0],array(
					"effect" => "0",
					"attack" => true,
					"break" => true,
					"Particle" => true,
					"Place" => true,
					));
					$this->config->save();
					$sender->sendMessage("鎴愬姛");
				}
				$sender->sendMessage("11");
				break;
			}
		}
		public function Move(PlayerMoveEvent $event){
			$player = $event->getPlayer();
			$cfg=$this->config->get($player->getLevel()->getFolderName());
			if($cfg["Particle"]==true){
			$player->getLevel()->addParticle(new DestroyBlockParticle(new Vector3($player->getX(), $player->getY(), $player->getZ()), Block::get(152)));
			}
		}
		public function Attack(EntityDamageEvent $event){
			if($event instanceof EntityDamageByEntityEvent){
				$damager = $event->getDamager();
				if($damager instanceof Player){
					$cfg=$this->config->get($damager->getLevel()->getFolderName());
					if($cfg["Particle"]==true){
						$event->setDamage(0);
						$damager->sendMessage("[WatchMan]抱歉，这里不能攻击");
					}
				}
			}
		}
		public function Place(BlockPlaceEvent $event){
			if(!$event->getPlayer()->isOp()){
			$cfg=$this->config->get($event->getPlayer()->getLevel()->getFolderName());
			if($cfg["Particle"]==true){
				$event->setCancelled(true);
				$event->getPlayer()->sendMessage("[WatchMan守护者]抱歉，只有管理员才可以在此地放置东西");
			}
			}
		}
		public function Break(BlockBreakEvent $event){
			if(!$event->getPlayer()->isOp()){
			$cfg=$this->config->get($event->getPlayer()->getLevel()->getFolderName());
			if($cfg["Particle"]==true){
				$event->setCancelled(true);
				$event->getPlayer()->sendMessage("[WatchMam]鎶辨瓑锛屼綘鏃犳硶鐮村潖杩欑墖鍖哄煙");
			}
			}
		}
}