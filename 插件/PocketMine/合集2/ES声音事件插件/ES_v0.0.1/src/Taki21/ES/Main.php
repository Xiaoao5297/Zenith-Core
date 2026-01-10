<?php

namespace Taki21\ES;

// Sound Effects
use pocketmine\level\sound\AnvilBreakSound;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\level\sound\AnvilUseSound;
use pocketmine\level\sound\BatSound;
use pocketmine\level\sound\BlazeShootSound;
use pocketmine\level\sound\ButtonClickSound;
use pocketmine\level\sound\ButtonReturnSound;
use pocketmine\level\sound\ClickSound;
use pocketmine\level\sound\DoorBumpSound;
use pocketmine\level\sound\DoorCrashSound;
use pocketmine\level\sound\DoorSound;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\level\sound\FizzSound;
use pocketmine\level\sound\GhastShootSound;
use pocketmine\level\sound\GhastSound;
use pocketmine\level\sound\LaunchSound;
use pocketmine\level\sound\NoteblockSound;
use pocketmine\level\sound\PopSound;
use pocketmine\level\sound\ZombieHealSound;
use pocketmine\level\sound\ZombieInfectSound;
use pocketmine\level\sound\Sound;

// Plugin Utils and Misc
use pocketmine\Player;
use pocketmine\utils\TextFormat as C;
use pocketmine\plugin\PluginBase;

// Events
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerAchievementAwardedEvent;
use pocketmine\event\player\PlayerQuitEvent;


// Command
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class Main extends PluginBase implements Listener{
	
	public function onEnable(){
		
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		
		$this->getLogger()->info(C::GREEN."[EventSounds] Enabled!");
	}
	public function onJoin(PlayerJoinEvent $event){
		$pl = $event->getPlayer();
		$level = $pl->getLevel();
		$level->addSound(new PopSound($pl));
	}
	public function onDeath(PlayerDeathEvent $event){
		$pl = $event->getEntity();
		$level = $pl->getLevel();
		$level->addSound(new AnvilFallSound($pl));
	}
	public function onChat(PlayerChatEvent $event){
		$pl = $event->getPlayer();
		$level = $pl->getLevel();
		$level->addSound(new FizzSound($pl));
	}
	public function onAwardedAchievement(PlayerAchievementAwardedEvent $event){
		$pl = $event->getPlayer();
		$level = $pl->getLevel();
		$level->addSound(new NoteblockSound($pl));
	}
	public function onQuit(PlayerQuitEvent $event){
		$pl = $event->getPlayer();
		$s = $this->config->get("PlayerQuitEvent");
		$level = $pl->getLevel();
		$level->addSound(new EndermanTeleportSound($pl));
	}
	
	public function onCommand(CommandSender $s, Command $cmd, $label, array $args){
		if(strtolower($cmd->getName() == "playsounds")){
			if(!$s instanceof Player){
				$s->sendMessage(C::RED."请在游戏中运行!");
			}else{
				$level = $s->getLevel();
				$level->addSound(new AnvilBreakSound($s));
				$level->addSound(new AnvilFallSound($s));
				$level->addSound(new AnvilUseSound($s));
				$level->addSound(new BatSound($s));
				$level->addSound(new BlazeShootSound($s));
				$level->addSound(new ButtonClickSound($s));
				$level->addSound(new ButtonReturnSound($s));
				$level->addSound(new ClickSound($s));
				$level->addSound(new DoorBumpSound($s));
				$level->addSound(new DoorCrashSound($s));
				$level->addSound(new DoorSound($s));
				$level->addSound(new EndermanTeleportSound($s));
				$level->addSound(new FizzSound($s));
				$level->addSound(new GhastShootSound($s));
				$level->addSound(new GhastSound($s));
				$level->addSound(new LaunchSound($s));
				$level->addSound(new NoteblockSound($s));
				$level->addSound(new PopSound($s));
				$level->addSound(new ZombieHealSound($s));
				$level->addSound(new ZombieInfectSound($s));
			}
		}
		if(strtolower($cmd->getName() == "s1")){
			if(!$s instanceof Player){
				$s->sendMessage(C::RED."请在游戏中运行!");
			}else{
				$level = $s->getLevel();
				$level->addSound(new AnvilFallSound($s));
				$level->addSound(new AnvilFallSound($s));
				$level->addSound(new AnvilFallSound($s));
			}
		}
		if(strtolower($cmd->getName() == "s2")){
			if(!$s instanceof Player){
				$s->sendMessage(C::RED."请在游戏中运行!");
			}else{
				$level = $s->getLevel();
				$level->addSound(new BlazeShootSound($s));
				$level->addSound(new BlazeShootSound($s));
				$level->addSound(new BlazeShootSound($s));
			}
		}
		if(strtolower($cmd->getName() == "s3")){
			if(!$s instanceof Player){
				$s->sendMessage(C::RED."请在游戏中运行!");
			}else{
				$level = $s->getLevel();
				$level->addSound(new ButtonClickSound($s));
				$level->addSound(new ButtonClickSound($s));
				$level->addSound(new ButtonClickSound($s));
			}
		}
		if(strtolower($cmd->getName() == "s4")){
			if(!$s instanceof Player){
				$s->sendMessage(C::RED."请在游戏中运行!");
			}else{
				$level = $s->getLevel();
				$level->addSound(new DoorSound($s));
				$level->addSound(new DoorSound($s));
				$level->addSound(new DoorSound($s));
			}
		}
		if(strtolower($cmd->getName() == "s5")){
			if(!$s instanceof Player){
				$s->sendMessage(C::RED."请在游戏中运行!");
			}else{
				$level = $s->getLevel();
				$level->addSound(new EndermanTeleportSound($s));
				$level->addSound(new EndermanTeleportSound($s));
				$level->addSound(new EndermanTeleportSound($s));
			}
		}
		if(strtolower($cmd->getName() == "s6")){
			if(!$s instanceof Player){
				$s->sendMessage(C::RED."请在游戏中运行!");
			}else{
				$level = $s->getLevel();
				$level->addSound(new FizzSound($s));
				$level->addSound(new FizzSound($s));
				$level->addSound(new FizzSound($s));
			}
		}
		if(strtolower($cmd->getName() == "s7")){
			if(!$s instanceof Player){
				$s->sendMessage(C::RED."请在游戏中运行!");
			}else{
				$level = $s->getLevel();
				$level->addSound(new GhastShootSound($s));
				$level->addSound(new GhastShootSound($s));
				$level->addSound(new GhastShootSound($s));
			}
		}
		if(strtolower($cmd->getName() == "s8")){
			if(!$s instanceof Player){
				$s->sendMessage(C::RED."请在游戏中运行!");
			}else{
				$level = $s->getLevel();
				$level->addSound(new LaunchSound($s));
				$level->addSound(new LaunchSound($s));
				$level->addSound(new LaunchSound($s));
			}
		}
		if(strtolower($cmd->getName() == "s9")){
			if(!$s instanceof Player){
				$s->sendMessage(C::RED."请在游戏中运行!");
			}else{
				$level = $s->getLevel();
				$level->addSound(new NoteblockSound($s));
				$level->addSound(new NoteblockSound($s));
				$level->addSound(new NoteblockSound($s));
			}
		}
		if(strtolower($cmd->getName() == "s10")){
			if(!$s instanceof Player){
				$s->sendMessage(C::RED."请在游戏中运行!");
			}else{
				$level = $s->getLevel();
				$level->addSound(new PopSound($s));
				$level->addSound(new PopSound($s));
				$level->addSound(new PopSound($s));
			}
		}
		if(strtolower($cmd->getName() == "s11")){
			if(!$s instanceof Player){
				$s->sendMessage(C::RED."请在游戏中运行!");
			}else{
				$level = $s->getLevel();
				$level->addSound(new ZombieHealSound($s));
				$level->addSound(new ZombieHealSound($s));
				$level->addSound(new ZombieHealSound($s));
			}
		}
		return true;
	}
	
}