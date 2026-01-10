<?php

namespace FactionsPro;

/*
 * 
 * 
 * 
 */

use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\utils\TextFormat;
use pocketmine\scheduler\PluginTask;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;


class Main extends PluginBase implements Listener {
	
	public $db;
	
	public function onEnable() {
		@mkdir($this->getDataFolder());
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->db = new \SQLite3($this->getDataFolder() . "FactionsPro.db");
		$this->db->exec("CREATE TABLE IF NOT EXISTS master (player TEXT PRIMARY KEY COLLATE NOCASE, faction TEXT, rank TEXT);");
		$this->db->exec("CREATE TABLE IF NOT EXISTS confirm (player TEXT PRIMARY KEY COLLATE NOCASE, faction TEXT, invitedby TEXT, timestamp INT);");
		$this->db->exec("CREATE TABLE IF NOT EXISTS motdrcv (player TEXT PRIMARY KEY, timestamp INT);");
		$this->db->exec("CREATE TABLE IF NOT EXISTS motd (faction TEXT PRIMARY KEY, message TEXT);");
		}
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
		if($sender instanceof Player) {
			$player = $sender->getPlayer()->getName();
			if(strtolower($command->getName('公会'))) {
				// CREATE
				if(empty($args)) {
					$sender->sendMessage("[公会管理] 请使用 /公会 帮助 来列出所有公会相关命令");
				}
				if(count($args) == 2) {
					if($args[0] == "创建") {
						if($this->factionExists($args[1]) == true ) {
							$sender->sendMessage("[提示] 公会已经存在");
							return true;
						}
						if($this->isInFaction($sender->getPlayer())) {
							$sender->sendMessage("[提示] 你必须先离开公会");
						} else {
							
							//Player Data
							$factionName = $args[1];
							$player = strtolower($player);
							$rank = "Leader";
							//Create row in FactionsPro.db for player
							$stmt = $this->db->prepare("INSERT OR REPLACE INTO master (player, faction, rank) VALUES (:player, :faction, :rank);");
							$stmt->bindValue(":player", $player);
							$stmt->bindValue(":faction", $factionName);
							$stmt->bindValue(":rank", $rank);
							$result = $stmt->execute();
							$sender->sendMessage("[恭喜] 公会创建成功 !");
							return true;
						}
					}
					if($args[0] == "邀请") {
						$invited = $this->getServer()->getPlayerExact($args[1]);
						if($this->isInFaction($invited) == true) {
							$sender->sendMessage("[提示] 玩家已经和你在同一个公会");
							return true;
						}
						if(!$invited instanceof Player) {
							$sender->sendMessage("[提示] 玩家不在线 !");
							return true;
						}
						if($invited->isOnline() == true) {
							$factionName = $this->getPlayerFaction($player);
							$invitedName = $invited->getName();
							$rank = "Member";
							
							$stmt = $this->db->prepare("INSERT OR REPLACE INTO confirm (player, faction, invitedby, timestamp) VALUES (:player, :faction, :invitedby, :timestamp);");
							$stmt->bindValue(":player", strtolower($invitedName));
							$stmt->bindValue(":faction", $factionName);
							$stmt->bindValue(":invitedby", $sender->getName());
							$stmt->bindValue(":timestamp", time());
							$result = $stmt->execute();

							$sender->sendMessage("[提示] 邀请 $invitedName 成功 !");
							$invited->sendMessage("[提示] 你被邀请去 $factionName 公会. 输入 '/公会 接受' 接受邀请 或者输入 '/公会 拒绝' 拒绝邀请 !");
						} else {
							$sender->sendMessage("[提示] 玩家不在线 !");
						}
					}
					if($args[0] == "会长") {
						if($this->isInFaction($sender->getName()) == true) {
							if($this->isLeader($player) == true) {
								if($this->getPlayerFaction($player) == $this->getPlayerFaction($args[1])) {
									if($this->getServer()->getPlayerExact($args[1])->isOnline() == true) {
										$factionName = $this->getPlayerFaction($player);
										$factionName = $this->getPlayerFaction($player);
										
										$stmt = $this->db->prepare("INSERT OR REPLACE INTO master (player, faction, rank) VALUES (:player, :faction, :rank);");
										$stmt->bindValue(":player", $sender->getPlayer()->getName());
										$stmt->bindValue(":faction", $factionName);
										$stmt->bindValue(":rank", "Member");
										$result = $stmt->execute();
										
										$stmt = $this->db->prepare("INSERT OR REPLACE INTO master (player, faction, rank) VALUES (:player, :faction, :rank);");
										$stmt->bindValue(":player", strtolower($args[1]));
										$stmt->bindValue(":faction", $factionName);
										$stmt->bindValue(":rank", "Leader");
										$result = $stmt->execute();
										
										
										$sender->sendMessage("[提示] 你不再是会长 !");
										$this->getServer()->getPlayerExact($args[1])->sendMessage("[提示] 你现在成为了 $factionName 的会长 !");
									} else {
										$sender->sendMessage("[提示] 玩家不在线 !");
									}
								} else {
									$sender->sendMessage("[提示] 请先给公会添加会员 !");
								}
							} else {
								$sender->sendMessage("[提示] 你必须是会长才能使用这个");
							}
						} else {
							$sender->sendMessage("[提示] 你必须加入一个公会才能使用这个 !");
						}
					}
					if($args[0] == "踢人") {
						if($this->isInFaction($sender->getName()) == false) {
							$sender->sendMessage("[提示] 你必须加入一个公会才能使用这个!");
							return true;
						}
						if($this->isLeader($player) == false) {
							$sender->sendMessage("[提示] 你必须是会长才能使用这个");
							return true;
						}
						if($this->getPlayerFaction($player) != $this->getPlayerFaction($args[1])) {
							$sender->sendMessage("[提示] 玩家不在公会中 !");
							return true;
						}
						$kicked = $this->getServer()->getPlayerExact($args[1]);
						$factionName = $this->getPlayerFaction($player);
						$this->db->query("DELETE FROM master WHERE player='$args[1]';");
						$sender->sendMessage("[提示] 你成功踢出 $args[1]!");
						$players[] = $this->getServer()->getOnlinePlayers();
						if(in_array($args[1], $players) == true) {
							$this->getServer()->getPlayerExact($args[1])->sendMessage("[提示] 你被踢出公会 $factionName !");	
							return true;
						}
					}	
				}
				if(count($args) == 1) {
					
					if(strtolower($args[0]) == "公告") {
						if($this->isInFaction($sender->getName()) == false) {
							$sender->sendMessage("[提示] 你必须加入一个公会才能使用这个 !");
							return true;
						}
						if($this->isLeader($player) == false) {
							$sender->sendMessage("[提示] 你必须是会长才能使用这个");
							return true;
						}
						$sender->sendMessage("[提示] 输入你的公告信息在聊天框中 . 它不会被其他玩家看见");
						$stmt = $this->db->prepare("INSERT OR REPLACE INTO motdrcv (player, timestamp) VALUES (:player, :timestamp);");
						$stmt->bindValue(":player", strtolower($sender->getName()));
						$stmt->bindValue(":timestamp", time());
						$result = $stmt->execute();
					}
					
					if(strtolower($args[0]) == "信息") {
						$faction = $this->getPlayerFaction(strtolower($sender->getName()));
						$result = $this->db->query("SELECT * FROM motd WHERE faction='$faction';");
						$array = $result->fetchArray(SQLITE3_ASSOC);
						$message = $array["message"];
						$sender->sendMessage("-------------------------");
						$sender->sendMessage("公会 $faction 的公告信息如下 :");
						$sender->sendMessage("$message");
						$sender->sendMessage("-------------------------");
					}
					
					if(strtolower($args[0]) == "接受") {
						$player = $sender->getName();
						$lowercaseName = strtolower($player);
						$result = $this->db->query("SELECT * FROM confirm WHERE player='$lowercaseName';");
						$array = $result->fetchArray(SQLITE3_ASSOC);
						if(empty($array) == true) {
							$sender->sendMessage("[提示] 你没有被邀请加入任何公会 !");
							return true;
						}
						$invitedTime = $array["timestamp"];
						$currentTime = time();
						if( ($currentTime - $invitedTime) <= 45 ) { //This should be configurable
							$faction = $array["faction"];
							$stmt = $this->db->prepare("INSERT OR REPLACE INTO master (player, faction, rank) VALUES (:player, :faction, :rank);");
							$stmt->bindValue(":player", strtolower($sender->getPlayer()->getName()));
							$stmt->bindValue(":faction", $faction);
							$stmt->bindValue(":rank", "Member");
							$result = $stmt->execute();
							$this->db->query("DELETE FROM confirm WHERE player='$lowercaseName';");
							$sender->sendMessage("[提示] 你成功加入 $faction!");
							$this->getServer()->getPlayerExact($array["invitedby"])->sendMessage("[提示] $player 加入公会 !");
						} else {
							$sender->sendMessage("[提示] 邀请已经过期 !");
							$this->db->query("DELETE FROM confirm WHERE player='$player';");
						}
					}
					if(strtolower($args[0]) == "拒绝") {
						$player = $sender->getName();
						$lowercaseName = strtolower($player);
						$result = $this->db->query("SELECT * FROM confirm WHERE player='$lowercaseName';");
						$array = $result->fetchArray(SQLITE3_ASSOC);
						if(empty($array) == true) {
							$sender->sendMessage("[提示] 你没有被邀请加入任何公会 !");
							return true;
						}
						$invitedTime = $array["timestamp"];
						$currentTime = time();
						if( ($currentTime - $invitedTime) <= 45 ) { //This should be configurable
							$this->db->query("DELETE FROM confirm WHERE player='$lowercaseName';");
							$sender->sendMessage("[提示] 你拒绝了邀请入会的请求!");
							$this->getServer()->getPlayerExact($array["invitedby"])->sendMessage("[提示] $player 拒绝邀请 !");
						} else {
							$sender->sendMessage("[提示] 邀请已经过期 !");
							$this->db->query("DELETE FROM confirm WHERE player='$lowercaseName';");
						}
					}
					if(strtolower($args[0]) == "解散") {
						if($this->isInFaction($player) == true) {
							if($this->isLeader($player)) {
								$faction = $this->getPlayerFaction($player);
								$this->db->query("DELETE FROM master WHERE faction='$faction';");
								$sender->sendMessage("[提示] 公会解散成功!");
							}	 else {
								$sender->sendMessage("[提示] 你不是会长 !");
							}
						} else {
							$sender->sendMessage("[提示] 你没有加入公会 !");
						}
					}
					if(strtolower($args[0] == "退出")) {
						if($this->isLeader($player) == false) {
							$remove = $sender->getPlayer()->getNameTag();
							$faction = $this->getPlayerFaction($player);
							$name = $sender->getName();
							$this->db->query("DELETE FROM master WHERE player='$name';");
							$sender->sendMessage("[提示] 你成功离开 $faction");
						} else {
							$sender->sendMessage("[提示] 你必须先解散公会或者把会长的职位给别人 !");
						}
					}
					if(strtolower($args[0]) == "帮助") {
						$sender->sendMessage("公会命令 : \n/公会 创建 <公会名>\n/公会 解散\n/公会 帮助\n/公会 邀请 <玩家>\n/公会 踢人 <玩家>\n/公会 退出\n/公会 会长 <玩家>\n/公会 公告\n/公会 信息");
					}
				} 
				if(count($args) > 2) {
					$sender->sendMessage("[公会管理] 请使用 /公会 帮助 来列出所有公会相关命令");
				}
			}
		} else {
			$this->getServer()->getLogger()->info(TextFormat::RED . "[提示] 请在游戏中运行此命令");
		}
	}
	public function factionChat(PlayerChatEvent $PCE) {
		if($this->isInFaction($PCE->getPlayer()->getName()) == true) {
			$m = $PCE->getMessage();
			$p = $PCE->getPlayer()->getName();
			$lowerp = strtolower($p);
			$stmt = $this->db->query("SELECT * FROM master WHERE player='$p';");
			$result = $stmt->fetchArray(SQLITE3_ASSOC);
			$f = $result["faction"];
			$PCE->setFormat("[$f] $p: $m");
			//MOTD RECEIVER
			$p = strtolower($p);
			$stmt = $this->db->query("SELECT * FROM motdrcv WHERE player='$p';");
			$result = $stmt->fetchArray(SQLITE3_ASSOC);
			if(empty($result) == false) {
				if(time() - $result["timestamp"] > 30) {
					$PCE->getPlayer()->sendMessage("[提示] 超时 . 请在此使用 /公会 公告");
					$this->db->query("DELETE FROM motdrcv WHERE player='$p';");
					$PCE->setCancelled(true);
					return true;
				} else {
				$motd = $PCE->getMessage();
				$faction = $this->getPlayerFaction($p);
				$stmt = $this->db->prepare("INSERT OR REPLACE INTO motd (faction, message) VALUES (:faction, :message);");
				$stmt->bindValue(":faction", $faction);
				$stmt->bindValue(":message", $motd);
				$result = $stmt->execute();
				$PCE->setCancelled(true);
				$this->db->query("DELETE FROM motdrcv WHERE player='$p';");
				$PCE->getPlayer()->sendMessage("[提示] 成功更新公会的每日公告消息!");
				}
			}
		} else {
			$m = $PCE->getMessage();
			$p = $PCE->getPlayer()->getName();
			$PCE->setFormat("$p: $m");
		}
	}
	
	//To be implemented later
	
	/*public function playerJoinInfo(PlayerJoinEvent $PJE) {
		if($this->isInFaction($PJE->getPlayer()->getName()) == true) {
			$player = $PJE->getPlayer();
			$faction = $this->getPlayerFaction(strtolower($PJE->getPlayer()->getName()));
			$result = $this->db->query("SELECT * FROM motd WHERE faction='$faction';");
			$array = $result->fetchArray(SQLITE3_ASSOC);
			$message = $array["message"];
			$player->sendMessage("-------------------------");
			$player->sendMessage(Welcome Back, $player);
			$player->sendMessage("$faction MOTD:");
			$player->sendMessage("$message");
			$player->sendMessage("-------------------------");
		}
	}*/
	
	public function factionPVP(EntityDamageEvent $factionDamage) {
		if($factionDamage instanceof EntityDamageByEntityEvent){
			if(!($factionDamage->getEntity() instanceof Player) or !($factionDamage->getDamager() instanceof Player)) {
				return true;
			}
			if(($this->isInFaction($factionDamage->getEntity()->getPlayer()->getName()) == false) or ($this->isInFaction($factionDamage->getDamager()->getPlayer()->getName()) == false) ) {
				return true;
			}
			if(($factionDamage->getEntity() instanceof Player) and ($factionDamage->getDamager() instanceof Player)) {
				$player1 = $factionDamage->getEntity()->getPlayer()->getName();
				$player2 = $factionDamage->getDamager()->getPlayer()->getName();
				if($this->sameFaction($player1, $player2) == true) {
					$factionDamage->setCancelled(true);
				}
			}
		}
	}
	public function isInFaction($player) {
		$player = strtolower($player);
		$result = $this->db->query("SELECT * FROM master WHERE player='$player';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return empty($array) == false;
	}
	public function isLeader($player) {
		$faction = $this->db->query("SELECT * FROM master WHERE player='$player';");
		$factionArray = $faction->fetchArray(SQLITE3_ASSOC);
		return $factionArray["rank"] == "Leader";
	}
	public function getPlayerFaction($player) {
		$faction = $this->db->query("SELECT * FROM master WHERE player='$player';");
		$factionArray = $faction->fetchArray(SQLITE3_ASSOC);
		return $factionArray["faction"];
	}
	public function factionExists($faction) {
		$result = $this->db->query("SELECT * FROM master WHERE faction='$faction';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return empty($array) == false;
	}
	public function sameFaction($player1, $player2) {
		$faction = $this->db->query("SELECT * FROM master WHERE player='$player1';");
		$player1Faction = $faction->fetchArray(SQLITE3_ASSOC);
		$faction = $this->db->query("SELECT * FROM master WHERE player='$player2';");
		$player2Faction = $faction->fetchArray(SQLITE3_ASSOC);
		return $player1Faction["faction"] == $player2Faction["faction"];
	}
	public function onDisable() {
		$this->db->close();
	}
}