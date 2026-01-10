<?php
namespace aliuly\manyworlds\common;

use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use aliuly\manyworlds\common\mc;
use aliuly\manyworlds\common\BasicHelp;

use pocketmine\event\player\PlayerQuitEvent;

abstract class BasicPlugin extends PluginBase implements Listener {
	protected $modules = [];
	protected $scmdMap = [];
	protected $state = [];

	protected function modConfig($ns,$mods,$defaults,$xhlp="") {
		if (!isset($defaults["features"])) $defaults["features"] = [];
		foreach ($mods as $i => $j) {
			$defaults["features"][$i] = $j[1];
		}
		$cfg=(new Config($this->getDataFolder()."config.yml",
									  Config::YAML,$defaults))->getAll();
		$this->modules = [];
		foreach ($cfg["features"] as $i=>$j) {
			if (!isset($mods[$i])) {
				$this->getLogger()->info(mc::_("未知功能 \"%1%\" 已被忽视",$i));
				continue;
			}
			if (!$j) continue;
			$class = $mods[$i][0];
			if(strpos($class,"\\") === false) $class = $ns."\\".$class;
			if (isset($cfg[$i]))
				$this->modules[$i] = new $class($this,$cfg[$i]);
			else
				$this->modules[$i] = new $class($this);
		}
		$c = count($this->modules);
		if ($c == 0) {
			$this->getLogger()->info(mc::_("没有功能启用"));
			return;
		}
		$this->state = [];
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info(mc::n(mc::_("已启用一个功能"),
													 mc::_("启用了 %1%功能",$c),
													 $c));
		if (count($this->scmdMap) && count($this->scmdMap["mgrs"])) {
			$this->modules[] = new BasicHelp($this,$xhlp);
		}
		return $cfg;
	}

	public function cfgSave($key,$settings) {
		$cfg=new Config($this->getDataFolder()."config.yml",Config::YAML);
		$dat = $cfg->getAll();
		$dat[$key] = $settings;
		$cfg->setAll($dat);
		$cfg->save();
	}

	protected function initSCmdMap() {
		$this->scmdMap = [
			"mgrs" => [],
			"help" => [],
			"usage" => [],
			"alias" => [],
			"permission" => [],
		];
	}
	protected function dispatchSCmd(CommandSender $sender,Command $cmd,array $args,$data=null) {
		if (count($args) == 0) {
			$sender->sendMessage(mc::_("没有指定子命令"));
			return false;
		}
		$scmd = strtolower(array_shift($args));
		if (isset($this->scmdMap["alias"][$scmd])) {
			$scmd = $this->scmdMap["alias"][$scmd];
		}
		if (!isset($this->scmdMap["mgrs"][$scmd])) {
			$sender->sendMessage(mc::_("未知子功能 %2% (请尝试使用指令/%1% 来看看帮助方法)",$cmd->getName(),$scmd));
			return false;
		}
		if (isset($this->scmdMapd["permission"][$scmd])) {
			if (!$sender->hasPermission($this->scmdMapd["permission"][$scmd])) {
				$sender->sendMessage(mc::_("你不被允许这样做"));
				return true;
			}
		}
		$callback = $this->scmdMap["mgrs"][$scmd];
		if ($callback($sender,$cmd,$scmd,$data,$args)) return true;
		if (isset($this->scmdMap["mgrs"]["help"])) {
			$callback = $this->scmdMap["mgrs"]["help"];
			return $callback($sender,$cmd,$scmd,$data,["usage"]);
		}
		return false;
	}

	public function getSCmdMap() {
		return $this->scmdMap;
	}
	public function registerSCmd($cmd,$callable,$opts) {
		$cmd = strtolower($cmd);
		$this->scmdMap["mgrs"][$cmd] = $callable;

		foreach (["help","usage","permission"] as $p) {
			if(isset($opts[$p])) {
				$this->scmdMap[$p][$cmd] = $opts[$p];
			}
		}
		if (isset($opts["aliases"])) {
			foreach ($opts["aliases"] as $alias) {
				$this->scmdMap["alias"][$alias] = $cmd;
			}
		}
	}

	public function onPlayerQuit(PlayerQuitEvent $ev) {
		$n = strtolower($ev->getPlayer()->getName());
		if (isset($this->state[$n])) unset($this->state[$n]);
	}

	public function getState($label,$player,$default) {
		if ($player instanceof CommandSender) $player = $player->getName();
		$player = strtolower($player);
		if (!isset($this->state[$player])) return $default;
		if (!isset($this->state[$player][$label])) return $default;
		return $this->state[$player][$label];
	}

	public function setState($label,$player,$val) {
		if ($player instanceof CommandSender) $player = $player->getName();
		$player = strtolower($player);
		if (!isset($this->state[$player])) $this->state[$player] = [];
		$this->state[$player][$label] = $val;
	}

	public function unsetState($label,$player) {
		if ($player instanceof CommandSender) $player = $player->getName();
		$player = strtolower($player);
		if (!isset($this->state[$player])) return;
		if (!isset($this->state[$player][$label])) return;
		unset($this->state[$player][$label]);
	}


}
