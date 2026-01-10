<?php

namespace WHint;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\level\Level;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerChatEvent;
use WHint\Main;

/*
作者：Wshape1
也就是. 吾形~
百度贴吧ID.  奇妙幻想术
. 欢迎学习 .
有BUG可在原帖反馈.
修改神马请保持原作者名.
作者QQ 737328634
请注明来历...
*/



class Main extends PluginBase implements Listener{


 public function onEnable(){
$this->getLogger()->info(TextFormat::YELLOW."--------------------");
$this->getLogger()->info(TextFormat::BLUE."作者：Wshape1 (吾形)");
$this->getLogger()->info(TextFormat::GREEN."【WHint】感谢使用...");
$this->getLogger()->info(TextFormat::YELLOW."--------------------");
@mkdir($this->getDataFolder(),0777,true);
		@mkdir($this->getDataFolder());
 $this->Config = new Config($this->getDataFolder()."Config.yml", Config::YAML, array(
 "#JoinTip"=>"#进入服务器中间（Tip）提示",
                "进退服中间提示"=>"服务器" ,
 "#头部显示&聊天美化"=>"#设置在下面。别看我...",
                "OP"=>"OP",
                "Player"=>"Player",
				"创造"=>"创造",
				"生存"=>"生存",
 "#JoinMsg"=>"#OP&Player进退服全服公告...",
				"JQOP"=>"§1OP◆先生",
				"OP进服全服公告"=>"§c进入§aMinecraftPE服务器！\n §c有事可以请教他哦!" ,
				"OP退服全服公告"=>"§c退出§aMinecraftPE服务器！",
				"玩家进服欢迎语"=>"§e欢迎进入MinecraftPE服务器",
 ));

		$this->getServer()->getPluginManager()->registerEvents( $this , $this );
}



public function onJoin(PlayerJoinEvent $event){
	
	$event->setJoinMessage(""); //覆盖系统的进入提示.
	
   $player=$event->getPlayer();
   $name=$player->getName();//玩家名字
   $h=$player->getHealth();//获取现在生命值
   $mh=$player->getMaxHealth();//获取总有的生命值
   $world=$player->getLevel()->getName();//获取世界


   $tip=$this->Config->get("进退服中间提示");
   $wop=$this->Config->get("OP");
   $wcz=$this->Config->get("创造");
   $wsc=$this->Config->get("生存");
   $bmopp=$this->Config->get("JQOP");
   $bmop=$this->Config->get("OP进服全服公告");
   $bmop1=$this->Config->get("OP退服全服公告");
   $pj=$this->Config->get("玩家进服欢迎语");
   $wj=$this->Config->get("Player");
   
      		if($player->isOp()){  //检测是不是op
			$op="".$wop."";  //是op的
		}else{
			$op="".$wj."";   //不是op的
		}
		$gm=$player->getGamemode();  //检测玩家游戏模式
		if($gm==0){  //是生存模式
			$gm="".$wsc."";  //
		}
			if($gm==1){  //是创造模式
				$gm="".$wcz."";  //
		}
		
$player->setDisplayName("§a".$world.">§4[§1".$op."§4]<§b".$name."§4>§6>§7");//聊天名字显示...
$this->getServer()->broadcastTip("§6--§4[ §d".$name." §4]§a进入".$tip."§6--");// 玩家进入中间(Tip)提示
if($player->isOp()){
$this->getServer()->broadcastMessage("§4[".$bmopp."§4]<".$name."§4>".$bmop."");//op进入信息提示
$player->setNameTag("§1".$wop."§e>§6".$name."\n §1>§4".$h."§a/§4".$mh."§1<>§b".$wj."§1<");//头部显示
$player->sendMessage("".$pj."");//玩家进服欢迎语
}else{
	$player->setNameTag("§1".$gm."§e>§6".$name."\n §1>§4".$h."§a/§4".$mh."§1<>§b".$wj."§1<");//头部显示
	$player->sendMessage("".$pj.""); //玩家进服欢迎语
}
}
public function onQuit(PlayerQuitEvent $event){
	
	$event->setQuitMessage(""); //覆盖系统的退出提示.
	
	$player=$event->getPlayer();
	$name=$player->getName();//玩家名字
	
	$bmop1=$this->Config->get("OP退服全服公告");
	$tip=$this->Config->get("进退服中间提示");
	$bmopp=$this->Config->get("JQOP");
	
	$this->getServer()->broadcastTip("/n§6--§4[ §d".$name." §4]§a退出".$tip."§6--");//玩家退服中间(Tip)提示
	if($player->isOp()){
	$this->getServer()->broadcastMessage("§4[".$bmopp."§4]<".$name."§4>".$bmop1."");//op退出信息提示
}
}
}