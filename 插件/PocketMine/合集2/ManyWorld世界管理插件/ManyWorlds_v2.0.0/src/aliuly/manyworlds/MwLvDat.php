<?php
/**
 ** OVERVIEW:Basic Usage
 **
 ** COMMANDS
 **
 ** * lvdat : Show/Modify level.dat variables
 **   usage: /mw **lvdat** _<world>_ _[attr=value]_
 **
 **   Change directly some **level.dat** values/attributes.  Supported
 **   attributes:
 **   - spawn=x,y,z : Sets spawn point
 **   - seed=randomseed : seed used for terrain generation
 **   - name=string : Level name
 **   - generator=flat|normal : Terrain generator
 **   - preset=string : Presets string.
 **
 ** * fixname : fixes name mismatches
 **   usage: /mw **fixname** _<world>_
 **
 **   Fixes a world's **level.dat** file so that the name matches the
 **   folder name.
 **/
namespace aliuly\manyworlds;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use pocketmine\utils\TextFormat;

use aliuly\manyworlds\common\mc;
use aliuly\manyworlds\common\BasicCli;

use pocketmine\level\generator\Generator;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Long;
use pocketmine\nbt\tag\Compound;

class MwLvDat extends BasicCli {
	public function __construct($owner) {
		parent::__construct($owner);
		$this->enableSCmd("lvdat",["usage" => mc::_("<世界名字> [attr=value]"),
										"help" => mc::_("改变 level.dat的值"),
										"permission" => "mw.cmd.lvdat",
										"aliases" => ["lv"]]);
		$this->enableSCmd("fixname",["usage" => mc::_("<世界名字>"),
										"help" => mc::_("修改世界名字"),
										"permission" => "mw.cmd.lvdat",
										"aliases" => ["fix"]]);
	}
	public function onSCommand(CommandSender $c,Command $cc,$scmd,$data,array $args) {
		if (count($args) == 0) return false;
		if ($scmd == "fixname") {
			$world = implode(" ",$args);
			$c->sendMessage(TextFormat::AQUA.mc::_("Running /mw lvdat %1%  名字=%1%",$world));
			$args = [ $world , "name=$world" ];
		}
		$world = array_shift($args);
		if(!$this->owner->autoLoad($c,$world)) {
			$c->sendMessage(TextFormat::RED.mc::_("[MW] 世界%1%未曾被加载！",$worl));
			return true;
		}
		$level = $this->owner->getServer()->getLevelByName($world);
		if (!$level) {
			$c->sendMessage(TextFormat::RED.mc::_("[MW] 出现意外错误！"));
			return true;
		}
		//==== provider
		$provider = $level->getProvider();
		$changed = false; $unload = false;
		foreach ($args as $kv) {
			$kv = explode("=",$kv,2);
			if (count($kv) != 2) {
				$c->sendMessage(mc::_("Invalid element: %1%, ignored",$kv[0]));
				continue;
			}
			list($k,$v) = $kv;
			switch (strtolower($k)) {
				case "spawn":
					$pos = explode(",",$v);
					if (count($pos)!=3) {
						$c->sendMessage(mc::_("Invalid spawn location: %1%",implode(",",$pos)));
						continue;
					}
					list($x,$y,$z) = $pos;
					$cpos = $provider->getSpawn();
					if (($x=intval($x)) == $cpos->getX() &&
						 ($y=intval($y)) == $cpos->getY() &&
						 ($z=intval($z)) == $cpos->getZ()) {
						$c->sendMessage(mc::_("出生点不变"));
						continue;
					}
					$changed = true;
					$provider->setSpawn(new Vector3($x,$y,$z));
					break;
				case "seed":
					if ($provider->getSeed() == intval($v)) {
						$c->sendMessage(mc::_("种子不变"));
						continue;
					}
					$changed = true; $unload = true;
					$provider->setSeed($v);
					break;
				case "name": // LevelName String
					if ($provider->getName() == $v) {
						$c->sendMessage(mc::_("名字不变"));
						continue;
					}
					$changed = true; $unload = true;
					$provider->getLevelData()->LevelName = new String("LevelName",$v);
					break;
				case "generator":	// generatorName(String)
					if ($provider->getLevelData()->generatorName == $v) {
						$c->sendMessage(mc::_("生成不变"));
						continue;
					}
					$changed=true; $unload=true;
					$provider->getLevelData()->generatorName=new String("generatorName",$v);
					break;
				case "preset":	// String("generatorOptions");
					if ($provider->getLevelData()->generatorOptions == $v) {
						$c->sendMessage(mc::_("预设不变"));
						continue;
					}
					$changed=true; $unload=true;
					$provider->getLevelData()->generatorOptions =new String("generatorOptions",$v);
					break;
				default:
					$c->sendMessage(mc::_("未知密钥 %1%, 忽视",$k));
					continue;
			}
		}
		if ($changed) {
			$c->sendMessage(mc::_("正在从%1%升级level.dat",$world));
			$provider->saveLevelData();
			if ($unload) {
				$c->sendMessage(TextFormat::RED.
											mc::_("变化不会生效直到地图被卸载！"));
			}
		} else {
			$c->sendMessage(mc::_("什么也没有发生"));
		}
		return true;
	}
}
