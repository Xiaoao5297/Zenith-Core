<?php

/*
__PocketMine Plugin__
name=drawStuff
description=A plugin for drawing different items
version=1.0
author=BCYorkie
class=drawStuff
apiversion=10,11,12
*/

class drawStuff implements Plugin {
	private $api;

	public function __construct(ServerAPI $api, $server = false) {
		$this->api = $api;

		$this->arrRollback = array();
		$this->arrLastCommand = array();
		$this->arrSavedMacros = array();
		$this->blnSavingMacro = 0;
		$this->blnPlaying = 0;
		$this->objStartingVector = array();
		$this->objStartingDirection = '';
		$this->arrDefaults = array();

		//Return messages
		$this->arrReturnMessage['error_drawFromCommandLine'] = 'Can not run draw from command line.';
		$this->arrReturnMessage['error_noUsername'] = 'Enter a logged in username to get info.';
		$this->arrReturnMessage['error_duplicateRecording'] = 'This recording already exists, enter another name.';
		$this->arrReturnMessage['error_missingRecodingName'] = 'You must enter a name for saving.';
		$this->arrReturnMessage['error_noRecording'] = 'There is no recording with this name.';
		$this->arrReturnMessage['recording_saved'] = 'Recording has been saved.';
		$this->arrReturnMessage['recording_deleted'] = 'The recording has been removed.';
		$this->arrReturnMessage['recording_started'] = 'Recording started.';
		$this->arrReturnMessage['recording_cancelled'] = 'Recording cancelled.';
		$this->arrReturnMessage['recording_list'] = 'Available macros: ';
		$this->arrReturnMessage['undo'] = 'Last command has been undone.';
		$this->arrReturnMessage['floor'] = 'Nice looking floor you got there!';
		$this->arrReturnMessage['pool'] = 'Time to cool off!';
		$this->arrReturnMessage['cut'] = 'Blocks have been removed.';
		$this->arrReturnMessage['replace'] = 'Blocks have been replaced.';
		$this->arrReturnMessage['steps'] = 'Stairway to heaven.';
		$this->arrReturnMessage['diamond'] = 'Diamonds are a girl\'s bestfriend.';
		$this->arrReturnMessage['sphere'] = 'The circle of life.';
		$this->arrReturnMessage['set_defaults'] = 'Defaults have been updated.';
		$this->arrReturnMessage['error_defaults'] = 'No defaults to update.';
		$this->arrReturnMessage['prism'] = 'Run for your life, it\'s a Cuboid!.';
		$this->arrReturnMessage['cube'] = 'Solid as could be!';
		$this->arrReturnMessage['pyramid'] = 'A true wonder!';
		$this->arrReturnMessage['wall'] = 'There\'s a wall for you!';
		$this->arrReturnMessage['box'] = 'Have a box!';
		$this->arrReturnMessage['string'] = 'Here is a message for you.';
		$this->arrReturnMessage['play'] = 'Playa';

		//maximum values allowed for different items.
		$this->arrMaxValues['height'] = 30;
		$this->arrMaxValues['width'] = 30;
		$this->arrMaxValues['radius'] = 20;
		$this->arrMaxValues['length'] = 20;
		$this->arrMaxValues['size'] = 30;
		$this->arrMaxValues['pyramid'] = 49;
	}

	public function init()
	{
		$this->api->console->register("drawstuff", "<command> [parameters...]" , array($this, "commandHandler"));
		$this->api->console->register("user_info", "<command> [parameters...]" , array($this, "commandHandler"));
		$this->api->console->alias("d", "drawstuff");

		$this->config = new Config($this->api->plugin->configPath($this)."macros.yml", CONFIG_YAML, array());
		$this->config = new Config($this->api->plugin->configPath($this)."config.yml", CONFIG_YAML, array());

		//Load the existing macros that have been saved.
		$this->arrSavedMacros = $this->api->plugin->readYAML($this->api->plugin->configPath($this). "macros.yml");
		$this->arrDefaults = $this->api->plugin->readYAML($this->api->plugin->configPath($this). "config.yml");
	}

	public function commandHandler($strCmd, $arrParams, $objIssuer, $strAlias, $objStartingVector = array(),$objStartingDirection = '')
	{
		//a little debug feature I kept in, I needed to see postion and direction from command line for testing.
		if($strCmd === "user_info")
		{
			if($objIssuer instanceof Player)
			{
				$return = 'x: ' . $objIssuer->entity->x . ' y: ' . $objIssuer->entity->y . ' z: ' . $objIssuer->entity->z . ' dir:' . $objIssuer->entity->getDirection();
				console($objIssuer->username . ' ' . $return);
				return $return;
			}
			else
			{
				//called from the console, allows admin to see position of users.
				$objIssuer = $this->api->player->get($arrParams[0]);
				if(!$objIssuer instanceof Player) return $this->arrReturnMessage['error_noUsername'];

				console('x: ' . $objIssuer->entity->x . ' y: ' . $objIssuer->entity->y . ' z: ' . $objIssuer->entity->z . ' dir:' . $objIssuer->entity->getDirection());
				return;
			}
		}
		
		//if coming from the console, just leave, can't run anything past this point.
		if(!$objIssuer instanceof Player) return $this->arrReturnMessage['error_drawFromCommandLine'];

		//make sure the user has their defaults defined
		$this->__fncSetupUserDefaults($objIssuer);

		$strOutput = "";

		//store the original arrParams so can store them for a repeat
		$arrOriginalParams = $arrParams;

		if(!empty($objStartingVector))
		{
			$this->objStartingVector = $objStartingVector;
		}
		else
		{
			$this->objStartingVector = new Vector3($objIssuer->entity->x, $objIssuer->entity->y, $objIssuer->entity->z);
		}

		if($objStartingDirection === '')
		{
			$this->objStartingDirection = $objIssuer->entity->getDirection();
		}
		else
		{
			$this->objStartingDirection = (int) $objStartingDirection;
		}

		if($strCmd === "drawstuff")
		{
			$strSubCmd = strtolower(array_shift($arrParams));

			console($objIssuer->username . '(dir: ' . $objIssuer->entity->getDirection() . ') \\' . $strAlias . ' ' . $strSubCmd . ' ' . implode(' ',$arrParams));

			if($strSubCmd == 'help')
			{
				if(isset($arrParams[0]))
				{
					return $this->__fncHelp($strAlias,$arrParams[0]);
				}
				else
				{
					return $this->__fncHelp($strAlias);
				}
			}
			elseif($arrParams[0] == 'help')
			{
				return $this->__fncHelp($strAlias,$strSubCmd);
			}
			elseif($strSubCmd == 'repeat')
			{
				return $this->commandHandler($this->arrLastCommand['strCmd'],$this->arrLastCommand['arrParams'],$objIssuer,$this->arrLastCommand['strAlias']);
			}
			elseif($strSubCmd == 'record')
			{
				if ($arrParams[0] == 'save')
				{
					if (isset($arrParams[1]))
					{
						if (!isset($this->arrSavedMacros[$arrParams[1]]))
						{
							$strOutput = $this->arrReturnMessage['recording_saved'];

							$this->arrSavedMacros[$arrParams[1]] = json_encode($this->arrCurrentMacro[$objIssuer->username]);

							$this->blnSavingMacro = 0;
							$this->arrCurrentMacro[$objIssuer->username] = array();

							$this->api->plugin->writeYAML($this->api->plugin->configPath($this)."macros.yml", $this->arrSavedMacros);

							return $strOutput;
						}
						else
						{
							return $this->arrReturnMessage['error_duplicateRecording'];
						}
					}
					else
					{
						return $this->arrReturnMessage['error_missingRecodingName'];
					}
				}
				elseif ($arrParams[0] == 'delete')
				{
					if (isset($arrParams[1]) && isset($this->arrSavedMacros[$arrParams[1]]))
					{
						unset($this->arrSavedMacros[$arrParams[1]]);
						$this->api->plugin->writeYAML($this->api->plugin->configPath($this)."macros.yml", $this->arrSavedMacros);

						return $this->arrReturnMessage['recording_deleted'];
					}
					else
					{
						return $this->arrReturnMessage['error_noRecording'];
					}
				}
				elseif ($arrParams[0] == 'start')
				{
					$this->arrCurrentMacro[$objIssuer->username] = array();
					$this->blnSavingMacro = 1;
					return $this->arrReturnMessage['recording_started'];
				}
				elseif ($arrParams[0] == 'cancel')
				{
					$this->blnSavingMacro = 0;
					$this->arrCurrentMacro[$objIssuer->username] = array();
					return $this->arrReturnMessage['recording_cancelled'];
				}
				else
				{
					return $this->__fncHelp($strAlias,$strSubCmd);
				}
			}
			elseif($strSubCmd == 'play')
			{
				if (isset($this->arrSavedMacros[$arrParams[0]]))
				{
					$this->arrLastCommand['strCmd'] = $strCmd;
					$this->arrLastCommand['arrParams'] = $arrOriginalParams;
					$this->arrLastCommand['strAlias'] = $strAlias;
					$this->arrLastCommand['objStartingVector'] = new Vector3($objIssuer->entity->x, $objIssuer->entity->y, $objIssuer->entity->z);
					$this->arrLastCommand['intCurrentDirection'] = $objIssuer->entity->getDirection();
					return $this->__fncPlay($arrParams, $objIssuer);
				}
				else
				{
					$strAvailableMacros = implode(", ",array_keys($this->arrSavedMacros));
					return $this->arrReturnMessage['recording_list'] . $strAvailableMacros;
				}
			}

			//reset the rollback array with each call, unless it is undo
			if(!$this->blnPlaying && $strSubCmd != 'undo')
			{
				
				$this->arrRollback[$objIssuer->username] = array();
				$this->arrLastCommand['strCmd'] = $strCmd;
				$this->arrLastCommand['arrParams'] = $arrOriginalParams;
				$this->arrLastCommand['strAlias'] = $strAlias;
				$this->arrLastCommand['objStartingVector'] = new Vector3($objIssuer->entity->x, $objIssuer->entity->y, $objIssuer->entity->z);
				$this->arrLastCommand['intCurrentDirection'] = $objIssuer->entity->getDirection();
			}

			//setup shortcuts for the arrParams
			$arrShortCuts = array();
			$arrShortCuts['b'] = 'block';
			$arrShortCuts['br'] = 'block_replace';
			$arrShortCuts['bsub'] = 'block_sub';
			$arrShortCuts['h'] = 'height';
			$arrShortCuts['t'] = 'text';
			$arrShortCuts['l'] = 'length';
			$arrShortCuts['d'] = 'depth';
			$arrShortCuts['s'] = 'size';
			$arrShortCuts['w'] = 'width';
			$arrShortCuts['e'] = 'elevation';
			$arrShortCuts['bg'] = 'background';
			$arrShortCuts['r'] = 'radius';

			//set the arrParams to named parameters
			$arrNamedParams = array();
			foreach($arrParams AS $currentParam)
			{
				$arrTemp = explode(':',$currentParam);
				if(count($arrTemp) == 2)
				{
					if(isset($arrShortCuts[$arrTemp[0]]))
					{
						$arrNamedParams[$arrShortCuts[$arrTemp[0]]] = $arrTemp[1];
					}
					else
					{
						$arrNamedParams[$arrTemp[0]] = $arrTemp[1];
					}
				}
				elseif (count($arrTemp) ==1 && isset($arrNamedParams['text']))
				{
					$arrNamedParams['text'] .= ' ' . $arrTemp[0];
				}
			}

			switch($strSubCmd)
			{
				case 'cube':
					$strOutput = $this->__fncDrawCube($arrNamedParams, $objIssuer);
				break;

				case 'floor':
					$strOutput = $this->__fncDrawFloor($arrNamedParams, $objIssuer);
				break;

				case 'wall':
					$strOutput = $this->__fncDrawWall($arrNamedParams, $objIssuer);
				break;

				case 'pool':
					$strOutput = $this->__fncDrawPool($arrNamedParams, $objIssuer);
				break;

				case 'pyramid':
					$strOutput = $this->__fncDrawPyramid($arrNamedParams, $objIssuer);
				break;

				case 'box':
					$strOutput = $this->__fncDrawBox($arrNamedParams, $objIssuer);
				break;

				case 'write':
					$strOutput = $this->__fncDrawString($arrNamedParams, $objIssuer);
				break;

				case 'prism':
					$strOutput = $this->__fncDrawPrism($arrNamedParams, $objIssuer);
				break;

				case 'cut':
					$strOutput = $this->__fncCut($arrNamedParams, $objIssuer);
				break;
				
				case 'replace':
					$strOutput = $this->__fncReplace($arrNamedParams, $objIssuer);
				break;

				case 'steps':
					$strOutput = $this->__fncDrawSteps($arrNamedParams, $objIssuer);
				break;

				case 'sphere':
					$strOutput = $this->__fncDrawSphere($arrNamedParams, $objIssuer);
				break;

				case 'diamond':
					$strOutput = $this->__fncDrawDiamond($arrNamedParams, $objIssuer);
				break;

				case 'set':
					$strOutput = $this->__fncSetDefaults($arrNamedParams, $objIssuer);
				break;
				case 'undo':
					if ($arrParams[0] == 'help')
					{
						return $this->__fncHelp('help');
					}
					else
					{
						$strOutput = $this->__fncUndo($arrParams, $objIssuer);
					}
				break;
			}
		}

		//if we are in saving mode, need to register this call
		if($this->blnSavingMacro === 1)
		{
			$this->arrCurrentMacro[$objIssuer->username][] = $this->arrLastCommand;
		}

		return $strOutput;
	}

	private function __fncUndo($arrParams, $objIssuer)
	{
		foreach(array_reverse($this->arrRollback[$objIssuer->username]) as $arrCurrentRollback)
		{
			$objBlock = $this->api->block->get($arrCurrentRollback['block_type'], 0);
			$objIssuer->level->setBlock($arrCurrentRollback['block_pos'], $objBlock, $objBlock->getMetadata());
		}

		$this->arrRollback[$objIssuer->username] = array();
		return $this->arrReturnMessage['undo'];
	}

	private function __fncSetRollback($objIssuer,$block_pos)
	{
		$objCurrentBlock = $objIssuer->level->getBlockRaw($block_pos);
		$intCurrentBlockID = $objCurrentBlock->getID();
		$this->arrRollback[$objIssuer->username][] = array('block_pos'=>$block_pos,'block_type'=>$intCurrentBlockID);
	}

	private function __fncDrawFloor($arrParams, $objIssuer)
	{
		//first get the coordinates of where the user is standing
		$intLength = (isset($arrParams['length']) && is_numeric ($arrParams['length'])) ? (int) $arrParams['length'] : $this->arrDefaults[$objIssuer->username]['length'];
		$intWidth = (isset($arrParams['width']) && is_numeric ($arrParams['width'])) ? (int) $arrParams['width'] : $this->arrDefaults[$objIssuer->username]['width'];
		$intElevation = (isset($arrParams['elevation']) && is_numeric ($arrParams['elevation'])) ? (int) $arrParams['elevation'] : -1;

		$current_x = $this->objStartingVector->x;
		$current_y = $this->objStartingVector->y + $intElevation;
		$current_z = $this->objStartingVector->z;

		$arrBlockID = $this->__fncGetBlockID($objIssuer,$arrParams['block']);

		$block_pos = new Vector3($current_x, $current_y, $current_z);
		$arrRectangle = $this->__fncDrawRectangle(array('objIssuer'=>$objIssuer,'strStaticPlain'=>'horizontal','intLength'=>$intLength,'intWidth'=>$intWidth,'objStartingPos'=>$block_pos,'arrBlockType'=>$arrBlockID));

		return $this->arrReturnMessage['floor'];
	}

	private function __fncDrawPool($arrParams, $objIssuer)
	{
		//first get the coordinates of where the user is standing
		$intLength = (isset($arrParams['length']) && is_numeric ($arrParams['length'])) ? (int) $arrParams['length'] : $this->arrDefaults[$objIssuer->username]['length'];
		$intWidth = (isset($arrParams['width']) && is_numeric ($arrParams['width'])) ? (int) $arrParams['width'] : $this->arrDefaults[$objIssuer->username]['width'];
		$intDepth = (isset($arrParams['depth']) && is_numeric ($arrParams['depth'])) ? (int) $arrParams['depth'] : $this->arrDefaults[$objIssuer->username]['depth'];

		$arrBlockID = $this->__fncGetBlockID($objIssuer,'water');

		$current_x = $this->objStartingVector->x;
		$current_y = $this->objStartingVector->y;
		$current_z = $this->objStartingVector->z;

		for($i=1;$i<=$intDepth;$i++)
		{
			$block_pos = new Vector3($current_x, $current_y - $i, $current_z);
			$arrRectangle = $this->__fncDrawRectangle(array('objIssuer'=>$objIssuer,'strStaticPlain'=>'horizontal','intLength'=>$intLength,'intWidth'=>$intWidth,'objStartingPos'=>$block_pos,'arrBlockType'=>$arrBlockID));
		}


		return $this->arrReturnMessage['pool'];
	}

	private function __fncCut($arrParams, $objIssuer)
	{
		//short cut function to remove blocks (paste coming soon). Calls draw prism and passes in air.
		$arrParams['block'] = 'air';
		$this-> __fncDrawPrism($arrParams, $objIssuer);

		return $this->arrReturnMessage['cut'];

	}
	
	private function __fncReplace($arrParams, $objIssuer)
	{
		//short cut function to replace blocks
		$this-> __fncDrawPrism($arrParams, $objIssuer);
		return $this->arrReturnMessage['replace'];

	}

	private function __fncDrawSteps($arrParams, $objIssuer)
	{
		//incoming parameters
		$intWidth = (isset($arrParams['width']) && is_numeric ($arrParams['width'])) ? (int) $arrParams['width'] : $this->arrDefaults[$objIssuer->username]['width'];
		$intHeight = (isset($arrParams['height']) && is_numeric ($arrParams['height'])) ? (int) $arrParams['height'] : $this->arrDefaults[$objIssuer->username]['height'];
		$intElevation = (isset($arrParams['elevation']) && is_numeric ($arrParams['elevation'])) ? (int) $arrParams['elevation'] : $this->arrDefaults[$objIssuer->username]['elevation'];
		$arrBlockID = $this->__fncGetBlockID($objIssuer,$arrParams['block']);

		//have to put some max values in place
		if ($intWidth > $this->arrMaxValues['width']) $intWidth = $this->arrMaxValues['width'];
		if ($intHeight > $this->arrMaxValues['height']) $intHeight = $this->arrMaxValues['height'];

		$current_x = $this->objStartingVector->x;
		$current_y = $this->objStartingVector->y + $intElevation;
		$current_z = $this->objStartingVector->z;


		$intLength = $intHeight;
		for($i=0;$i<$intHeight;$i++)
		{
			switch($this->objStartingDirection)
			{
				case '0':
					$block_pos = new Vector3($current_x + $i, $current_y + $i, $current_z);
				break;
				case '1':
					$block_pos = new Vector3($current_x, $current_y + $i, $current_z + $i);
				break;
				case '2':
					$block_pos = new Vector3($current_x - $i, $current_y + $i, $current_z);
				break;
				case '3':
					$block_pos = new Vector3($current_x, $current_y + $i, $current_z - $i);
				break;
			}
			$arrRectangle = $this->__fncDrawRectangle(array('objIssuer'=>$objIssuer,'strStaticPlain'=>'horizontal','intLength'=>$intLength,'intWidth'=>$intWidth,'objStartingPos'=>$block_pos,'arrBlockType'=>$arrBlockID));
			$intLength--;
		}


		return $this->arrReturnMessage['steps'];
	}

	private function __fncDrawDiamond($arrParams, $objIssuer)
	{
		$intSize = (isset($arrParams['size']) && is_numeric ($arrParams['size'])) ? (int) $arrParams['size'] : $this->arrDefaults[$objIssuer->username]['size'];
		$intElevation = (isset($arrParams['elevation']) && is_numeric ($arrParams['elevation'])) ? (int) $arrParams['elevation'] : $this->arrDefaults[$objIssuer->username]['elevation'];
		$arrBlockID = $this->__fncGetBlockID($objIssuer,$arrParams['block']);

		$current_x = $this->objStartingVector->x;
		$current_y = $this->objStartingVector->y + $intElevation;
		$current_z = $this->objStartingVector->z;

		//check for max size
		if ($intSize > $this->arrMaxValues['pyramid']) $intSize = $this->arrMaxValues['pyramid'];

		$intPositionAdjustment = $intSize/2 -1;

		if ($intSize % 2 == 0)
		{
			$intStartingSize = 2;
		}
		else
		{
			$intStartingSize = 1;
		}

		$blnIncreaseSize = 1;

		for($i=0;$i<$intSize;$i++)
		{
			switch($this->objStartingDirection)
			{
				case '0':
					$block_pos = new Vector3($current_x + $intPositionAdjustment, $current_y + $i, $current_z + $intPositionAdjustment);
				break;
				case '1':
					$block_pos = new Vector3($current_x - $intPositionAdjustment, $current_y + $i, $current_z + $intPositionAdjustment);
				break;
				case '2':
					$block_pos = new Vector3($current_x - $intPositionAdjustment, $current_y + $i, $current_z - $intPositionAdjustment);
				break;
				case '3':
					$block_pos = new Vector3($current_x + $intPositionAdjustment, $current_y + $i, $current_z - $intPositionAdjustment);
				break;
			}

			$arrRectangle = $this->__fncDrawRectangle(array('objIssuer'=>$objIssuer,'strStaticPlain'=>'horizontal','intLength'=>$intStartingSize,'intWidth'=>$intStartingSize,'objStartingPos'=>$block_pos,'arrBlockType'=>$arrBlockID));

			if($intStartingSize >= $intSize)
			{
				$blnIncreaseSize = 0;
			}
			if($blnIncreaseSize)
			{
				$intStartingSize = $intStartingSize + 2;
				$intPositionAdjustment--;
			}
			else
			{
				$intStartingSize = $intStartingSize - 2;
				$intPositionAdjustment++;
			}
		}
		return $this->arrReturnMessage['diamond'];
	}

	private function __fncDrawSphere($arrParams, $objIssuer)
	{
		$intRadius = (isset($arrParams['radius']) && is_numeric ($arrParams['radius'])) ? (int) $arrParams['radius'] : $this->arrDefaults[$objIssuer->username]['radius'];
		$intElevation = (isset($arrParams['elevation']) && is_numeric ($arrParams['elevation'])) ? (int) $arrParams['elevation'] : $this->arrDefaults[$objIssuer->username]['elevation'];
		$arrBlockID = $this->__fncGetBlockID($objIssuer,$arrParams['block']);

		//have to put some max values in place
		if ($intRadius > $this->arrMaxValues['radius']) $intRadius = $this->arrMaxValues['radius'];

		$current_y = $this->objStartingVector->y + $intRadius + $intElevation;
		switch($this->objStartingDirection)
		{
			case '0':
				$current_x = $this->objStartingVector->x + $intRadius;
				$current_z = $this->objStartingVector->z + $intRadius;
			break;
			case '1':
				$current_x = $this->objStartingVector->x - $intRadius;
				$current_z = $this->objStartingVector->z + $intRadius;
			break;
			case '2':
				$current_x = $this->objStartingVector->x - $intRadius;
				$current_z = $this->objStartingVector->z - $intRadius;
			break;
			case '3':
				$current_x = $this->objStartingVector->x + $intRadius;
				$current_z = $this->objStartingVector->z - $intRadius;
			break;
		}

		$objBlock = $this->api->block->get($arrBlockID[0], $arrBlockID[1]);

		for($x = -$intRadius; $x < $intRadius; $x++){
			for($y = -$intRadius; $y < $intRadius; $y++){
				for($z = -$intRadius; $z < $intRadius; $z++){
					$intDist = sqrt(($x*$x + $y*$y + $z*$z)); //Calculates the distance
					if($intDist > $intRadius) continue;

					$block_pos = new Vector3($current_x + $x, $current_y + $y, $current_z - $z);
					$this->__fncSetRollback($objIssuer,$block_pos);
					$objIssuer->level->setBlock($block_pos, $objBlock, $objBlock->getMetadata());
				}
			}
		}

		return $this->arrReturnMessage['sphere'];
	}

	private function __fncSetDefaults($arrParams, $objIssuer)
	{
		$blnNeedSaved = 0;

		if(isset($arrParams['block']) || isset($arrParams['block_sub']))
		{
			$strBlock = (isset($arrParams['block'])) ? $arrParams['block'] : $this->arrDefaults[$objIssuer->username]['block'];
			$strBlockSub = (isset($arrParams['block_sub'])) ? $arrParams['block_sub'] : $this->arrDefaults[$objIssuer->username]['block_sub'];
			$arrBlockID = $this->__fncGetBlockID($objIssuer,$strBlock."|".$strBlockSub);

			$arrParams['block'] = $arrBlockID[0];
			$arrParams['block_sub'] = $arrBlockID[1];
		}

		foreach($arrParams AS $currentKey=>$currentValue)
		{
			if(isset($this->arrDefaults[$objIssuer->username][$currentKey]))
			{
				$this->arrDefaults[$objIssuer->username][$currentKey] = $currentValue;
				$blnNeedSaved = 1;
			}
		}

		if($blnNeedSaved)
		{
			$this->api->plugin->writeYAML($this->api->plugin->configPath($this)."config.yml", $this->arrDefaults);
			return $this->arrReturnMessage['set_defaults'];
		}
		else
		{
			return $this->arrReturnMessage['error_defaults'];
		}

	}

	private function __fncDrawPrism($arrParams, $objIssuer)
	{
		//incoming parameters
		$intLength = (isset($arrParams['length']) && is_numeric ($arrParams['length'])) ? (int) $arrParams['length'] : $this->arrDefaults[$objIssuer->username]['length'];
		$intWidth = (isset($arrParams['width']) && is_numeric ($arrParams['width'])) ? (int) $arrParams['width'] : $this->arrDefaults[$objIssuer->username]['width'];
		$intHeight = (isset($arrParams['height']) && is_numeric ($arrParams['height'])) ? (int) $arrParams['height'] : $this->arrDefaults[$objIssuer->username]['height'];
		$intElevation = (isset($arrParams['elevation']) && is_numeric ($arrParams['elevation'])) ? (int) $arrParams['elevation'] : $this->arrDefaults[$objIssuer->username]['elevation'];
		$arrBlockID = $this->__fncGetBlockID($objIssuer,$arrParams['block']);
		
		
		//if we are calling replace, then we need to pass in a second block to the rectangle
		if (isset($arrParams['block_replace']))
		{
			$arrBlockReplaceID = $this->__fncGetBlockID($objIssuer,$arrParams['block_replace']);
		}
		else
		{
			$arrBlockReplaceID = '';
		}

		//have to put some max values in place
		if ($intWidth > $this->arrMaxValues['width']) $intWidth = $this->arrMaxValues['width'];
		if ($intLength > $this->arrMaxValues['length']) $intLength = $this->arrMaxValues['length'];
		if ($intHeight > $this->arrMaxValues['height']) $intHeight = $this->arrMaxValues['height'];

		$current_x = $this->objStartingVector->x;
		$current_y = $this->objStartingVector->y + $intElevation;
		$current_z = $this->objStartingVector->z;

		for($i=0;$i<$intHeight;$i++)
		{
			$block_pos = new Vector3($current_x, $current_y + $i, $current_z);
			$arrRectangle = $this->__fncDrawRectangle(array('objIssuer'=>$objIssuer,'strStaticPlain'=>'horizontal','intLength'=>$intLength,'intWidth'=>$intWidth,'objStartingPos'=>$block_pos,'arrBlockType'=>$arrBlockID,'arrBlockReplaceType'=>$arrBlockReplaceID));
		}


		return $this->arrReturnMessage['prism'];
	}

	private function __fncDrawCube($arrParams, $objIssuer)
	{
		//first get the coordinates of where the user is standing
		$intSize = (isset($arrParams['size']) && is_numeric ($arrParams['size'])) ? (int) $arrParams['size'] : $this->arrDefaults[$objIssuer->username]['length'];
		$intElevation = (isset($arrParams['elevation']) && is_numeric ($arrParams['elevation'])) ? (int) $arrParams['elevation'] : $this->arrDefaults[$objIssuer->username]['elevation'];
		$arrBlockID = $this->__fncGetBlockID($objIssuer,$arrParams['block']);

		if ($intSize > $this->arrMaxValues['size']) $intSize = $this->arrMaxValues['size'];

		$current_x = $this->objStartingVector->x;
		$current_y = $this->objStartingVector->y + $intElevation;
		$current_z = $this->objStartingVector->z;

		for($i=0;$i<$intSize;$i++)
		{
			$block_pos = new Vector3($current_x, $current_y + $i, $current_z);
			$arrRectangle = $this->__fncDrawRectangle(array('objIssuer'=>$objIssuer,'strStaticPlain'=>'horizontal','intLength'=>$intSize,'intWidth'=>$intSize,'objStartingPos'=>$block_pos,'arrBlockType'=>$arrBlockID));
		}


		return $this->arrReturnMessage['cube'];
	}

	private function __fncDrawPyramid($arrParams, $objIssuer)
	{
		//first get the coordinates of where the user is standing
		$intSize = (isset($arrParams['size']) && is_numeric ($arrParams['size'])) ? (int) $arrParams['size'] : $this->arrDefaults[$objIssuer->username]['length'];
		$intElevation = (isset($arrParams['elevation']) && is_numeric ($arrParams['elevation'])) ? (int) $arrParams['elevation'] : $this->arrDefaults[$objIssuer->username]['elevation'];
		$arrBlockID = $this->__fncGetBlockID($objIssuer,$arrParams['block']);

		//check for max size
		if ($intSize > $this->arrMaxValues['pyramid']) $intSize = $this->arrMaxValues['pyramid'];

		$current_x = $this->objStartingVector->x;
		$current_y = $this->objStartingVector->y + $intElevation;
		$current_z = $this->objStartingVector->z;
		$intCurrentSize = $intSize;

		for($i=0;$i< $intSize/2; $i++)
		{
			switch($this->objStartingDirection)
			{
				case '0':
					$block_pos = new Vector3($current_x + $i, $current_y + $i, $current_z + $i);
				break;
				case '1':
					$block_pos = new Vector3($current_x - $i, $current_y + $i, $current_z + $i);
				break;
				case '2':
					$block_pos = new Vector3($current_x - $i, $current_y + $i, $current_z - $i);
				break;
				case '3':
					$block_pos = new Vector3($current_x + $i, $current_y + $i, $current_z - $i);
				break;
			}

			$arrRectangle = $this->__fncDrawRectangle(array('objIssuer'=>$objIssuer,'strStaticPlain'=>'horizontal','intLength'=>$intCurrentSize,'intWidth'=>$intCurrentSize,'objStartingPos'=>$block_pos,'arrBlockType'=>$arrBlockID));
			$intCurrentSize = $intCurrentSize - 2;
		}

		return $this->arrReturnMessage['pyramid'];
	}



	private function __fncDrawWall($arrParams, $objIssuer)
	{
		$current_x = $this->objStartingVector->x;
		$current_y = $this->objStartingVector->y;
		$current_z = $this->objStartingVector->z;

		$intLength = (isset($arrParams['length']) && is_numeric ($arrParams['length'])) ? (int) $arrParams['length'] : $this->arrDefaults[$objIssuer->username]['length'];
		$intHeight = (isset($arrParams['height']) && is_numeric ($arrParams['height'])) ? (int) $arrParams['height'] : $this->arrDefaults[$objIssuer->username]['width'];
		$intElevation = (isset($arrParams['elevation']) && is_numeric ($arrParams['elevation'])) ? (int) $arrParams['elevation'] : $this->arrDefaults[$objIssuer->username]['elevation'];
		$arrBlockID = $this->__fncGetBlockID($objIssuer,$arrParams['block']);

		$block_pos = new Vector3($current_x, $current_y + $intElevation, $current_z);
		$arrRectangle = $this->__fncDrawRectangle(array('objIssuer'=>$objIssuer,'strStaticPlain'=>'vertical','intLength'=>$intLength,'intWidth'=>$intHeight,'objStartingPos'=>$block_pos,'arrBlockType'=>$arrBlockID));
		return $this->arrReturnMessage['wall'];
	}


	//This can probably get cleaned up, one of the earlier functions
	private function __fncDrawBox($arrParams, $objIssuer)
	{
		$intSize = (isset($arrParams['size']) && is_numeric ($arrParams['size'])) ? (int) $arrParams['size'] : $this->arrDefaults[$objIssuer->username]['size'];
		$intElevation = (isset($arrParams['elevation']) && is_numeric ($arrParams['elevation'])) ? (int) $arrParams['elevation'] : $this->arrDefaults[$objIssuer->username]['elevation'];
		$arrBlockID = $this->__fncGetBlockID($objIssuer,$arrParams['block']);

		//first get the coordinates of where the user is standing
		$current_x = (int) $this->objStartingVector->x + 1;
		$current_y = (int) $this->objStartingVector->y + $intElevation;
		$current_z = (int) $this->objStartingVector->z + 1;

		if ($intSize > $this->arrMaxValues['size']) $intSize = $this->arrMaxValues['size'];

		switch($this->objStartingDirection)
		{
			case '0':
				$objFrontWallPos = new Vector3($current_x + 1, $current_y, $current_z - 1 );
				$objBackWallPos = new Vector3($current_x + $intSize, $current_y, $current_z);
				$objSideWall = new Vector3($current_x + 1, $current_y, $current_z);
				$objOppositeSideWallPos = new Vector3($current_x, $current_y, $current_z + $intSize - 1);

				$objFloor = new Vector3($current_x, $current_y, $current_z);
				$objCeiling = new Vector3($current_x, $current_y + $intSize - 1, $current_z);
				$intOppositeDirection = 1;
			break;
			case '1':
				$objFrontWallPos = new Vector3($current_x + 1, $current_y, $current_z + 1);
				$objBackWallPos = new Vector3($current_x, $current_y, $current_z + $intSize);
				$objSideWall = new Vector3($current_x, $current_y, $current_z + 1);
				$objOppositeSideWallPos = new Vector3($current_x - $intSize + 1, $current_y, $current_z);
				$objFloor = new Vector3($current_x, $current_y, $current_z);
				$objCeiling = new Vector3($current_x, $current_y + $intSize - 1, $current_z);
				$intOppositeDirection = 2;
			break;
			case '2':
				$objFrontWallPos = new Vector3($current_x - 2, $current_y, $current_z );
				$objBackWallPos = new Vector3($current_x - $intSize -1, $current_y, $current_z + 1);
				$objSideWall = new Vector3($current_x - 1, $current_y, $current_z);
				$objOppositeSideWallPos = new Vector3($current_x - 2, $current_y, $current_z - $intSize + 1);
				$objFloor = new Vector3($current_x - 1, $current_y, $current_z);
				$objCeiling = new Vector3($current_x -1, $current_y + $intSize - 1, $current_z);
				$intOppositeDirection = 3;
			break;
			case '3':
				$objFrontWallPos = new Vector3($current_x, $current_y, $current_z - 3);
				$objBackWallPos = new Vector3($current_x - 1, $current_y, $current_z - $intSize - 2);
				$objSideWall = new Vector3($current_x, $current_y, $current_z - 2);
				$objOppositeSideWallPos = new Vector3($current_x + $intSize - 1, $current_y, $current_z - 3);
				$objFloor = new Vector3($current_x, $current_y, $current_z - 2);
				$objCeiling = new Vector3($current_x, $current_y + $intSize - 1, $current_z - 2);
				$intOppositeDirection = 0;
			break;
		}

		$arrRectangle = $this->__fncDrawRectangle(array('objIssuer'=>$objIssuer,'strStaticPlain'=>'vertical','intLength'=>$intSize - 1,'intWidth'=>$intSize - 1,'objStartingPos'=>$objSideWall,'arrBlockType'=>$arrBlockID));
		$arrRectangle = $this->__fncDrawRectangle(array('objIssuer'=>$objIssuer,'strStaticPlain'=>'vertical','intLength'=>$intSize - 1,'intWidth'=>$intSize - 1,'objStartingPos'=>$objOppositeSideWallPos,'arrBlockType'=>$arrBlockID));
		$arrRectangle = $this->__fncDrawRectangle(array('objIssuer'=>$objIssuer,'strStaticPlain'=>'vertical','intLength'=>$intSize - 1,'intWidth'=>$intSize - 1,'objStartingPos'=>$objFrontWallPos,'arrBlockType'=>$arrBlockID,'intCurrentDirection'=>$intOppositeDirection));
		$arrRectangle = $this->__fncDrawRectangle(array('objIssuer'=>$objIssuer,'strStaticPlain'=>'vertical','intLength'=>$intSize - 1,'intWidth'=>$intSize - 1,'objStartingPos'=>$objBackWallPos,'arrBlockType'=>$arrBlockID,'intCurrentDirection'=>$intOppositeDirection));
		$arrRectangle = $this->__fncDrawRectangle(array('objIssuer'=>$objIssuer,'strStaticPlain'=>'horizontal','intLength'=>$intSize,'intWidth'=>$intSize,'objStartingPos'=>$objFloor,'arrBlockType'=>$arrBlockID));
		$arrRectangle = $this->__fncDrawRectangle(array('objIssuer'=>$objIssuer,'strStaticPlain'=>'horizontal','intLength'=>$intSize,'intWidth'=>$intSize,'objStartingPos'=>$objCeiling,'arrBlockType'=>$arrBlockID));

		return $this->arrReturnMessage['box'];
	}

	private function __fncDrawString($arrParams, $objIssuer)
	{

		$arrBlockID = $this->__fncGetBlockID($objIssuer,$arrParams['block']);
		$intElevation = (isset($arrParams['elevation']) && is_numeric ($arrParams['elevation'])) ? (int) $arrParams['elevation'] : $this->arrDefaults[$objIssuer->username]['elevation'];
		$strBackgroundBlock = (isset($arrParams['background'])) ?  $arrParams['background'] : 'air';
		$arrBlockBackgroundID = $this->__fncGetBlockID($objIssuer,$strBackgroundBlock);
		$objBackgroundBlock = $this->api->block->get($arrBlockBackgroundID[0], $arrBlockBackgroundID[1]);

		$arrFullString = str_split(strtolower($arrParams['text']));

		$arrSmall['a']=array(6,7,8,9,10,12,13,15,16,17,18,20,21,22,24,25,26,28,29,31,38,39);
		$arrSmall['b']=array(9,10,11,13,14,17,18,19,21,22,25,26,27,29,30,32,39);
		$arrSmall['c']=array(9,10,11,12,13,14,17,18,19,20,21,22,25,26,27,28,29,30,33,34,35,26,27,36,37,38);
		$arrSmall['d']=array(9,10,11,12,13,14,17,18,19,20,21,22,25,26,27,28,29,30,32,39);
		$arrSmall['e']=array(9,10,11,13,14,17,18,19,21,22,25,26,27,29,30,33,34,35,36,37,38);
		$arrSmall['f']=array(8,9,10,11,13,14,16,17,18,19,21,22,24,25,26,27,29,30,32,33,34,35,36,37,38);
		$arrSmall['g']=array(9,10,11,12,13,14,17,18,19,20,21,22,25,26,28,29,30,36,37,38);
		$arrSmall['h']=array(8,9,10,11,13,14,15,16,17,18,19,21,22,23,24,25,26,27,29,30,31);
		$arrSmall['i']=array(1,2,3,4,5,6,9,10,11,12,13,14,25,26,27,28,29,30,33,34,35,36,37,38);
		$arrSmall['j']=array(0,3,4,5,6,9,10,11,12,13,14,24,25,26,27,28,29,30,32,33,34,35,36,37,38);
		$arrSmall['k']=array(8,9,10,11,13,14,15,16,17,18,20,22,23,24,25,27,28,29,31,34,35,36,37,38);
		$arrSmall['l']=array(9,10,11,12,13,14,15,17,18,19,20,21,22,23,25,26,27,28,29,30,31,33,34,35,36,37,38,39);
		$arrSmall['m']=array(8,9,10,11,12,13,14,16,17,18,19,20,24,25,26,27,28,29,30);
		//$arrSmall['n']=array(7,8,9,10,11,12,13,16,17,18,19,20,21,22,24,25,26,27,28,29,30);
		$arrSmall['n']=array(15,12,11,10,9,8,23,22,21,18,17,16,31,30,29,28,27,24);
		$arrSmall['o']=array(9,10,11,12,13,14,17,18,19,20,21,22,25,26,27,28,29,30);
		$arrSmall['p']=array(8,9,10,11,13,14,16,17,18,19,21,22,24,25,26,27,29,30,32,33,34,35);
		$arrSmall['q']=array(0,8,10,11,12,13,14,16,18,19,20,21,22,24,33,34,35,36,37,38,39);
		$arrSmall['r']=array(8,9,10,13,14,16,17,18,21,22,24,25,27,29,30,34,35);
		$arrSmall['s']=array(1,2,3,9,10,11,13,14,17,18,19,21,22,25,26,27,29,30,37,38);
		$arrSmall['t']=array(0,1,2,3,4,5,6,8,9,10,11,12,13,14,24,25,26,27,28,29,30,32,33,34,35,36,37,38);
		$arrSmall['u']=array(9,10,11,12,13,14,15,17,18,19,20,21,22,23,25,26,27,28,29,30,31);
		$arrSmall['v']=array(0,1,8,10,11,12,13,14,15,17,18,19,20,21,22,23,24,26,27,28,29,30,31,32,33);
		$arrSmall['w']=array(9,10,11,12,13,14,15,19,20,21,22,23,25,26,27,28,29,30,31);
		$arrSmall['x']=array(2,3,4,5,8,9,11,12,14,15,16,17,18,21,22,23,24,25,27,28,30,31,34,35,36,37);
		$arrSmall['y']=array(0,1,2,3,8,9,10,11,13,14,15,21,22,23,24,25,26,27,29,30,31,32,33,34,35);
		$arrSmall['z']=array(2,3,4,5,6,9,11,12,13,14,17,18,20,21,22,25,26,27,29,30,33,34,35,36);


		switch($this->objStartingDirection)
		{
			case '0':
				$intOppositeDirection = 1;
			break;
			case '1':
				$intOppositeDirection = 2;
			break;
			case '2':
				$intOppositeDirection = 3;
			break;
			case '3':
				$intOppositeDirection = 0;
			break;
		}

		$current_y = $this->objStartingVector->y + $intElevation;
		$current_x = $this->objStartingVector->x;
		$current_z = $this->objStartingVector->z;
		$block_pos = new Vector3($current_x, $current_y, $current_z);
		foreach($arrFullString AS $strCurrentChar)
		{
			if(isset($arrSmall[$strCurrentChar]))
			{
				$arrRectangle = $this->__fncDrawRectangle(array('objIssuer'=>$objIssuer,'strStaticPlain'=>'vertical','intLength'=>5,'intWidth'=>8,'objStartingPos'=>$block_pos,'arrBlockType'=>$arrBlockID,'intCurrentDirection'=>$intOppositeDirection));
				$block_pos = $this->__fncCalculateStringPosition($block_pos ,$this->objStartingDirection,7);

				foreach($arrSmall[$strCurrentChar] AS $intCurrentRemoval)
				{
					if(isset($arrRectangle[$intCurrentRemoval]))
					{
						$objIssuer->level->setBlock($arrRectangle[$intCurrentRemoval], $objBackgroundBlock, $objBackgroundBlock->getMetadata());
					}
				}
			}
			elseif ($strCurrentChar == ' ')
			{
				$block_pos = $this->__fncCalculateStringPosition($block_pos ,$this->objStartingDirection,5);
			}
		}
		return $this->arrReturnMessage['string'];
	}

	private function __fncCalculateStringPosition($block_pos,$intCurrentDirection, $intCount)
	{
		$current_y = $block_pos->y;
		$current_x = $block_pos->x;
		$current_z = $block_pos->z;

		switch($intCurrentDirection)
		{
			case '0':
					$current_z = $current_z + $intCount;
			break;
			case '1':
				$current_x = $current_x-+ $intCount;
			break;
			case '2':
				$current_z = $current_z - $intCount;
			break;
			case '3':
				$current_x = $current_x + $intCount;
			break;
		}

		$block_pos = new Vector3($current_x, $current_y, $current_z);
		return $block_pos;
	}

	private function __fncPlay($arrParams,$objIssuer)
	{
		$objCurrentSaved = json_decode($this->arrSavedMacros[$arrParams[0]],true);

		$this->blnPlaying = 1;
		for($step=0;$step < count($objCurrentSaved);$step++)
		{
			$objCurrentStep = $objCurrentSaved[$step];
			//if first step, start at current position
			if($step == 0)
			{
				$this->commandHandler($objCurrentStep['strCmd'],$objCurrentStep['arrParams'],$objIssuer,$objCurrentStep['strAlias']);
				$intNextX = $objIssuer->entity->x;
				$intNextY = $objIssuer->entity->y;
				$intNextZ = $objIssuer->entity->z;
				$intCurrentDirection = $objIssuer->entity->getDirection();

				//indicate how many positions the direction has changed from when it was recorded. This will be used to adjust all steps.
				$intNeededRotation = $intCurrentDirection - $objCurrentStep['intCurrentDirection'];
			}
			else
			{
				$objPreviousStep = $objCurrentSaved[$step-1];
				$intNextDirection = $objCurrentSaved[$step]['intCurrentDirection'] + $intNeededRotation;

				if ($intNextDirection > 3) $intNextDirection = $intNextDirection - 4;
				elseif ($intNextDirection < 0) $intNextDirection = $intNextDirection + 4;

				$intXDiff = ceil($objCurrentStep['objStartingVector']['x']) - ceil($objPreviousStep['objStartingVector']['x']);
				$intZDiff = ceil($objCurrentStep['objStartingVector']['z']) - ceil($objPreviousStep['objStartingVector']['z']);

				//need to figure out how much the user moved when next statement was ran
				switch($intNeededRotation)
				{
					case 0:
						$intNextX = $intNextX + $intXDiff;
						$intNextZ = $intNextZ + $intZDiff;
					break;
					case 2:
					case -2:
						$intNextX = $intNextX - $intXDiff;
						$intNextZ = $intNextZ - $intZDiff;
					break;

					case 1:
					case -3:
						$intNextX = $intNextX - $intZDiff;
						$intNextZ = $intNextZ + $intXDiff;

					break;
					case -1:
					case 3:
						$intNextX = $intNextX + $intZDiff;
						$intNextZ = $intNextZ - $intXDiff;
					break;
				}

				$intYDiff = ceil($objCurrentStep['objStartingVector']['y']) - ceil($objPreviousStep['objStartingVector']['y']);
				$intNextY = $intNextY + $intYDiff;

				$objNewVector = new Vector3($intNextX, $intNextY, $intNextZ);
				$this->commandHandler($objCurrentStep['strCmd'],$objCurrentStep['arrParams'],$objIssuer,$objCurrentStep['strAlias'],$objNewVector,$intNextDirection);
			}
		}
		$this->blnPlaying = 0;
		return $this->arrReturnMessage['play'];
	}

	private function __fncDrawRectangle($criteria = array())
	{
		//$objIssuer (required) is the person who issues the command
		if (!isset($criteria['objIssuer']))
		{
			return false;
		}

		$arrRectangle = array();
		$objIssuer = (isset($criteria['objIssuer'])) ? $criteria['objIssuer'] : '';
		//$strStaticPlain is the plain on which stays static, can be horizontal or vertical
		$strStaticPlain = (isset($criteria['strStaticPlain'])) ? $criteria['strStaticPlain'] : 'z';
		//$objStartingPos is the vector to begin drawing, defaults to the user's position.
		$objStartingPos = (isset($criteria['objStartingPos'])) ? $criteria['objStartingPos'] : new Vector3($objIssuer->entity->x, $objIssuer->entity->y, $objIssuer->entity->z);
		//$intLength is the Length
		$intLength = (isset($criteria['intLength'])) ? (int) $criteria['intLength'] : $this->arrDefaults[$objIssuer->username]['length'];
		//$intWidth is the width
		$intWidth = (isset($criteria['intWidth'])) ? (int) $criteria['intWidth'] : $this->arrDefaults[$objIssuer->username]['width'];
		//$arrBlockType is the type of block to use.
		$arrBlockType = (isset($criteria['arrBlockType'])) ? $criteria['arrBlockType'] : array($this->arrDefaults[$objIssuer->username]['block'],$this->arrDefaults[$objIssuer->username]['block_sub']);
		
		if (isset($criteria['arrBlockReplaceType']) && !empty($criteria['arrBlockReplaceType']))
		{
			$arrBlockReplaceType = $criteria['arrBlockReplaceType'][0];
		}
		
		$objBlock = $this->api->block->get($arrBlockType[0], $arrBlockType[1]);

		//$intCurrentDirection is 0,1,2,3 indicating the direction that the wall needs to be built. default is the way the player is facing.
		$intCurrentDirection = (isset($criteria['intCurrentDirection'])) ? $criteria['intCurrentDirection'] : $this->objStartingDirection;

		$intCurrent_x = $objStartingPos->x;
		$intCurrent_y = $objStartingPos->y;
		$intCurrent_z = $objStartingPos->z;

		$intNewFirstLevel = 0;
		$intNewSecondLevel = 0;

		$arrPositioning = array();

		switch($strStaticPlain)
		{
			case 'horizontal':
				if ($intCurrentDirection == 0 || $intCurrentDirection == 2)
				{
					$intFirstLevel = $intCurrent_x;
					$intSecondLevel = $intCurrent_z;
					$arrPositioning['x'] = &$intNewFirstLevel;
					$arrPositioning['y'] = $intCurrent_y;
					$arrPositioning['z'] = &$intNewSecondLevel;
				}
				else
				{
					$intFirstLevel = $intCurrent_z;
					$intSecondLevel = $intCurrent_x;
					$arrPositioning['z'] = &$intNewFirstLevel;
					$arrPositioning['y'] = $intCurrent_y;
					$arrPositioning['x'] = &$intNewSecondLevel;
				}
			break;
			case 'vertical':
				if ($intCurrentDirection == 1 || $intCurrentDirection == 3)
				{
					$intFirstLevel = $intCurrent_z;
					$intSecondLevel = $intCurrent_y;
					$arrPositioning['x'] = $intCurrent_x;
					$arrPositioning['y'] = &$intNewSecondLevel;
					$arrPositioning['z'] = &$intNewFirstLevel;
				}
				else
				{
					$intFirstLevel = $intCurrent_x;
					$intSecondLevel = $intCurrent_y;
					$arrPositioning['z'] = $intCurrent_z;;
					$arrPositioning['y'] = &$intNewSecondLevel;
					$arrPositioning['x'] = &$intNewFirstLevel;
				}
			break;
		}

		for($i = 1; $i <= $intLength; $i++)
		{
			if ($intCurrentDirection == 0 || $intCurrentDirection == 1)
			{
				$intNewFirstLevel =  $intFirstLevel + $i;
			}
			else
			{
				$intNewFirstLevel =  $intFirstLevel - $i;
			}

			for($j = 0; $j < $intWidth; $j++)
			{
				if ($strStaticPlain == 'vertical' || $intCurrentDirection == 3 || $intCurrentDirection == 0)
				{
					$intNewSecondLevel = $intSecondLevel + $j;
				}
				else
				{
					$intNewSecondLevel = $intSecondLevel - $j;
				}

				$block_pos = new Vector3($arrPositioning['x'], $arrPositioning['y'], $arrPositioning['z']);



				$this->__fncSetRollback($objIssuer,$block_pos);
				$arrRectangle[] = $block_pos;
				
				if (isset($arrBlockReplaceType))
				{
					$objCurrentBlock = $objIssuer->level->getBlockRaw($block_pos);
					$intCurrentBlockID = $objCurrentBlock->getID();
					
					
					if ($intCurrentBlockID == $arrBlockReplaceType)
					{
						$objIssuer->level->setBlock($block_pos, $objBlock, $objBlock->getMetadata());
					}
				}
				else
				{
					$objIssuer->level->setBlock($block_pos, $objBlock, $objBlock->getMetadata());
				}
				
				
			}
		}

		return $arrRectangle;
	}

	private function __fncGetBlockID($objIssuer,$strInput)
	{
		$arrTempBlockData = explode("|",$strInput);
		$arrColors = array('orange'=>1,'magenta'=>2,'light_blue'=>3,'yellow'=>4,'lime'=>5,'pink'=>6,'gray'=>7,'light_gray'=>8,'cyan'=>9,'purple'=>10,'blue'=>11,'brown'=>12,'green'=>13,'red'=>14,'black'=>15);

		if (is_numeric($arrTempBlockData[0]))
		{
			$arrBlockData[0] = $arrTempBlockData[0];
		}
		elseif (defined(strtoupper($arrTempBlockData[0]) . "_BLOCK"))
		{
			$arrBlockData[0]  = constant(strtoupper($arrTempBlockData[0]) . "_BLOCK");
		}
		elseif (defined(strtoupper($arrTempBlockData[0])))
		{
			$arrBlockData[0]  = constant(strtoupper($arrTempBlockData[0]));
		}
		else
		{
			$arrBlockData[0]  = $this->arrDefaults[$objIssuer->username]['block'];
		}



		if (isset($arrTempBlockData[1]) && is_numeric($arrTempBlockData[1]))
		{
			$arrBlockData[1] = (int) $arrTempBlockData[1];
		}
		elseif(isset($arrColors[$arrTempBlockData[1]]))
		{
			$arrBlockData[1] = $arrColors[$arrTempBlockData[1]];
		}
		else
		{
			$arrBlockData[1] = $this->arrDefaults[$objIssuer->username]['block_sub'];
		}

		return $arrBlockData;
	}

	private function __fncSetupUserDefaults($objIssuer)
	{

		if (isset($this->arrDefaults[$objIssuer->username])) return;

		//set up the defaults for the new user
		$this->arrDefaults[$objIssuer->username]['block'] = 3;
		$this->arrDefaults[$objIssuer->username]['block_sub'] = 0;
		$this->arrDefaults[$objIssuer->username]['length'] = 5;
		$this->arrDefaults[$objIssuer->username]['width'] = 5;
		$this->arrDefaults[$objIssuer->username]['height'] = 5;
		$this->arrDefaults[$objIssuer->username]['depth'] = 4;
		$this->arrDefaults[$objIssuer->username]['size'] = 5;
		$this->arrDefaults[$objIssuer->username]['elevation'] = 0;
		$this->arrDefaults[$objIssuer->username]['radius'] = 5;

		$this->api->plugin->writeYAML($this->api->plugin->configPath($this)."config.yml", $this->arrDefaults);
	}

	private function __fncHelp($strAlias, $strSubCommand = '')
	{
		$strOutput = '';

		switch($strSubCommand)
		{
			case 'undo':
				$strOutput .= "Usage: /$strAlias undo\n";
				$strOutput .= "This command takes no params\n";
				$strOutput .= "It will undo the last /$strAlias command\n";
				$strOutput .= "Currently it can only undo 1 command\n";
			break;

			case 'repeat':
				$strOutput .= "Usage: /$strAlias repeat\n";
				$strOutput .= "This command takes no params\n";
				$strOutput .= "It will repeat the last /$strAlias command\n";
				$strOutput .= "This command can save time\n";
			break;

			case 'pool':
				$strOutput .= "Usage: /$strAlias pool w:10 h:10 d:3\n";
				$strOutput .= "Optional params:\n";
				$strOutput .= "(w)idth, (h)eight, and (d)epth\n";
				$strOutput .= "It will create a pool of water in front of you.\n";
			break;

			case 'floor':
				$strOutput .= "Usage: /$strAlias floor b:gold l:12 w:12 h:-1\n";
				$strOutput .= "Optional params:\n";
				$strOutput .= "(b)lock, (l)ength, (w)idth, and (e)levation \n";
				$strOutput .= "This will draw a floor in front of you.\n";
				$strOutput .= "e:-1 will draw on the level you are standing.\n";
				$strOutput .= "e:6 will draw a ceiling.\n";
			break;

			case 'wall':
				$strOutput .= "Usage: /$strAlias wall b:iron l:8 h:4\n";
				$strOutput .= "Optional params:\n";
				$strOutput .= "(b)lock, (l)ength,(h)eight, and (e)elevation\n";
				$strOutput .= "This will draw a wall in front of you.\n";
				$strOutput .= "Your direction will control the wall direction.\n";
			break;

			case 'box':
				$strOutput .= "Usage: /$strAlias box s:15 b:dirt\n";
				$strOutput .= "Optional params:\n";
				$strOutput .= "(s)ize, (e)elevation, and (b)lock,\n";
				$strOutput .= "This will draw a hollow square box in front of you.\n";
				$strOutput .= "Max size of 30.\n";
			break;

			case 'cube':
				$strOutput .= "Usage: /$strAlias cube s:5 b:diamond\n";
				$strOutput .= "Optional params:\n";
				$strOutput .= "(s)ize, (e)elevation, and (b)lock,\n";
				$strOutput .= "This will draw a solid square cube in front of you.\n";
				$strOutput .= "Max size of 30.\n";
			break;

			case 'pyramid':
				$strOutput .= "Usage: /$strAlias pyramid s:40 b:sand\n";
				$strOutput .= "Optional params:\n";
				$strOutput .= "(s)ize, (e)elevation, and (b)lock,\n";
				$strOutput .= "This will draw a solid pyramid in front of you.\n";
				$strOutput .= "Max size of 49.\n";
			break;

			case 'diamond':
				$strOutput .= "Usage: /$strAlias diamond s:20 b:sand\n";
				$strOutput .= "Optional params:\n";
				$strOutput .= "(s)ize, (e)elevation, and (b)lock,\n";
				$strOutput .= "This will draw a solid pyramid in front of you.\n";
				$strOutput .= "Max size of 49.\n";
			break;

			case 'sphere':
				$strOutput .= "Usage: /$strAlias sphere r:15 b:sand\n";
				$strOutput .= "Optional params:\n";
				$strOutput .= "(r)adius, (e)elevation, and (b)lock,\n";
				$strOutput .= "This will draw a solid sphere in front of you.\n";
				$strOutput .= "Max radius of 20.\n";
			break;

			case 'steps':
				$strOutput .= "Usage: /$strAlias pyramid s:40 b:sand\n";
				$strOutput .= "Optional params:\n";
				$strOutput .= "(h)eight, (w)idth, (e)elevation, and (b)lock,\n";
				$strOutput .= "This will draw a steps in front of you.\n";
				$strOutput .= "Max size of 30.\n";
			break;

			case 'write':
				$strOutput .= "Usage: /$strAlias write b:gold t:test message\n";
				$strOutput .= "Optional params:\n";
				$strOutput .= "(t)ext, (e)elevation, and (b)lock,\n";
				$strOutput .= "This will write a block message for you.\n";
				$strOutput .= "Each letter is 8 blocks hight and 5 blocks wide.\n";
			break;

			case 'prism':
				$strOutput .= "Usage: /$strAlias prism b:gold w:10 h:5 l:15 e:5\n";
				$strOutput .= "Optional params:\n";
				$strOutput .= "(b)lock, (l)ength, (w)idth, (h)eight, and (e)elevation\n";
				$strOutput .= "This will draw a rectangle prism with the given dimensions.\n";
				$strOutput .= "Max length, width, height of 30.\n";
			break;

			case 'cut':
				$strOutput .= "Usage: /$strAlias cut w:10 h:5 l:15 e:5\n";
				$strOutput .= "Optional params:\n";
				$strOutput .= "(l)ength, (w)idth, (h)eight, and (e)elevation\n";
				$strOutput .= "This will replace given rectangle prism with air.\n";
			break;
			
			case 'replace':
				$strOutput .= "Usage: /$strAlias replace br:lava b:air w:10 h:5 l:15\n";
				$strOutput .= "Optional params:\n";
				$strOutput .= "(b)lock, (br)block_replace, (l)ength, (w)idth, (h)eight, and (e)elevation\n";
				$strOutput .= "This will replace the block_replace type with the given block.\n";
			break;

			case 'record':
				$strOutput .= "Usage: /$strAlias record start|save|cancel|delete\n";
				$strOutput .= "Allows user to save andy /draw commands:\n";
				$strOutput .= "Save and Delete will also need a name param.\n";
			break;

			case 'play':
				$strOutput .= "Usage: /$strAlias play house_shell\n";
				$strOutput .= "Play a saved named macro. Used for repeating things over and over.\n";
			break;

			case 'set':
				$strOutput .= "Usage: /$strAlias set b:gold\n";
				$strOutput .= "Allows you to change the defaults values.\n";
				$strOutput .= "Possible defaults:\n";
				$strOutput .= "block, block_sub, height, width, length, elevation, size, depth, radius\n";
			break;

			default:
				$strOutput .= "Usage: /$strAlias <command> [parameters...]\n";
				$strOutput .= "Possible commands:\n";
				$strOutput .= "floor, wall, pool, box, cube, pyramid, diamond, steps, sphere, write, prism, cut, replace, repeat, undo, record, play, set\n";
				$strOutput .= "/$strAlias <command> help for more details.\n";
		}

		return $strOutput;
	}

	public function __destruct(){}
}
?>