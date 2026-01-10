<?php
namespace OreFinder;

use pocketmine\block\Block;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\math\Vector3;
use pocketmine\network\protocol\UpdateBlockPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class OreFinder extends PluginBase implements CommandExecutor, Listener{
    public $s, $config;
    public function onEnable(){
        @mkdir($this->getDataFolder());
        $this->s = [];
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, ["size" => 4, "ores" => [16, 15, 21, 14, 56, 73, 74, 129]]);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("启用.");
    }
    public function onDisable(){
        foreach($this->s as $p => $blocks){
            if(($p = $this->getServer()->getPlayer($p)) instanceof Player){
                $this->revertDisplay($blocks, $p);
            }
        }
        $this->getLogger()->info("禁用.");
    }
    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
        if($sender instanceof Player){
            if(isset($this->s[$sender->getName()])){
                $sender->sendMessage("已经打开.");
                return true;
            }
            else{
                $sender->sendMessage("寻找...");
                $this->s[$sender->getName()] = $this->fetchBlocks($sender);
                $this->renderBlocks($this->s[$sender->getName()], $sender);
                $sender->sendMessage("请在游戏里使用.");
                return true;
            }
        }
        else{
            $sender->sendMessage("请在游戏里使用.");
            return true;
        }
    }
    public function onInteract(PlayerInteractEvent $event){
        if(isset($this->s[$event->getPlayer()->getName()])){
                $event->getPlayer()->sendMessage("钻石显示.");
                $this->revertDisplay($this->s[$event->getPlayer()->getName()], $event->getPlayer());
                unset($this->s[$event->getPlayer()->getName()]);
        }
    }
    public function onBlockBreak(BlockBreakEvent $event){
        if(isset($this->s[$event->getPlayer()->getName()])){
            $event->getPlayer()->sendMessage("钻石显示.");
            $this->revertDisplay($this->s[$event->getPlayer()->getName()], $event->getPlayer());
            unset($this->s[$event->getPlayer()->getName()]);
            $event->setCancelled();
        }
    }
    public function onBlockPlace(BlockPlaceEvent $event){
        if(isset($this->s[$event->getPlayer()->getName()])){
            $event->getPlayer()->sendMessage("钻石显示.");
            $this->revertDisplay($this->s[$event->getPlayer()->getName()], $event->getPlayer());
            unset($this->s[$event->getPlayer()->getName()]);
        }
    }
    public function onQuit(PlayerQuitEvent $event){
        if(isset($this->s[$event->getPlayer()->getName()])){
            unset($this->s[$event->getPlayer()->getName()]);
        }
    }
    public function fetchBlocks(Player $p){
        $ret = [];
        $s = $this->config->get("size");
        for($x = $p->getFloorX()-$s; $x <= $p->getFloorX()+$s; $x++){
            for($y = $p->getFloorY()-$s; $y <= $p->getFloorY()+$s; $y++){
                for($z = $p->getFloorZ()-$s; $z <= $p->getFloorZ()+$s; $z++){
                    $block = $p->getLevel()->getBlock(new Vector3($x, $y, $z));
                    if($block->getID() !== 0 || $y == $p->getFloorY()-1){
                        if(!$this->isOre($block)){
                            $ret[] = $block;
                        }
                    }
                }
            }
        }
        return $ret;
    }
    public function renderBlocks(array $blocks, Player $p){
        foreach($blocks as $block){
            $pk = new UpdateBlockPacket;
            $pk->x = $block->getX();
            $pk->y = $block->getY();
            $pk->z = $block->getZ();
            $pk->block = ($block->getY() == $p->getFloorY()-1 ? 20 : 0);
            $pk->meta = 0;
            $p->dataPacket($pk);
        }
    }
    public function revertDisplay(array $blocks, Player $p){
        foreach($blocks as $block){
            $pk = new UpdateBlockPacket;
            $pk->x = $block->getX();
            $pk->y = $block->getY();
            $pk->z = $block->getZ();
            $pk->block = $block->getID();
            $pk->meta = $block->getDamage();
            $p->dataPacket($pk);
        }
    }
    public function isOre(Block $block){
        return in_array($block->getID(), $this->config->get("ores"));
    }
}