<?php
/**
 ** OVERVIEW:Basic Usage
 **
 ** COMMANDS
 **
 ** * generators : List available world generators
 **   usage: /mw **generators**
 **
 **   List registered world generators.
 **/
namespace aliuly\manyworlds;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use pocketmine\utils\TextFormat;
use pocketmine\level\generator\Generator;

use aliuly\manyworlds\common\mc;
use aliuly\manyworlds\common\MPMU;
use aliuly\manyworlds\common\BasicCli;

class MwGenLst extends BasicCli {
	public function __construct($owner) {
		parent::__construct($owner);
		$this->enableSCmd("generators",["usage" => "",
												  "help" => mc::_("世界上列出生成器"),
												  "permission" => "mw.cmd.world.create",
												  "aliases" => ["gen","genlst"]]);
	}
	public function onSCommand(CommandSender $c,Command $cc,$scmd,$data,array $args) {
		if (count($args) != 0) return false;

		if (MPMU::apiVersion("1.12.0")) {
			$c->sendMessage(implode(", ",Generator::getGeneratorList()));
		} else {
			$c->sendMessage("normal, flat");
			$c->sendMessage(TextFormat::RED.
								 mc::_("[MW] 提供插件生成器的 \n 不被包括"));
		}
		return true;
	}
}
