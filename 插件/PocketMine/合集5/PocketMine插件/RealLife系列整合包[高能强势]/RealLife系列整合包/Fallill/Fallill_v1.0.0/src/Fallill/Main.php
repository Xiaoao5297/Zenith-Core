<?php
/**
 * Created by PhpStorm.
 * User: ASUS-
 * Date: 2017/8/24
 * Time: 11:24
 */

namespace Fallill;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Effect;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as C;
use pocketmine\utils\config;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\entity\EntityDamageEvent;

class Main extends PluginBase implements Listener
{
    public $worlds;
    public function onEnable()
    {
        $this->getLogger()->info("RealLife系列之五 生病系统 已加载");
        $this->getLogger()->info("RealLife是Spiderman正在开发的仿生功能插件,坚持做到与人们平时生活所遇到的一致");
        @mkdir($this->getDataFolder(),0777,true);
        $this->worlds = new Config($this->getDataFolder()."config.yml", Config::YAML, array("worlds"=>array("world")));
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    public function onCommand(CommandSender $s, Command $command, $label, array $args)
    {
        switch($command->getName())
        {
            case "ill":
                $s->sendMessage(C::GRAY .      "-=§l§dRealLife§r§7=-=====================================");
                $s->sendMessage(C::DARK_AQUA . ">  特效药   §b适合症状:眩晕  §d治疗方法：食用 §6物品：苹果 ");
                $s->sendMessage(C::GOLD .      ">  骨头     §b适合症状:骨折  §d治疗方法：点地 §6物品：骨头");
                $s->sendMessage(C::WHITE .     ">  南瓜派   §b适合症状:虚弱  §d治疗方法：食用 §6物品：南瓜派");
                $s->sendMessage(C::YELLOW .    ">  士力架   §b适合症状:饥饿  §d治疗方法：食用 §6物品：面包");
                $s->sendMessage(C::GREEN .     ">  曲奇     §b适合症状:疲劳  §d治疗方法：食用 §6物品：曲奇");
                $s->sendMessage(C::AQUA .      ">  维生素A  §b适合症状:失明  §d治疗方法：食用 §6物品：胡萝卜");
                $s->sendMessage(C::RED .       ">  云南白药 §b适合症状:中毒  §d治疗方法：点地  §6物品：纸");
                return true;
                break;
        }
    }

    public function onHeld(PlayerItemHeldEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $itemid = $item->getId();
        $level = $player->getLevel()->getFolderName();
        if(in_array($level,$this->worlds->get("worlds")))
        {
            switch($itemid)
            {
                case 260://苹果
                    $player->sendMessage(C::DARK_AQUA . ">  特效药 §b适合症状:眩晕 §d治疗方法：食用 §6物品：苹果 ");
                    break;
                case 352://骨头
                    $player->sendMessage(C::GOLD .      ">  骨头 §b适合症状:骨折 §d治疗方法：点地 §6物品：骨头");
                    break;
                case 400: //南瓜派
                    $player->sendMessage(C::WHITE .     ">  南瓜派 §b适合症状:虚弱 §d治疗方法：点地 §6物品：南瓜派");
                    break;
                case 297: //面包
                    $player->sendMessage(C::YELLOW .    ">  士力架 §b适合症状:饥饿 §d治疗方法：食用 §6物品：面包");
                    break;
                case 357://曲奇
                    $player->sendMessage(C::GREEN .     ">  曲奇 §b适合症状:疲劳 §d治疗方法：食用 §6物品：曲奇");
                    break;
                case 391://胡萝卜
                    $player->sendMessage(C::AQUA .      ">  维生素A §b适合症状:失明 §d治疗方法：食用 §6物品：胡萝卜");
                    break;
                case 339://纸
                    $player->sendMessage(C::RED .       ">  云南白药§b适合症状:中毒 §d治疗方法：点地  §6物品：纸");
                    break;
            }
        }
    }
    public function onPlayerInteract(PlayerInteractEvent $event)
    {

        $player = $event->getPlayer();
        $inventory = $player->getInventory();
        $item = $event->getItem();
        $level = $player->getLevel()->getFolderName();
        if(in_array($level,$this->worlds->get("worlds")))
        {
            switch($item->getId())
            {
                case 352:
                    if($player->hasEffect(Effect::SLOWNESS))
                    {
                        $player->removeEffect(Effect::SLOWNESS);
                        $inventory->removeItem(new Item(352, 0, 1));
                        $player->sendMessage(C::GRAY . "-=§l§dRealLife§r§7=- 你已换骨");
                    }
                    break;
                case 339:
                    if($player->hasEffect(Effect::POISON))
                    {
                        $player->removeEffect(Effect::POISON);
                        $inventory->removeItem(new Item(339, 0, 1));
                        $player->sendMessage(C::GRAY . "-=§l§dRealLife§r§7=- 已排毒");
                    }
                    break;
            }
        }

    }
    public function onPlayerEat(PlayerItemConsumeEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $itemid = $item->getId();
        $effect19 = Effect::getEffect(Effect::POISON)->setVisible(true)->setAmplifier(0)->setDuration(20*120);//中毒
        $level = $player->getLevel()->getFolderName();
        if(in_array($level,$this->worlds->get("worlds")))
        {
            switch($itemid)
            {
                case 260: //苹果---特效药
                    if($player->hasEffect(Effect::NAUSEA))
                    {
                        $player->removeEffect(Effect::NAUSEA);
                        $player->sendMessage(C::GRAY . "-=§l§dRealLife§r§7=- 已缓解头晕！");
                    }
                    break;
                case 297://面包---士力架
                    if($player->hasEffect(Effect::HUNGER))
                    {
                        $player->removeEffect(Effect::HUNGER);
                        $player->sendMessage(C::GRAY . "-=§l§dRealLife§r§7=- 横扫饥饿，做回自己！");
                    }
                    break;
                case 357://曲奇
                    if($player->hasEffect(Effect::FATIGUE))
                    {
                        $player->removeEffect(Effect::FATIGUE);
                        $player->sendMessage(C::GRAY . "-=§l§dRealSurvival§r§7=- 已缓解疲劳！");
                    }
                    break;
                case 400://南瓜派
                    if($player->hasEffect(Effect::WEAKNESS))
                    {
                        $player->removeEffect(Effect::WEAKNESS);
                        $player->sendMessage(C::GRAY . "-=§l§dRealLife§r§7=- 强身健体");
                    }
                    break;
                case 391://胡萝卜---维生素A
                    if($player->hasEffect(Effect::BLINDNESS))
                    {
                        $player->removeEffect(Effect::BLINDNESS);
                        $player->sendMessage(C::GRAY . "-=§l§dRealLife§r§7=- 成功治疗失明！");
                    }
                    break;
                case 392://马铃薯
                    $player->addEffect($effect19);
                    $player->sendMessage(C::GRAY . "-=§l§dRealLife§r§7=- 生吃马铃薯会中毒哟！");
                    break;
            }
        }

    }
    public function onFallDamage(EntityDamageEvent $event)
    {
        $player = $event->getEntity();
        $playername = $event->getEntity()->getName();
        $cause = $event->getCause();
        $health = $player->getHealth();

        $effect = Effect::getEffect(Effect::SLOWNESS)->setVisible(true)->setAmplifier(0)->setDuration(20*120);//缓慢
        $effect4 = Effect::getEffect(Effect::FATIGUE)->setVisible(true)->setAmplifier(0)->setDuration(20*120);//疲劳
        $effect9 = Effect::getEffect(Effect::NAUSEA)->setVisible(false)->setAmplifier(0)->setDuration(20*120); //反胃
        $effect17 = Effect::getEffect(Effect::HUNGER)->setVisible(true)->setAmplifier(0)->setDuration(20*120);//饥饿
        $effect18 = Effect::getEffect(Effect::WEAKNESS)->setVisible(true)->setAmplifier(0)->setDuration(20*120);//变弱

        $level = $player->getLevel()->getFolderName();
        if(in_array($level,$this->worlds->get("worlds")))
        {
            if($event instanceof EntityDamageByEntityEvnt)
            {
                if($event->getDamager()->getInventory()->getItemInHand()->getId() === 276)
                {
                    $event->setCancelled(true);
                    $player->sendTitle(C::AQUA."你被剑砍而感染开放性伤口",C::YELLOW ."有流血、虚弱、损伤效果","2","2","40");
                    $player->addEffect($effect18);
                    $player->setHealth($health-5);
                }
            }
            if($cause == EntityDamageEvent::CAUSE_FALL)//摔
            {
                $player->addEffect($effect);//缓慢
                $player->sendTitle(C::GREEN."你腿摔断了",C::GOLD."需要接骨！","2","2","40");
            }
            if($cause == EntityDamageEvent::CAUSE_LAVA OR $cause == EntityDamageEvent::CAUSE_FIRE)//熔岩、烧
            {
                $player->sendTitle(C::RED."你浴火纵身",C::GOLD."直接死亡","2","2","40");
                sleep(2);
                $player->kill();
                $this->getServer()->broadcastMessage(C::GRAY . "-=§l§dRealLife§r§7=- §a玩家§b§l". "$playername". "§a§r浴火纵身,直接死亡");
            }
            if($cause == EntityDamageEvent::CAUSE_DROWNING)//溺水
            {
                $player->addEffect($effect4); //疲劳
                $player->addEffect($effect18); //变弱
                $player->addEffect($effect9); //反胃
                $player->sendTitle(C::YELLOW ."你疲劳过度溺水了",C::RED ."有虚弱,眩晕,疲劳效果！","2","2","40");
            }
            if($cause === EntityDamageEvent::CAUSE_SUFFOCATION)//窒息
            {
                $player->sendTitle(C::RED. "你窒息了",C::GOLD."导致无法呼吸,直接死亡！","2","2","40");
                sleep(2);
                $player->kill();
                $this->getServer()->broadcastMessage(C::GRAY . "-=§l§dRealLife§r§7=- §a玩家§b§l{$playername}§a§r因为窒息了,导致无法呼吸,直接死亡！");
            }
            if($cause == EntityDamageEvent::CAUSE_BLOCK_EXPLOSION)//方块爆炸(tnt)
            {
                $player->sendTitle(C::RED."你被tnt炸碎得粉身碎骨",C::GOLD."直接死亡！","2","2","40");
                sleep(2);
                $player->kill();
                $this->getServer()->broadcastMessage(C::GRAY . "-=§l§dRealLife§r§7=- §a玩家§b§l". "$playername". "§a§r被tnt炸碎得粉身碎骨,直接死亡！");
            }
            if($cause == EntityDamageEvent::CAUSE_STARVATION) //饥饿
            {
                $player->addEffect($effect17); //饥饿
                $player->addEffect($effect9); //反胃
                $player->sendTitle(C::GOLD."你的血液血糖浓度太低",C::GREEN."导致饥饿,并且出现眩晕!","2","2","40");
            }
        }

    }
}