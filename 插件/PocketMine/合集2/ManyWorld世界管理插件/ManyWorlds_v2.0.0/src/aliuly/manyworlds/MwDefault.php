<?php
/**
 ** OVERVIEW:Basic Usage
 **
 ** COMMANDS
 **
 ** * default : Sets the default world
 **   usage: /mw **default** _<world>_
 **
 **   Teleports you to another world.  If _player_ is specified, that
 **   player will be teleported.
 **/
namespace aliuly\manyworlds;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use pocketmine\utils\TextFormat;

use aliuly\manyworlds\common\mc;
use aliuly\manyworlds\common\BasicCli;

class MwDefault extends BasicCli {
	public function __construct($owner) {
		parent::__construct($owner);
		$this->enableSCmd("default",["usage" => mc::_("<world>"),
											  "help" => mc::_("默认情况下改变世界"),
											  "permission" => "mw.cmd.default"]);
	}
	public function onSCommand(CommandSender $c,Command $cc,$scmd,$data,array $args) {
		if (count($args) == 0) return false;
		$wname =implode(" ",$args);
		$old = $this->owner->getServer()->getConfigString("level-name");
		if ($old == $wname) {
			$c->sendMessage(TextFormat::RED.mc::_("没有改变"));
			return true;
		}
		if (!$this->owner->autoLoad($c,$wname)) {
			$c->sendMessage(TextFormat::RED.
										mc::_("[MW] 加载 %1%失败",$wname));
			$c->sendMessage(TextFormat::RED.mc::_("改变失败！"));
			return true;
		}
		$level = $this->owner->getServer()->getLevelByName($wname);
		if ($level === null) {
			$c->sendMessage(TextFormat::RED.mc::_("错误获取世界名字 %1%！"));
			return true;
		}
		$this->owner->getServer()->setConfigString("level-name",$wname);
		$this->owner->getServer()->setDefaultLevel($level);
		$c->sendMessage(TextFormat::BLUE.mc::_("默认世界改变为 %1%",$wname));
		return true;
	}
}
