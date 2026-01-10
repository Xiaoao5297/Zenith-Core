<?php

namespace HomeWithPiZzle;


use pocketmine\level\sound\ClickSound;
use pocketmine\level\sound\DoorSound;
use pocketmine\level\sound\LaunchSound;
use pocketmine\level\sound\PopSound;
use pocketmine\entity\Effect;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\math\Vector3;
use pocketmine\scheduler\PluginTask;
//use pocketmine\scheduler\CallbackTask;
//use wallway\CallbackTask;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as CL;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\level\Level;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\block\Block;
use pocketmine\block\Air;
use pocketmine\block\Sand;
//use pocketmine\block\ice;
use pocketmine\OfflinePlayer;
use pocketmine\utils\Config;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\tile\Sign;
use pocketmine\tile\Tile;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;
use onebone\economyapi\EconomyAPI;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerMoveEvent;
//use pocketmine\event\block\BlockBreakEvent;
use pocketmine\utils\Utils;
//use pocketmine\scheduler\CallbackTask;
use HomeWithPiZzle\task\CallbackTask;
class ClickGame extends PluginBase implements Listener
{
	public function onEnable(){
	    $this->getServer()->getPluginManager()->registerEvents($this, $this);
		@mkdir($this->getDataFolder(), 0777, true);
		$this->cfg = new Config($this->getDataFolder() . "Config.yml", Config::YAML, array());
		$this->gamestart = false;
		$this->shijian2 = 10;
		$this->css = 0;
		$this->cx = 4;
		$this->players = array();
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new CallbackTask([$this,"jishi"]),20);
	    $this->getLogger()->info(CL::GREEN.""); 
        $this->getLogger()->info(CL::GREEN."| 极速点击 插件加载完成，本插件由极致·人生优化 |");
        $this->getLogger()->info(CL::GREEN."| 作者 HomeNear  && PIZZLE |");
        $this->getLogger()->info(CL::GREEN."");
		$this->getLogger()->info(CL::GOLD."");
        $this->getLogger()->info(CL::GREEN.""); 
		if($this->cfg->exists("block")){
			$this->getLogger()->info(CL::GOLD."");
			$this->csign();
			$this->cblock();
		}
    }
	public function jishi(){
		if($this->gamestart){
			$p11=$this->getServer()->getPlayer($this->players['id']);
			switch($this->shijian2){
				case"10":
				$this->shijian2--;
				$p11->sendTip(CL::GREEN."§9你还有".($this->shijian2)."秒");
				break;
				case"9":
				$this->shijian2--;
				$p11->sendTip(CL::GREEN."§9你还有".($this->shijian2)."秒");
				break;
				case"8":
				$this->shijian2--;
				$p11->sendTip(CL::GREEN."§9你还有".($this->shijian2)."秒");
				break;
				case"7":
				$this->shijian2--;
				$p11->sendTip(CL::GREEN."§9你还有".($this->shijian2)."秒");
				break;
				case"6":
				$this->shijian2--;
				$p11->sendTip(CL::GREEN."§9你还有".($this->shijian2)."秒");
				break;
				case"5":
				$this->shijian2--;
				$p11->sendTip(CL::GREEN."§9你还有".($this->shijian2)."秒");
				break;
				case"4":
				$this->shijian2--;
				$p11->sendTip(CL::GREEN."§9你还有".($this->shijian2)."秒");
				break;
				case"3":
				$this->shijian2--;
				$p11->sendTip(CL::GREEN."§9你还有".($this->shijian2)."秒");
				break;
				case"2":
				$this->shijian2--;
				$p11->sendTip(CL::GREEN."§9你还有".($this->shijian2)."秒");
				break;
				case"1":
				$this->shijian2--;
				$p11->sendTip(CL::GREEN."§9你还有".($this->shijian2)."秒");
				break;
				case"0":
				$this->csign1();
				$this->shijian2 = 10;
				$this->gamestart = false;
				$this->cx = 4;
				$p11->sendTip(CL::GREEN."§9[极速点击] 游戏结束!");
				$p11->sendMessage(CL::GREEN."§9[极速点击] 你一共点击了".($this->css)."次.");
				$this->players = array();
				$this->css = 0;
				$this->csign();
				break;
				unset($p11);
			}
		}
	}
	public function oncommand(CommandSender $s, Command $c, $label, array $args){
		$sn=$s->getName();
		if($c->getName() == "click"){
			if(isset($args[0])){
				if($s->isOp()){
				    switch($args[0]){
					    case"set":
						if(!$this->cfg->exists("sign")){
						    $s->sendMessage(CL::GREEN."§9[极速点击] 已进入设置状态,请先设置状态木牌!");
							$this->cx = 1;
						}else{
							$s->sendMessage(CL::GREEN."§9[极速点击] 游戏已设置.");
						}
						break;
						case"remove":
						if(!$this->cfg->exists("sign")){
							$s->sendMessage(CL::GREEN."§9[极速点击] 游戏未设置.");
						}else{
							$s->sendMessage(CL::GREEN."§9[极速点击] 删除成功!");
							$this->cx = 0;
							$this->gamestart = false;
							$this->css = 0;
							$this->shuliang = 0;
							$this->cfg->setAll(array());
							$this->cfg->save();
							$this->shijian2 = 10;
						}
					}
				}else{
					$s->sendMessage(CL::GREEN."§c[极速点击] 你没有权限执行此操作!");
				}
			}
		}
	}
	public function playerBlockTouch(PlayerInteractEvent $e){
		$p=$e->getPlayer();
		$pn=$p->getName();
		$block=$e->getBlock();
		$xx=$block->getX();
		$yy=$block->getY();
		$zz=$block->getZ();
		$level=$block->getLevel();
		switch($this->cx){
			case"1":
			if($p->isOp()){
				$sign=$e->getPlayer()->getLevel()->getTile($e->getBlock());
				if($sign instanceof Sign){
				    $p->sendMessage(CL::GREEN."§9[极速点击] 状态木牌已设置,请点击排行榜木牌.");
				    $this->sign111 = array(
					    "x" =>$xx,
						"y" =>$yy,
						"z" =>$zz,
						"level" =>$level->getFolderName());
					$this->cfg->set("sign",$this->sign111);
					$this->cfg->save();
					$this->cx++;
				}else{
					$p->sendMessage(CL::GREEN."§9[极速点击] 请点击一个方块作为游戏方块.");
				}
			}
			break;
			case"2":
			if($p->isOp()){
				$sign=$e->getPlayer()->getLevel()->getTile($e->getBlock());
				if($sign instanceof Sign){
				    $p->sendMessage(CL::GREEN."§9[极速点击] 排行榜木牌已设置完成,请点击一个方块作为游戏方块.");
					$sign->setText("§9[极速点击]","§9最高纪录","§9当前玩家: 无" ,"");
				    $this->sign112 = array(
					    "x" =>$xx,
						"y" =>$yy,
						"z" =>$zz,
						"level" =>$level->getFolderName());
					$this->cfg->set("sign1",$this->sign112);
					$this->cfg->save();
					$this->cx++;
				}else{
					$p->sendMessage(CL::GREEN."§9[极速点击] 请点击一个方块作为游戏方块.");
				}
			}
			break;
			case"3":
			if($p->isOp()){
				$p->sendMessage(CL::GREEN."§9[极速点击] 游戏设置已完成,可以开始游戏了!");
				$level->setBlock(new Vector3($xx,$yy,$zz), new block(17,4));
				$this->block111 = array(
					"x" =>$xx,
					"y" =>$yy,
					"z" =>$zz,
					"level" =>$level->getFolderName());
				$this->cfg->set("block",$this->block111);
				$this->cfg->save();
				$this->cx++;
				$this->csign();
			}
			break;
			case"4":
			if(!$this->gamestart){
				if($this->cfg->exists("sign")){
					$this->sign1 = $this->cfg->get("sign");
			        $x1=$this->sign1['x'];
			        $y1=$this->sign1['y'];
			        $z1=$this->sign1['z'];
					$leveln=$this->sign1['level'];
					if($xx == $x1 and $yy = $y1 and $zz == $z1 and $level->getFolderName() == $leveln){
						$p->sendMessage(CL::GREEN."§9[极速点击] 你已加入游戏!");
					    $this->gamestart = true;
					    $this->players = array('id' =>$pn);
					    $this->csign();
						$this->cx++;
					}
				}
			}
			break;
			case"5":
			if($this->gamestart){
				$this->block1 = $this->cfg->get("block");
				$x1=$this->block1['x'];
			    $y1=$this->block1['y'];
			    $z1=$this->block1['z'];
			    $leveln=$this->block1['level'];
				if($xx == $x1 and $yy = $y1 and $zz == $z1 and $level->getFolderName() == $leveln){
					if($pn == $this->players['id']){
						$this->css++;
						$p->sendMessage(CL::GREEN."§9[极速点击] 你一共点了".($this->css)."次.");
					}
				}
			}
			break;
		}
	}
	public function csign(){
		if($this->cfg->exists("sign")){
			$this->sign0=$this->cfg->get("sign");
			$x=$this->sign0['x'];
			$y=$this->sign0['y'];
			$z=$this->sign0['z'];
			$leveln=$this->sign0['level'];
			$sign=$this->getServer()->getLevelByName($leveln)->getTile(new Vector3($x,$y,$z));
			if($sign instanceof Sign){
				switch(count($this->players)){
					case"0":
					$sign->setText("§9[极速点击]","§9点击加入","§9当前玩家: 无" ,"");
					break;
					case"1":
					$sign->setText("§9[极速点击]","§9游戏开始","§9当前玩家:".($this->players['id']) ,"");
					break;
				}
			}
			unset($sign);
		}
	}
	public function cblock(){
		if($this->cfg->exists("block")){
			$this->block1 = $this->cfg->get("block");
			$x1=$this->block1['x'];
			$y1=$this->block1['y'];
			$z1=$this->block1['z'];
			$leveln=$this->block1['level'];
			$level=$this->getServer()->getLevelByName($leveln);
			$level->setBlock(new Vector3($x1,$y1,$z1),new block(17,4));
			unset($x1,$y1,$z1,$leveln,$level);
		}
	}
	public function csign1(){
		if($this->cfg->exists("sign1")){
			$this->sign1=$this->cfg->get("sign1");
			$x=$this->sign1['x'];
			$y=$this->sign1['y'];
			$z=$this->sign1['z'];
			$leveln=$this->sign1['level'];
			$sign=$this->getServer()->getLevelByName($leveln)->getTile(new Vector3($x,$y,$z));
			$signtext=$sign->getText();
			if($sign instanceof Sign){
				if($this->css > $signtext[3]){
					$sign->setText("§9[极速点击]","§9最高纪录","§9玩家:".($this->players['id']) ,($this->css));
					Server::getInstance()->broadcastMessage(CL::YELLOW."§9[极速点击] ".($this->players['id'])."获得了极速点击第一名!");
				}
			}
			unset($x,$y,$z,$leveln,$sign);
		}
	}
}
?>