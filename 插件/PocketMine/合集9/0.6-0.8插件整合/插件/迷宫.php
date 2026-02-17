<?php

/*
__PocketMine Plugin__
name=Maze
description=Maze Generator
version=1.0
author=BlinkSun
class=Maze
apiversion=11,12
*/


class Maze implements Plugin {
	private $api;

	public function __construct(ServerAPI $api, $server = false) {
		$this->api = $api;
	}

	public function init() {
		$this->cmaze = new Config($this->api->plugin->configPath($this)."config.yml", CONFIG_YAML, array("wallblock"=>"18","wallheight"=>"3"));
		$this->api->console->register('maze', "[X] [Y] Generate a maze (must be odd numbers).",array($this, 'commandHandle'));
		$this->api->console->alias("mz", "maze");
	}

	public function commandHandle($cmd, $params, $issuer, $alias){
		switch($cmd){
			case 'maze':
				if(isset($params[0]) or isset($params[1])) {
					if($params[0]%2 == false or $params[1]%2 == false) {
						$output = "[MAZE] Usage: /$cmd [X] [Y] (must be odd numbers)\n";
					}else{
						$this->GenerateMaze($params[0],$params[1],$issuer);
					}
				}else{
					$output = "[MAZE] Usage: /$cmd [X] [Y] (must be odd numbers)\n";
				}
			break;
		}
		return $output;
	}

	public function RandomDirections() {
		$cDir = array_fill(0, 4, array('X' => 0, 'Y' => 0));
		switch (rand(0,3)) {
			Case 0:
				$cDir[0]['X'] = -1;
				$cDir[1]['X'] = 1;
				$cDir[2]['Y'] = -1;
				$cDir[3]['Y'] = 1;
			break;
			Case 1:
				$cDir[3]['X'] = -1;
				$cDir[2]['X'] = 1;
				$cDir[1]['Y'] = -1;
				$cDir[0]['Y'] = 1;
			break;
			Case 2:
				$cDir[2]['X'] = -1;
				$cDir[3]['X'] = 1;
				$cDir[0]['Y'] = -1;
				$cDir[1]['Y'] = 1;
			break;
			Case 3:
				$cDir[1]['X'] = -1;
				$cDir[0]['X'] = 1;
				$cDir[3]['Y'] = -1;
				$cDir[2]['Y'] = 1;
			break;
		}
		return $cDir;
	}

	public function GenerateMaze($MAZE_X, $MAZE_Y, $player) {

		$intDir = 0;
		$intDone = 0;
		$cDir = array();
		$blnBlocked = False;
		$cN = array('X' => NULL, 'Y' => NULL);
		$cS = array('X' => NULL, 'Y' => NULL);
		$blnMaze = array_fill(1, $MAZE_X, array_fill(1, $MAZE_Y, False));

		Do {
			$cS['X'] = round(rand(1, $MAZE_X - 1) / 2) * 2;
			$cS['Y'] = round(rand(1, $MAZE_Y - 1) / 2) * 2;
			if($intDone == 0) {
				$blnMaze[$cS['X']][$cS['Y']] = True;
			}
			if($blnMaze[$cS['X']][$cS['Y']] == True) {
				Do {
					$blnBlocked = True;
					$cDir = $this->RandomDirections();
					for($intDir = 0; $intDir <= 3; $intDir++) {
						$cN['X'] = $cS['X'] + ($cDir[$intDir]['X'] * 2);
						$cN['Y'] = $cS['Y'] + ($cDir[$intDir]['Y'] * 2);
						if($cN['X'] < $MAZE_X and $cN['X'] > 1 and $cN['Y'] < $MAZE_Y and $cN['Y'] > 1) {
							if($blnMaze[$cN['X']][$cN['Y']] == False) {
								$blnMaze[$cN['X']][$cN['Y']] = True;
								$blnMaze[$cS['X'] + $cDir[$intDir]['X']][$cS['Y'] + $cDir[$intDir]['Y']] = True;
								$cS['X'] = $cN['X'];
								$cS['Y'] = $cN['Y'];
								$blnBlocked = False;
								$intDone++;
								break;
							}
						}
					}
				} while ($blnBlocked == False);
			}
		} while ($intDone + 1 < (($MAZE_X - 1) * ($MAZE_Y - 1)) / 4);
		$blnMaze[1][2] = True;					//Start
		$blnMaze[$MAZE_X][$MAZE_Y - 1] = True;	//Exit
		$this->BuildMaze($blnMaze, $MAZE_X, $MAZE_Y, $player);
	}

	private function BuildMaze($rmaze, $xmax, $ymax, $player) {
		For($z = 1; $z <= $ymax; $z++) {
			For($x = 1; $x <= $xmax; $x++) {
				For($y = 0; $y <= $this->cmaze->get("wallheight") - 1; $y++) {
					$maze_pos = new Vector3($player->entity->x + $x, $player->entity->y + $y, $player->entity->z + $z);
					If($rmaze[$x][$z] == True) {
						$player->level->setBlock($maze_pos, BlockAPI::get(AIR));
					} Else {
						$player->level->setBlock($maze_pos, BlockAPI::get($this->cmaze->get("wallblock")));
					}
				}
			}
		}
	}

	public function __destruct() {
	}
}
?>