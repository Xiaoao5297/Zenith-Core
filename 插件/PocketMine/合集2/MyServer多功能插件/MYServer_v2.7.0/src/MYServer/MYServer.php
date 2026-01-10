<?php

namespace MYServer;

use pocketmine\plugin\Plugin;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\level\Position;
use pocketmine\utils\TextFormat;
use pocketmine\inventory\Inventory;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\server\ServerCommandEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\scheduler\CallbackTask;
use pocketmine\level\particle\ItemBreakParticle;
use pocketmine\level\sound\AnvilFallSound;

//♀♂∞

class MYServer extends PluginBase implements Listener {

	private $newplayer;
	private $pper = array();
	
	public function onLoad(){
		$this->path = $this->getDataFolder();
		@mkdir($this->path);
		$Config = new Config($this->path."Config.yml",Config::YAML,array(
		"新手箱子"=>false,
		"升级粒子"=>true,
		"禁止重复登入"=>true,
		"阵营创建钱数"=>30000,
		"Tip"=>"                                                                          §a<<§bServer§a>>§bQx§a>>\n                                                                          §a<<§b等级§a>>§7:§e dj \n                                                                          §a<<§b金币§a>>§7:§e money \n                                                                          §a<<§b情侣§a>>§7:§e LOVE \n                                                                          §a<<§b时间§a>>§7:§e timeH : timei : times \n                                                                          §a-------=======-------\n\n\n\n\n\n\n\n",
		"Popup"=>"§a<<§bServer§a>>§bQx§a>>§b时间§a>>§7:§e timeH : timei : times\n§a<<<§b等级§a>>>§7:§e dj §a<<<§b金币§a>>>§7:§e money §a<<<§b情侣§a>>>§7:§e LOVE"
		));
		$Config->save();
		@mkdir($this->path."/Players");
		$this->newplayer=$this->path."/Players/";
	}
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new CallbackTask([$this,"Popup"]),10);
		$this->getServer()->getLogger()->info("§7[§3MYServer§7] §aMYServer加载成功!");
	}

	private function randItem(): Item
 {
  $o = mt_rand(1, Item::$list->getSize());
  $i = Item::$list[$o];
  while (is_null($i) && count(explode("item", $i)) > 0) {
  $o = mt_rand(1, Item::$list->getSize());
  $i = Item::$list[$o];
  }
  return new Item($o);
 }

	public function onPlayerJoin(PlayerJoinEvent $event){
 	$player = $event->getPlayer();
 	$dj=$player->getXpLevel();
 	$id = $player->getName();
 	$player->setNameTag("§c>>§bLv.$dj §e{$id}§c<<");
 	$ip=$player->getAddress();
 	$level = $player->getLevel();
 	if(!file_exists($this->newplayer."$id.yml")){
	$p = new Config($this->newplayer."$id.yml", Config::YAML, array(
	"name"=>$id,
	"address"=>null,
	"password"=>null,
	"vip"=>array("vip"=>false,"viptime"=>0),
	"love"=>null,
	"links"=>2
	));
	$p->save();	
	$this->getServer()->getLogger()->notice(TextFormat::YELLOW."$id ".TextFormat::RED."第一次加入服务器,成功建立数据");	
		}
		$pp = new Config($this->newplayer."$id.yml", Config::YAML);
		$sip=$pp->get("address");
		$ttt=$pp->get("links");
		$OldTime=$pp->get("register-time");
		$NewTime=date("y-m-d");
		$this->pper[$id]["login"] = "off";
		$this->pper[$id]["MYLove"]=$pp->get("love");
		if($pp->get("vip")["vip"]){
		if($pp->get("vip")["viptime"]==0){
		$pp->set("vip",array("vip"=>false,"viptime"=>0));
		$pp->save();
		$player->setGamemode(0);
		$player->sendMessage("§7[§3VIP§7] §b尊敬的VIP玩家§6{$id}§b,您的VIP已到期!");
		}}
		
		if($pp->get("vip")["vip"]){
		if($NewTime == $OldTime){
		$player->sendMessage("§7[§3VIP§7] §b尊敬的VIP玩家§6{$id}§b,欢迎回到服务器!");
		}else{
		$NewVtime=($pp->get("vip")["viptime"])-1;
		$pp->set("vip",array("vip"=>true,"viptime"=>$NewVtime));
		$pp->set("register-time","$NewTime");
		$pp->save();
		$player->sendMessage("§7[§3VIP§7] §b尊敬的VIP§6{$id}§b,您的VIP还有§e{$NewVtime}§b天到期.");}
		$this->pper[$id]["vip"]="on";}
	if($ttt < "2"){
	if($sip == $ip){
  $this->getServer()->broadcastMessage(TextFormat::GOLD."$id.成功登入服务器");
  $this->pper[$id]["login"] = "on";
  }else{
  $pp->set("links",1);
  $pp->save();
  $player->sendMessage("§7[§3登陆验证§7] §6你需要使用你的密码进行登录哦~");
  }
  }
  if($ttt == "2"){
  $this->getServer()->getLogger()->notice(TextFormat::RED."$id.未注册账号");
  $player->sendMessage("§7[§3登陆验证§7] §6你是首次加入服务器，请直接设置你的密码哦~");
  }
		 if($player->getFloorY() <= 1){
		 $player->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
		}
	}	

	public function onCmdandChat(PlayerCommandPreprocessEvent $event){
		$player = $event->getPlayer();
		$level = $player->getLevel();
		$id = $player->getName();
		$m = $event->getMessage();
		$ip = $player->getAddress();
		$time = date("y-m-d");
		$pp =new Config($this->newplayer."$id.yml", CONFIG::YAML);
		$ttt=$pp->get("links");
		if($ttt !== 0){$event->setCancelled(true);}
 if($ttt == 2){
			$m=trim($m);
			$pp->set("password",$m);
			$pp->set("links",0);
			$pp->set("register-time","$time");
			$pp->save();
			$Config = new Config($this->path."Config.yml", Config::YAML);
			if($Config->get("新手箱子")){
			 	$v3 = new Vector3($Config->get("x"),$Config->get("y"),$Config->get("z"));
 	$player->getLevel()->setBlock($v3, new Block(Block::CHEST), true, true);
        $nbt = new \pocketmine\nbt\tag\CompoundTag("", [
        new \pocketmine\nbt\tag\ListTag("Items", []),
        new \pocketmine\nbt\tag\StringTag("id", \pocketmine\tile\Tile::CHEST),
        new \pocketmine\nbt\tag\IntTag("x", $v3->x),
        new \pocketmine\nbt\tag\IntTag("y", $v3->y),
        new \pocketmine\nbt\tag\IntTag("z", $v3->z)
        ]);
        $nbt->Items->setTagType(\pocketmine\nbt\NBT::TAG_Compound);
        $tile = \pocketmine\tile\Tile::createTile("Chest", $level->getChunk($v3->x >> 4, $v3->z >> 4), $nbt);
        if ($tile instanceof \pocketmine\tile\Chest) {
        for ($i = 0; $i <= mt_rand(1,27); $i++) {
        $item = $this->randItem();
        $tile->getInventory()->setItem($i, $item);
        }
			}
			$player->sendMessage("§7[§3登陆验证§7] §6您的密码设置为: §5$m ");
			$player->sendMessage("§7[§3登陆验证§7] §6注册完毕,给你生成了一个新手箱子呦~");
			$player->sendMessage("§7[§3Popup§7] §6输入指令/Popup 可以切换自己的底部信息哦~");
			$this->getServer()->broadcastMessage(TextFormat::GOLD."$id.成功登入服务器");
			}else{
			$player->sendMessage("§7[§3登陆验证§7] §6您的密码设置为: §5$m ");
			$player->sendMessage("§7[§3Popup§7] §6输入指令/Popup 可以切换自己的底部信息哦~");
			$this->getServer()->broadcastMessage(TextFormat::GOLD."$id.成功登入服务器");
			}
			$this->getServer()->getLogger()->notice(TextFormat::RED."$id.成功注册账号");
			$this->pper[$id]["login"] = "on";
			return;
			}
		$reg = $pp->get("password");
		if($ttt == 1){
		if(!$m == NULL){
		if($m == $reg){
		$pp->set("links",$ttt-1);
		$pp->set("address",$ip);
		$pp->save();
		$this->pper[$id]["login"] = "on";
		$this->getServer()->broadcastMessage(TextFormat::GOLD."$id.成功登入服务器");
		return;
 }else{
 return $player->sendMessage(TextFormat::RED."$id.你输入的密码有误!");
			}}}
        if($m == $reg){
        $player->sendMessage("[密码保护] ".TextFormat::YELLOW."你差点透露密码哦！");
	    $event->setCancelled(true);
		}
	}

//融合系统等级哈
 public function onChat(PlayerChatEvent $event){
		$event->setCancelled(true);
		$player = $event->getPlayer();
		$level = $player->getLevel()->getFolderName();
		$id = $player->getName();
		$p = new Config($this->newplayer."$id.yml", Config::YAML);
		if($player->isOp()){$Qx="§eAdmin";}else{$Qx="§bPlayer";}
		if($p->get("vip")["vip"]){$Qx="§cVIP";}
		$dj=$player->getXpLevel();
		$msg = "§5<§6{$level}§5> {$Qx}. §6{$id}§7>>§f ".$event->getMessage();
		$this->getServer()->broadcastMessage($msg);
	}

	public function Popup(){
	$Config = new Config($this->path."Config.yml", Config::YAML);
	foreach($this->getServer()->getOnlinePlayers() as $player){
	if($player->isOnline()){
	$id=$player->getName();
	if(!isset($this->pper[$id]["MYLove"]) or $this->pper[$id]["MYLove"]==null)
	$LOVE="没情侣";
	else
	$LOVE=$this->pper[$id]["MYLove"];
	if(!isset($this->pper[$id]["vip"]))
	$this->pper[$id]["vip"]="off";
	if($player->isOp())
	$Qx="Admin";
	else
	$Qx="Player";
	if($this->pper[$id]["vip"]=="on")
	$Qx="VIP";
	if(is_dir($this->getServer()->getPluginPath()."EconomyAPI"))
	{
	$money=$this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->myMoney($id);
	}else{
	$money="EconomyAPI";
	}
	$dj=$player->getXpLevel();
	$x=$player->getX();
	$y=$player->getY();
	$z=$player->getZ();
	$level=$player->getLevel();
	$this->pper[$id]["Newlevel"]=$dj;
	if(!isset($this->pper[$id]["Oldlevel"])){
	$this->pper[$id]["Oldlevel"]=$dj;
	}
	if($this->pper[$id]["Newlevel"]>$this->pper[$id]["Oldlevel"]){
	if($Config->get("升级粒子")){
	for($i=1;$i<=90;$i++){
 $G[1]=60;
 $G[2]=90;
 $G[3]=120;
 foreach($G as $e){
	$level->addParticle(new ItemBreakParticle(new Vector3($x+1*cos(($i-$e)*3.14/45),$y+1+cos($i*2),$z+1*sin(($i+$e)*3.14/45)),Item::get(266)));
	}
	}
	$level->addSound(new AnvilFallSound(new Vector3($x,$y,$z)));
	}
	$player->sendMessage("§7[§3提示§7] §e升级! §6Lv. {$dj}");
	$player->setNameTag("§c>>§bLv.$dj §e{$id}§c<<");
	$this->pper[$id]["Oldlevel"]=$dj;
	unset($x,$y,$z);
	}
	if($this->pper[$id]["Newlevel"]<$this->pper[$id]["Oldlevel"]){
	$player->sendMessage("§7[§3提示§7] §c掉级! 如果是死亡掉级,请联系管理员,请管理员打开死亡经验不掉落!");
	$player->setNameTag("§c>>§bLv.$dj §e{$id}§c<<");
	$this->pper[$id]["Oldlevel"]=$dj;
	}
	date_default_timezone_set('Asia/Chongqing');
	$H=date("H");
	$i=date("i");
	$s=date("s");
	$ServerName=$this->getServer()->getMotd();
	$msg=$Config->get("Popup");
	$msgt=$Config->get("Tip");
	$tip=str_replace(array("Server","Qx","dj","money","LOVE","timeH","timei","times"),array("$ServerName","$Qx","$dj","$money","$LOVE","$H","$i","$s"), $msgt);
	$pop = str_replace(array("Server","Qx","dj","money","LOVE","timeH","timei","times"),array("$ServerName","$Qx","$dj","$money","$LOVE","$H","$i","$s"), $msg);
	if(!isset($this->pper[$id]["Popup"])){
	$this->pper[$id]["Popup"]=1;
	}
	if($this->pper[$id]["Popup"]==1){
	if($player->isSurvival()){
	$player->sendPopup("\n$pop");
	}else{
	$player->sendPopup("$pop");
	}
	}
	if($this->pper[$id]["Popup"]==2){
	if($player->isSurvival()){
	$player->sendTip("$tip");
	}else{
	$player->sendTip("$tip");
	}}
	}}
	}
	
	public function inPlayerLogin(PlayerLoginEvent $event){
		$player = $event->getPlayer();
		$id = $player->getName();
		if($player->getFloorY() <= 1){
		 $player->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
		 $this->getServer()->getLogger()->notice(TextFormat::YELLOW."$id ".TextFormat::BLUE."被卡在虚空,系统传送到服务器出生点");	
		}
	}

	public function onPlayerPreLogin(PlayerPreLoginEvent $event){
	$Config = new Config($this->path."Config.yml", Config::YAML);
		$player = $event->getPlayer();
		$id = $player->getName();
		$ip = $player->getAddress();
		if($Config->get("禁止重复登入")){
		if(isset($this->pper[$id]["login"])){
		if($this->pper[$id]["login"] == "off" ){
		    return;}
		foreach($this->getServer()->getOnlinePlayers() as $p){
			if($p !== $player and $id === $p->getName()){
				if($this->pper[$id]["login"] == "on"){
					$event->setCancelled(true);
					$player->kick("禁止重复登入");
					return;}}}}}
	}
	public function onPlayerInteract(PlayerInteractEvent $event){
	    $this->permission($event);
	}		
	public function onBlockBreak(BlockBreakEvent $event){
		$this->permission($event);
	}	
	public function onEntityDamage(EntityDamageEvent $event){
		if($event->getEntity() instanceof Player){
			$id = $event->getEntity()->getName();
			if(isset($this->pper[$id]["login"])===false){$this->pper[$id]["login"]="off";}
		    if($this->pper[$id]["login"] == "off" ){	
			$event->setCancelled(true);}
		}
	}
	public function onBlockPlace(BlockPlaceEvent $event){
		$this->permission($event);
	}
	public function onPlayerDrop(PlayerDropItemEvent $event){
		$this->permission($event);
	}
	public function onPlayerItemConsume(PlayerItemConsumeEvent $event){
		$this->permission($event);
	}
	public function onPlayerMove(PlayerMoveEvent $event){
 $id = $event->getPlayer()->getName();
		if(!isset($this->pper[$id]["login"])){
			$this->pper[$id]["login"]="off";}
		if($this->pper[$id]["login"] == "off" ){
		$event->getPlayer()->sendTip("§e§oYou need to log in the game");
		$event->setCancelled(true);
		return false;
		}
	}
	public function onPickupItem(InventoryPickupItemEvent $event){
	foreach($this->getServer()->getOnlinePlayers() as $player){
	$id = $player->getName();
	if(!isset($this->pper[$id]["login"])){$this->pper[$id]["login"]=="off";}
		if($this->pper[$id]["login"] == "off"){
		$event->setCancelled(true);}
		}
	}

	public function onPlayerQuit(PlayerQuitEvent $event){
	$player = $event->getPlayer();
	$id = $player->getName();
	unset($this->pper[$id]);
	}
	public function permission($event){
		$player = $event->getPlayer();
		$id = $player->getName();
		if(isset($this->pper[$id]["login"]) === false){
		$this->pper[$id]["login"]="off";}
		if($this->pper[$id]["login"] == "off" ){
		$event->setCancelled(true);
		}
	}	
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
	$Config = new Config($this->path."Config.yml", Config::YAML);
		if($sender instanceof Player){
		$id = $sender->getName();
		$x = $sender->getFloorX();
 $y = $sender->getFloorY();
 $z = $sender->getFloorZ();
 $level = $sender->getLevel()->getFolderName();
		$pp =new Config($this->newplayer."$id.yml", CONFIG::YAML);
		if($command=="vip"){
		if(isset($args[0])){
		switch ($args[0]){
		case "add":
		if($sender->isOp()){
		$a=$args[1];
		if($a==null or $args[2]==null){
		$sender->sendMessage("§6用法: §b/vip add 玩家 天数");
		return true;
		}
		$vip=new Config($this->newplayer."$a.yml", CONFIG::YAML);
		$vip->set("vip",array("vip"=>true,"viptime"=>$args[2]));
		$vip->save();
		$this->pper[$a]["vip"]="on";
		$player=$this->getServer()->getPlayer($a);
		if($player->isOnline()){
		$player->sendMessage("§7[§3VIP§7] §aOP君§e{$id}§a为你设置了§e{$args[2]}§a天的§bVIP§a!");
		}
	$sender->sendMessage("§7[§3VIP§7] §a成功为§e{$a}§a添加了§e{$args[2]}§a天的§bVIP§a!");
		}else{
		$sender->sendMessage("§7[§3VIP§7] §c你没有权限这麽做!");
		}
		break;
		case "del":
		if($sender->isOp()){
		$a=$args[1];
		if($a==null){
		$sender->sendMessage("§6用法 §b/vip del 玩家");
		return true;
		}
		$vip=new Config($this->newplayer."$a.yml", CONFIG::YAML);
		$vip->set("vip",array("vip"=>false,"viptime"=>0));
		$vip->save();
		$this->pper[$a]["vip"]="off";
		$player=$this->getServer()->getPlayer($a);
		if($player->isOnline()){
		$player->sendMessage("§7[§3VIP§7] §aOP君§e{$id}§a删除了你的§bVIP§a!");
		}
		$sender->sendMessage("§7[§3VIP§7] §a成功删除{$a}的VIP!");
		}else{
		$sender->sendMessage("§7[§3VIP§7] §c你没有权限这麽做!");
		}
		break;
		case "help":
		$sender->sendMessage("§3_______§bVIP Help§3_______");
		if($sender->isOp()){
		$sender->sendMessage("§7=§c>> §b/vip add 玩家 天数");
		$sender->sendMessage("§7=§c>> §b/vip del 玩家");
		}
		$sender->sendMessage("§7=§c>> §b/vip gm <0/1/3>");
		$sender->sendMessage("§7=§c>> §b/vip tp 玩家");
		$sender->sendMessage("§7=§c>> §b/vip tp 玩家 玩家");
		break;
		case "gm":
		if($pp->get("vip")["vip"]){
		$sender->setGamemode($args[1]);
		$sender->sendMessage("§7[§3VIP§7] §a您的模式已更新!");
		}else{
		$sender->sendMessage("§7[§3VIP§7] §c你不是一个VIP!");
		}
		break;
		case "tp":
		if($pp->get("vip")["vip"]){
		if(!isset($args[1])){
		$sender->sendMessage("§6用法 §b/vip tp 玩家");
		return true;
		}
		$player=$this->getServer()->getPlayer($args[1]);
		if(isset($args[2])){
		$player2=$this->getServer()->getPlayer($args[2]);
		if($player2==null){$sender->sendMessage("§7[§3VIP§7] §c玩家§6{$args[2]}§c不在线!");return true;}
		if($player==null){$sender->sendMessage("§7[§3VIP§7] §c玩家§6{$args[1]}§c不在线!");return true;}
		$player->setLevel($player2->getLevel());
		$player->teleport(new Vector3($player2->getX(),$player2->getY(),$player2->getZ()));
		$sender->sendMessage("§7[§3VIP§7] §a你成功将玩家§6".$player->getName()."§a传送到玩家§6".$player2->getName()."§a的身边!");
		$player->sendMessage("§7[§3VIP§7] §bVIP玩家§6{$id}§b将你强制传送到玩家§6".$player2->getName()."§b的身边!");
		$player2->sendMessage("§7[§3VIP§7] §bVIP玩家§6{$id}§b将玩家§6".$player->getName()."§b强制传送到你的身边!");
		return true;
		}
		if($player==null){
		$sender->sendMessage("§7[§3VIP§7] §c玩家§6{$args[1]}§c不在线!");
		}else{
		$sender->setLevel($player->getLevel());
		$sender->teleport(new Vector3($player->getX(),$player->getY(),$player->getZ()));
		$sender->sendMessage("§7[§3VIP§7] §a已将你传送到玩家§6".$player->getName()."§a的身边!");
		$player->sendMessage("§7[§3VIP§7] §bVIP玩家§6{$id}§b已强制传送到你身边.");
		}
		}else{
		$sender->sendMessage("§7[§3VIP§7] §c你不是一个VIP!");
		}
		break;
		}
		}else{
		$sender->sendMessage("§6用法 §b/vip help");
		return true;
		}
		return true;
		}
		if($command=="结婚"){
		if(isset($args[0])){
		switch ($args[0]){
		case "求婚":
		if(isset($args[1])){
		$player=$this->getServer()->getPlayer($args[1]);
		if($player==null){
		$sender->sendMessage("§7[§3LOVE§7] §c玩家§6{$args[1]}§c不在线!");
		return true;
		}
		if(isset($this->pper[$args[1]]["love"])){
		$sender->sendMessage("§7[§3LOVE§7] §c已经有人和§6{$args[1]}§c求婚了,等§6{$args[1]}§c拒绝后,你才可以求婚!");
		return true;
		}else{
		$this->pper[$args[1]]["love"]=$id;
		$sender->sendMessage("§7[§3LOVE§7] §b你成功向§6{$args[1]}§b求婚了,请等待§6{$args[1]}§b的答复吧!");
		$player->sendMessage("§7[§3LOVE§7] §b玩家§6{$id}§b向你求婚了!");
		$player->sendMessage("§7[§3LOVE§7] §b接受求婚请输入/结婚 接受");
		$player->sendMessage("§7[§3LOVE§7] §b拒绝求婚请输入/结婚 拒绝");
		return true;
		}	
		}else{
		$sender->sendMessage("§6用法 §b/结婚 求婚 玩家");
		return true;
		}
		break;
		case "离婚":
		if($pp->get("love")==null){
		$sender->sendMessage("§7[§3LOVE§7] §b身为单身汪的你,能和谁离婚?");
		return true;
		}else{
		$PName=$pp->get("love");
		$player=$this->getServer()->getPlayer($PName);
		if($player==null){
		$sender->sendMessage("§7[§3LOVE§7] §c玩家§6{$PName}§c不在线!");
		return true;
		}
		$pp2=new Config($this->newplayer."$PName.yml", CONFIG::YAML);
		$pp2->set("love",null);
		$pp2->save();
		$this->pper[$PName]["MYLove"]=$pp2->get("love");
		$pp->set("love",null);
		$pp->save();
		$this->pper[$id]["MYLove"]=$pp->get("love");
		$sender->sendMessage("§7[§3LOVE§7] §b你已经成功和玩家§6{$PName}§b离婚了.");
		$player->sendMessage("§7[§3LOVE§7] §b可怜的§6{$PName}§b,你被§6{$id}§b狠心的抛弃了.");
		return true;
		}
		break;
		case "接受":
		if(isset($this->pper[$id]["love"])){
		$PName=$this->pper[$id]["love"];
		$player=$this->getServer()->getPlayer($PName);
		$pp2=new Config($this->newplayer."$PName.yml", CONFIG::YAML);
		if($pp2->get("love")==null){
		$pp2->set("love",$id);
		$pp2->save();
		$this->pper[$PName]["MYLove"]=$pp2->get("love");
		$pp->set("love",$PName);
		$pp->save();
		$this->pper[$id]["MYLove"]=$pp->get("love");
		$sender->sendMessage("§7[§3LOVE§7] §b你和§6{$PName}§b结婚了,由衷的祝你们幸福!");
		$player->sendMessage("§7[§3LOVE§7] §b玩家§6{$id}§b答应了你的求婚!祝你们幸福!");
		return true;
		}else{
		$sender->sendMessage("§7[§3LOVE§7] §c你接受的晚了，玩家§6{$PName}§c和玩家§6".$pp2->get("love")."§c勾搭上了!");
		return true;
		}
		}else{
		$sender->sendMessage("§7[§3LOVE§7] §b没人和你求婚,别自作多情了.");
		return true;
		}
		break;
		case "拒绝":
		if(isset($this->pper[$id]["love"])){
		$PName=$this->pper[$id]["love"];
		$player=$this->getServer()->getPlayer($PName);
		unset($this->pper[$id]["love"]);
		$sender->sendMessage("§7[§3LOVE§7] §b你成功拒绝了玩家§6{$PName}§b的求婚,单身快乐!");
		$player->sendMessage("§7[§3LOVE§7] §b玩家§6{$id}§b拒绝了你的求婚,糗死了,2333!");
		return true;
		}else{
		$sender->sendMessage("§7[§3LOVE§7] §b没人和你求婚,别自作多情了.");
		return true;
		}
		break;
		case "tp":
		$PName=$pp->get("love");
		if($PName==null){
		$sender->sendMessage("§7[§3LOVE§7] §c你没有结婚,无法传送到你的爱人身边!");
		return true;
		}else{
		$player=$player=$this->getServer()->getPlayer($PName);
		if($player==null){
		$sender->sendMessage("§7[§3LOVE§7] §c您的爱人不在线!");
		return true;
		}else{
		$sender->setLevel($player->getLevel());
		$sender->teleport(new Vector3($player->getX(),$player->getY(),$player->getZ()));
		$sender->sendMessage("§7[§3LOVE§7] §b成功传送到你的爱人身边!");
		$player->sendMessage("§7[§3LOVE§7] §b你的爱人通过结婚tp传送到你身边!");
		return true;
		}
		}
		break;
		case "help":
		$sender->sendMessage("§3_______§bLove Help§3_______");
		$sender->sendMessage("§7=§c>> §b/结婚 求婚 玩家");
		$sender->sendMessage("§7=§c>> §b/结婚 离婚 玩家");
		$sender->sendMessage("§7=§c>> §b/结婚 接受");
		$sender->sendMessage("§7=§c>> §b/结婚 拒绝");
		$sender->sendMessage("§7=§c>> §b/结婚 tp");
		break;
		return true;
		}
		}else{
		$sender->sendMessage("§6用法 §b/结婚 help");
		return true;
		}
		return true;
		}
		if($command=="新手箱子"){
		if($Config->get("新手箱子")){
		$Config->set("新手箱子",false);
		$Config->save();
		$sender->sendMessage("§7[§3新手箱子§7] §a成功关闭新手箱子");
		return true;
		}else{
		$Config->set("新手箱子",true);
		$x=$sender->getFloorX();
		$y=$sender->getFloorY();
		$z=$sender->getFloorZ();
		$Config->set("x",$x);
		$Config->set("y",$y);
		$Config->set("z",$z);
		$Config->save();
		$sender->sendMessage("§7[§3新手箱子§7] §a成功开启新手箱子, 坐标为$x,$y,$z");
		return true;
		}
		}
		if($command=="升级粒子"){
		if($Config->get("升级粒子")){
		$Config->set("升级粒子",false);
		$Config->save();
		$sender->sendMessage("§7[§3升级粒子§7] §a成功将服务器玩家升级粒子关闭");
		return true;
		}else{
		$Config->set("升级粒子",true);
		$x=$sender->getFloorX();
		$y=$sender->getFloorY();
		$z=$sender->getFloorZ();
		$Config->set("x",$x);
		$Config->set("y",$y);
		$Config->set("z",$z);
		$Config->save();
		$sender->sendMessage("§7[§3升级粒子§7] §a成功为服务器玩家开始升级粒子");
		return true;
		}
		}
		if($command=="Popup"){
		if($this->pper[$id]["Popup"]==0){
		$this->pper[$id]["Popup"]=1;
		$sender->sendMessage("§7[§3信息显示§7] §a成功切换为Popup!");
		return true;}
		if($this->pper[$id]["Popup"]==1){
		$this->pper[$id]["Popup"]=2;
		$sender->sendMessage("§7[§3信息显示§7] §a成功切换为Tip!");
		return true;}
		if($this->pper[$id]["Popup"]==2){
		$this->pper[$id]["Popup"]=0;
		$sender->sendMessage("§7[§3信息显示§7] §a成功关闭信息显示!");
		return true;}
		}
		if($command=="改密码"){
		if(isset($args[1])){
		$y=$args[0];
		$n=$args[1];
		$reg=$pp->get("password");
		if($y==$reg){
		$pp->set("password",$n);
		$pp->save();
		$sender->sendMessage("§7[§3改密码§7] §6{$id}§a你成功修改账号密码为:§5{$n}");
		}else{
		$sender->sendMessage("§7[§3改密码§7] §a对不起, §6{$id}§a你输入的原密码有误!");}
		}else{
		$sender->sendMessage("§6用法 §b/改密码 旧密码 新密码");
		}
		return true;
		}
		}
		}
}




















