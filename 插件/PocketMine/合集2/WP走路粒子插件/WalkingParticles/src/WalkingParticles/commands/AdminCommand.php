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
 
namespace WalkingParticles\commands;

use WalkingParticles\base\BaseCommand;
use WalkingParticles\Particle;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;

class AdminCommand extends BaseCommand{
  
  public function onCommand(CommandSender $issuer, Command $cmd, $label, array $args){
   switch($cmd->getName()):
     case "walkingparticles":
       if($issuer->hasPermission("walkingparticles.command") || $issuer->hasPermission("walkingparticles.command.admin")){
         if(isset($args[0])){
           switch($args[0]):
             case "help":
             case "h":
               $issuer->sendMessage("§7帮助列表");
               $issuer->sendMessage("§a默认粒子:§b/wparticles defaultparticle <particle>");
               $issuer->sendMessage("§a默认强度:§b/wparticles defaultamplifier <amplifier>");
               $issuer->sendMessage("§e添加粒子:§b/wparticles add <particle> <player>");
               $issuer->sendMessage("§c删除:§b/wparticles remove <particle> <player>");
               $issuer->sendMessage("§a粒子强度:§b/wparticles amplifier <amplifier> <player>");
               $issuer->sendMessage("§a粒子模式:§b/wparticles display line|group <player>");
               $issuer->sendMessage("§a清除粒子:§b/wparticles clear <player>");
               $issuer->sendMessage("§a获取使用中粒子：§b/wparticles get <player>");
               $issuer->sendMessage("§a粒子列表：§b/wplist");
               return true;
             break;
             case "setdefaultamplifier":
             case "defaultamplifier":
               if(isset($args[1])){
                 if(is_numeric($args[1])){
                   $this->getConfig()->set("default-amplifier", $args[1]);
                   $this->getConfig()->save();
                   $issuer->sendMessage("§e默认强度已修改");
                   return true;
                 }else{
                   $issuer->sendMessage("§cInvalid amplifier!");
                   return true;
                 }
               }else{
                 $issuer->sendMessage("§fUsage: /wparticles defaultamplifier <amplifier>");
                 return true;
               }
             break;
             case "setdefaultparticle":
             case "defaultparticle":
               if(isset($args[1])){
                 $particle = $args[1];
                 $this->getConfig()->set("default-particle", $particle);
                 $this->getConfig()->save();
                 $issuer->sendMessage("§e默认粒子已修改!");
                 return true;
               }else{
                 $issuer->sendMessage("§fUsage: /wparticles setdefault <particle>");
                 return true;
               }
             break;
            case "add":
            case "addparticle":
              if(isset($args[1])){
                $particle = $args[1];
                if(isset($args[2])){
                  $target = $this->getPlugin()->getServer()->getPlayer($args[2]);
                  if($target !== null){
                    if($particle == "all"){
                      foreach($this->getPlugin()->getParticles()->getAll() as $ps){
                        $this->getPlugin()->clearPlayerParticle($target);
                        $this->getPlugin()->addPlayerParticle($target, $ps);
                        $issuer->sendMessage("§a添加所有粒子给 ".$target->getName());
                        return true;
                      }
                    }else{
                      $this->getPlugin()->addPlayerParticle($target, $particle);
                      $issuer->sendMessage("§a将 ".$particle." 添加给了§b".$target->getName()."!");
                      return true;
                    }
                  }else{
                    $issuer->sendMessage("§cInvalid target!");
                  }
                }else{
                  if($issuer instanceof Player){
                    if($particle == "all"){
                      foreach($this->getPlugin()->getParticles()->getAll() as $ps){
                        $this->getPlugin()->clearPlayerParticle($issuer);
                        $this->getPlugin()->addPlayerParticle($issuer, $ps);
                        $issuer->sendMessage("§aAdded §l§bALL §r§aparticles to you!");
                        return true;
                      }
                    }else{
                      $this->getPlugin()->addPlayerParticle($issuer, $particle);
                      $issuer->sendMessage("§a给自己添加了 ".$particle." 特效!");
                      return true;
                    }
                  }else{
                    $issuer->sendMessage("Usage: /wparticles add <particle> <player>");
                    return true;
                  }
                }
              }else{
                $issuer->sendMessage("Usage: /wparticles add <particle> <player>");
                return true;
              }
            break;
            case "removeparticle":
            case "rmparticle":
            case "remove":
              if(isset($args[1])){
                $particle = $args[1];
                if(isset($args[2])){
                  $target = $this->getPlugin()->getServer()->getPlayer($args[2]);
                  if($target !== null){
                    $this->getPlugin()->removePlayerParticle($target, $particle);
                    $issuer->sendMessage("§a你删除了 §b".$target->getName()."§a的粒子!");
                    return true;
                  }else{
                    $issuer->sendMessage("§cInvalid target!");
                    return true;
                  }
                }else{   
                  if($issuer instanceof Player){
                    $this->getPlugin()->removePlayerParticle($issuer, $particle);
                    $issuer->sendMessage("§a你的 '§b".$particle."§a粒子已经删除!");
                    return true;
                  }else{
                    $issuer->sendMessage("Usage: /wparticles remove <particle> <player>");
                    return true;
                  }  
                }
              }else{
                $issuer->sendMessage("Usage: /wparticles remove <particle> <player>");
                return true;
              }
            break;
            case "setamplifier":
            case "amplifier":
              if(isset($args[2]) && isset($args[1])){
                if(is_numeric($args[1])){
                  $target = $this->getPlugin()->getServer()->getPlayer($args[2]);
                  if($target !== null){
                    $amplifier = $args[1];
                    $this->getPlugin()->setPlayerAmplifier($target, $amplifier);
                    $issuer->sendMessage("§a你改变了 §b".$target->getName()."§a得强度!");
                    return true;
                  }else{
                    $issuer->sendMessage("§cInvalid target!");
                    return true;
                  }
                }else{
                  $issuer->sendMessage("§cInvalid amplifier!");
                  return true;
                }
              }else if(isset($args[1]) && !isset($args[2])){
                if(is_numeric($args[1]) !== false){
                  if($issuer instanceof Player){
                    $amplifier = $args[1];
                    $this->getPlugin()->setPlayerAmplifier($issuer, $amplifier);
                    $issuer->sendMessage("§a你改变了你的粒子强度§a!");
                    return true;
                  }else{
                    $issuer->sendMessage("Usage: /wparticles amplifier <amplifier> <player>");
                    return true;
                  }
                }else{
                  $issuer->sendMessage("§cInvalid amplifier!");
                  return true;
                }
              }else{
                $issuer->sendMessage("§fUsage: /wparticles amplifier <amplifier> <player>");
                return true;
              }
            break;
            case "display":
              if(isset($args[1]) && isset($args[2])){
                $target = $this->getPlugin()->getServer()->getPlayer($args[2]);
                if($target !== null){
                    switch($args[1]):
                      case "line":
                        $this->getPlugin()->setPlayerDisplay($target, "line");
                        $issuer->sendMessage("§aYou set §e".$target->getName()."§a's display to §bline§a!");
                        return true;
                      case "group":
                        $this->getPlugin()->setPlayerDisplay($target, "group");
                      $issuer->sendMessage("§aYou set §e".$target->getName()."§a's display to §bgroup§a!");
                        return true;
                      default:
                        $issuer->sendMessage("Usage: /wparticles display line|group <target>");
                        return true;
                    endswitch;
                  }else{
                    $issuer->sendMessage("§cInvalid target!");
                    return true;
                  }
              }else if(isset($args[1]) && !isset($args[2])){
                if($issuer instanceof Player){
                  switch($args[1]):
                    case "line":
                      $this->getPlugin()->setPlayerDisplay($issuer, "line");
                      $issuer->sendMessage("§aYou set your display to §bline§a!");
                      return true;
                    case "group":
                      $this->getPlugin()->setPlayerDisplay($issuer, "group");
                      $issuer->sendMessage("§aYou set your display to §bgroup§a!");
                      return true;
                    default:
                      $issuer->sendMessage("Usage: /wparticles display line|group");
                      return true;
                  endswitch;
                }
              }else{
                $issuer->sendMessage("Usage: /wparticles display line|group <target>");
                return true;
              }
            break;
            case "clear":
            case "stop":
              if(isset($args[1])){
                $target = $this->getPlugin()->getServer()->getPlayer($args[1]);
                if($target !== null){
                  if($this->getPlugin()->isCleared($target) !== true){
                    $this->getPlugin()->clearPlayerParticle($target);
                    $issuer->sendMessage("§aYou cleared §b".$target->getName()."§a's WalkingParticles!");
                    $target->sendMessage("§aYour §bWalkingParticles §ahas been cleared!");
                    return true;
                  }else{
                    $issuer->sendMessage("§cThere is no particle in use!");
                    return true;
                  }
                }else{
                  $issuer->sendMessage("§cInvalid target!");
                  return true;
                }
              }else{
                if($this->getPlugin()->isCleared($issuer) !== true){
                  $this->getPlugin()->clearPlayerParticle($issuer);
                  $issuer->sendMessage("§aYour §bWalkingParticles §ahas been cleared!");
                  return true;
                }else{
                  $issuer->sendMessage("§cThere are no particles in use!");
                  return true;
                }
              }
            break;
            case "get":
              if(isset($args[1])){
                $target = $this->getPlugin()->getServer()->getPlayer($args[1]);
                if($target !== null){
                  $issuer->sendMessage("§e".$target->getName()."§a's §bWalkingParticles§a: §f".$this->getPlugin()->getAllPlayerParticles($target));
                  return true;
                }else{
                  $issuer->sendMessage("§cInvalid target!");
                  return true;
                }
              }else{
                $issuer->sendMessage("§aYour §bWalkingParticles§a: §f".$this->getPlugin()->getAllPlayerParticles($issuer));
                return true;
              }
            break;
            case "list":
              $issuer->sendMessage("§aList of available particles: §6explode / bubble / splash / water / critical / spell / dripwater / driplava / spore / portal / flame / lava / reddust / heart / ink / snowball / smoke / entityflame");
              return true;
            break;
          endswitch;
        }else{
          return false;
        }
      }else{
        $issuer->sendMessage("§cYou don't have permission for this!");
        return true;
      }
    break;
   endswitch;
 }
  
}
?>
