<?php

/*
 * This file is a part of WalkingParticles.
 * Copyright (C) 2015  CyberCube-HK
 *
 * WalkingParticles is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * WalkingParticles is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WalkingParticles.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace WalkingParticles\base;
 
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandExecutor;

use WalkingParticles\WalkingParticles;

abstract class BaseCommand extends PluginBase implements CommandExecutor{
  public $plugin;
  
  public function __construct(WalkingParticles $plugin){
    $this->plugin = $plugin;
  }
  
  public final function getPlugin(){
    return $this->plugin;
  }
  
  public final function getConfig(){
    return $this->plugin->getConfig();
  }
  
}
?>
