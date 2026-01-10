<?php

namespace Yhzx;

use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class CreativeBlocks {
	
	/** @var \SQLite3 */
    private $sql;
	
	public function __construct($filename,$plugin){
		$this->sql = new \SQLite3($filename);
		$this->sql->exec("CREATE TABLE IF NOT EXISTS yhzx(
			ID INTEGER PRIMARY KEY AUTOINCREMENT,
			x INTEGER NOT NULL,
			y INTEGER NOT NULL,
			z INTEGER NOT NULL,
			level TEXT NOT NULL
			)");
	}
	
	public function add($x, $y, $z, $level){
		$this->sql->exec("INSERT INTO yhzx (x, y, z, level) VALUES ($x,$y,$z,'$level');");
	}
	
	public function iscreativeblock($x, $y, $z, $level){
		$f = $this->sql->query("SELECT * FROM yhzx WHERE x = $x AND y = $y AND z = $z AND level = '$level'")->fetchArray();
		return $f !== false;
	}
	
	public function delblock($x, $y, $z, $level){
		$this->sql->exec("DELETE FROM yhzx WHERE x = $x AND y = $y AND z = $z AND level = '$level'");
	}
}