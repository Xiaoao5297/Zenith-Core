<?php
/**
 ** OVERVIEW:Basic Usage
 **
 ** COMMANDS
 **
 ** * load : Loads a world
 **   usage: /mw **load** _<world>_
 **
 **   Loads _world_ directly.  Use _--all_ to load **all** worlds.
 **
 ** * unload : Unloads world
 **   usage: /mw **unload** _[-f]_  _<world>_
 **
 **   Unloads _world_.  Use _-f_ to force unloads.
 **/
namespace aliuly\manyworlds;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use pocketmine\utils\TextFormat;

use aliuly\manyworlds\common\mc;
use aliuly\manyworlds\common\MPMU;
use aliuly\manyworlds\common\BasicCli;

class MwLoader extends BasicCli {
	public function __construct($owner) {
		parent::__construct($owner);
		$this->enableSCmd("load",["usage" => mc::_("<world|--all>"),
										"help" => mc::_("加载一个世界"),
										"permission" => "mw.cmd.world.load",
										"aliases" => ["ld"]]);
		$this->enableSCmd("unload",["usage" => mc::_("[-f] <world>"),
										"help" => mc::_("尝试卸载世界"),
											 "permission" => "mw.cmd.world.load"]);
	}
	public function onSCommand(CommandSender $c,Command $cc,$scmd,$data,array $args) {
		if (count($args) == 0) return false;
		switch ($scmd) {
			case "load":
				return $this->mwWorldLoadCmd($c,implode(" ",$args));
			case "unload":
				$force = false;
				if ($args[0] == "-f") {
					$force = true;
					array_shift($args);
					if (count($args) == 0) return false;
				}
				return $this->mwWorldUnloadCmd($c,implode(" ",$args),$force);
		}
		return false;
	}
	private function mwWorldLoadCmd(CommandSender $sender,$wname) {
		if ($wname == "--all") {
			$wlst = [];
			foreach (glob($this->owner->getServer()->getDataPath(). "worlds/*") as $f) {
				$world = basename($f);
				if ($this->owner->getServer()->isLevelLoaded($world)) continue;
				if (!$this->owner->getServer()->isLevelGenerated($world)) continue;
				$wlst[] = $world;
			}
			if (count($wlst) == 0) {
				$sender->sendMessage(TextFormat::RED.
											mc::_("[MW] 没有世界可以加载！"));
				return true;
			}
			$sender->sendMessage(
				TextFormat::AQUA.
				mc::n(
					mc::_("[MW] 正在加载一个世界"),
					mc::_("[MW]正在加载整个 %1%世界",count($wlst)),
					count($wlst)));
		} else {
			if ($this->owner->getServer()->isLevelLoaded($wname)) {
				$sender->sendMessage(TextFormat::RED.
											mc::_("[MW] %1% 已被加载",$wname));
				return true;
			}
			if (!$this->owner->getServer()->isLevelGenerated($wname)) {
				$sender->sendMessage(TextFormat::RED.
											mc::_("[MW] %1% 不存在",$wname));
				return true;
			}
			$wlst = [ $wname ];
		}
		foreach ($wlst as $world) {
			if (!$this->owner->autoLoad($sender,$world)) {
				$sender->sendMessage(TextFormat::RED.
											mc::_("[MW] 加载世界失败 %1%",$world));
			}
		}
		return true;
	}
	private function mwWorldUnloadCmd(CommandSender $sender,$wname,$force) {
		if (MPMU::apiVersion("<1.12.0")) {
			// For old stuff...
			if ($wname == "--enable") {
				$this->owner->canUnload = true;
				$sender->sendMessage(TextFormat::YELLOW.
											mc::_("[MW] 卸载子命令启动"));
				$sender->sendMessage(TextFormat::YELLOW.
											mc::_("[MW] 如果想转为不启动请用此指令: /mw unload --disable"));
				return true;
			}
			if ($wname == "--disable") {
				$this->owner->canUnload = false;
				$sender->sendMessage(TextFormat::GREEN.
											mc::_("[MW] 不加载子命令启动"));
				$sender->sendMessage(TextFormat::GREEN.
											mc::_("[MW] 改为启动请打: /mw unload --enable"));
				return true;
			}
			if (!$this->owner->canUnload) {
				$sender->sendMessage(TextFormat::RED.mc::_("[MW] 卸载子命令默认情况下禁用"));
				$sender->sendMessage(TextFormat::RED.mc::_("[MW] 这是因为它通常会导致"));
				$sender->sendMessage(TextFormat::RED.mc::_("[MW] 服务器崩溃!"));
				$sender->sendMessage(TextFormat::RED.mc::_("[MW] 要激活使用:"));
				$sender->sendMessage(TextFormat::BLUE.mc::_("-   /mw unload --enable"));
				return true;
			}
		}
		// Actual implementation
		if (!$this->owner->getServer()->isLevelLoaded($wname)) {
			$sender->sendMessage(TextFormat::RED.mc::_("[MW] 世界%1%没有被加载.",$wname));
			return true;
		}
		$level = $this->owner->getServer()->getLevelByName($wname);
		if ($level === null) {
			$sender->sendMessage(TextFormat::RED.mc::_("[MW] 取得 %1%失败！",$wname));
			return true;
		}
		if (!$this->owner->getServer()->unloadLevel($level,$force)) {
			if ($force)
				$sender->sendMessage(TextFormat::RED.mc::_("[MW] 加载世界%1失败%",$wname));
			else
				$sender->sendMessage(TextFormat::RED.mc::_("[MW] 加载%1%失败  请尝试 -f",$wname));
		} else {
			$sender->sendMessage(TextFormat::GREEN.mc::_("[MW] %1% 已被卸载.",$wname));
		}
		return true;
	}
}
