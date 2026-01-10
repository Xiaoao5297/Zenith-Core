<?php
/**
 * Created by PhpStorm.
 * User: Spiderman
 * Machine: iMac Pro
 * Date: 2017/8/27
 * Time: 15:28
 */

namespace Bionics;


use pocketmine\block\Block;
use pocketmine\block\DeadBush;
use pocketmine\block\TallGrass;
use pocketmine\entity\Effect;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBedLeaveEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\level\sound\DoorBumpSound;
use pocketmine\level\sound\FizzSound;
use pocketmine\level\sound\GhastShootSound;
use pocketmine\level\sound\PopSound;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat as C;
use pocketmine\utils\config;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\block\Cactus;
use pocketmine\block\Leaves;
use pocketmine\block\Leaves2;
use pocketmine\block\Melon;
use pocketmine\block\Ice;
use pocketmine\block\PackedIce;
use pocketmine\block\Glass;
use pocketmine\block\GlassPane;
use pocketmine\block\Wood;
use pocketmine\block\Wood2;
use pocketmine\block\Vine;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;


class Main extends PluginBase implements Listener
{
    public $mgc,$world,$worlds,$high_80,$high_100,$high_120;
    public function onEnable()
    {
        $this->getLogger()->info("仿生插件----Bionics 已加载");
        $this->getLogger()->info("Spiderman开发,请加入交流群哟~");
        @mkdir($this->getDataFolder(),0777,true);

        $this->world = new Config($this->getDataFolder()."Worlds.yml", Config::YAML, array("worlds"=>array("world")));
        $this->worlds = new Config($this->getDataFolder()."config.yml", Config::YAML, array(
            "LuCactus"=>true, //仙人掌掉落水瓶
            "WaterBottle"=>"1:10", //水瓶掉落数
            "LuWoodBlood"=>true, //手撸木头扣血
            "LuLeaves"=>true, //手撸树叶掉落
            "CutGlass"=>true, //刀撸玻璃掉落
            "CutIce"=>true, //刀撸冰块掉落
            "LuVines"=>true, //手撸藤蔓掉落
            "CutWatermelon"=>true, //刀撸西瓜掉落
            "BoreWoodFire"=>true, //钻木取火
            "FrictionProbability"=>"0:250",//火种几率多少到多少
            "Probability"=>30,//火种几率
            "AltitudeStress"=>true,//高原反应
            "Fart"=>true,//排泄
            "Fallill"=>true,//生病系统
            "Adrenaline"=>true,//肾上腺素
            "SprayBlood"=>true,//喷血
            "LuGrass"=>true,//撸草掉落种子
            "KuShuZhi"=>true,//撸枯树枝掉落木棒
            "BedLeaveBX"=>true,//睡觉补血
            "BedLeaveXT"=>true,//起床几率血糖过低
            "Tip"=>true,//提示信息
        ));
        $this->mgc = new Config($this->getDataFolder()."Message.yml", Config::YAML, array(

            "LuLeaves"=>"§6手撸树叶掉落",//√
            "CutGlass"=>"§6刀撸玻璃掉落",//√
            "CutIce"=>"§6刀撸冰块掉落",//√
            "LuVines"=>"§6手撸藤蔓掉落",//√
            "CutWatermelon"=>"§6刀撸西瓜掉落",//√
            "BoreWoodFire"=>"§6钻木取火点燃木棒",//√
            "LuGrass"=>"§a手撸草掉落小麦种子",//√
            "KuShuZhi"=>"§5手撸枯树枝掉落木棒",//√

            "LuCactus"=>[//√
                "1"=>" ",
                "2"=>"§a你在仙人掌里找到了 §e{瓶数} §a瓶水",
                "3"=>2,
                "4"=>2,
                "5"=>40
            ],
            "LuWoodBlood"=>[//√
                "1"=>" ",
                "2"=>"§7手撸木头,会伤害手哦~~",
                "3"=>2,
                "4"=>2,
                "5"=>40
            ],
            "AltitudeStress"=>[ //√
                "a1"=>"§b高原反应",
                "a2"=>"§6开始使你变得虚弱,再高一点甚至会出现眩晕状况!",
                "a3"=>2,
                "a4"=>2,
                "a5"=>40,
                "b1"=>"§b高原反应",
                "b2"=>"§6你已经出现眩晕状况,不能再往上走了!可能直接让你死亡!",
                "b3"=>2,
                "b4"=>2,
                "b5"=>40,
                "c1"=>"§b高原反应",
                "c2"=>"§6高地极度缺氧让你死亡!",
                "c3"=>2,
                "c4"=>2,
                "c5"=>40,
            ],
            "Fart"=>[//√
                "1"=>"§e§l你放了个屁",
                "2"=>"§6§l你拉了坨屎",
                "3"=>"§a§l你居然吃屎",
                "4"=>"§6玩家§b§l {玩家} §a§r§6居然吃屎",
            ],
            "Adrenaline"=>[ //√
                "Tip"=>"§a>§6  肾上腺素 §b加强速度,挖掘速度,力量",
                "1"=>"§a你打了肾上激素",
                "2"=>"§6加强速度,挖掘速度,力量",
                "3"=>2,
                "4"=>2,
                "5"=>40
            ],
            "BedLeaveBX"=>[ //√
                "1"=>" ",
                "2"=>"§c睡觉恢复血量~~",
                "3"=>2,
                "4"=>2,
                "5"=>40
            ],
            "BedLeaveXT"=>[ //√
                "1"=>"§a你刚起床导致血糖过低",
                "2"=>"§e会有短时间眩晕和虚弱效果",
                "3"=>2,
                "4"=>2,
                "5"=>40
            ],
            "Fallill Interact"=>[
                "1"=>"§6 你已换骨",
                "2"=>"§e 你已排毒",
            ],
            "Fallill ItemTip"=>[

                "苹果"=>  "§a>§3  特效药 §b适合症状:眩晕 §d治疗方法：食用 §6物品：苹果",
                "骨头"=>  "§a>§6  骨头 §b适合症状:骨折 §d治疗方法：点地 §6物品：骨头",
                "南瓜派"=>"§a>§f  南瓜派 §b适合症状:虚弱 §d治疗方法：点地 §6物品：南瓜派",
                "面包"=>  "§a>§e  士力架 §b适合症状:饥饿 §d治疗方法：食用 §6物品：面包",
                "曲奇"=>  "§a>§9  曲奇 §b适合症状:疲劳 §d治疗方法：食用 §6物品：曲奇",
                "胡萝卜"=>"§a>§b  维生素A §b适合症状:失明 §d治疗方法：食用 §6物品：胡萝卜",
                "纸"=>    "§a>§c  云南白药§b适合症状:中毒 §d治疗方法：点地  §6物品：纸",
            ],
            "Fallill Consume"=>[
                "1"=>"§a已缓解头晕！",
                "2"=>"§e横扫饥饿，做回自己！",
                "3"=>"§2已缓解疲劳！",
                "4"=>"§6强身健体",
                "5"=>"§0成功治疗失明！",
                "6"=>"§c生吃马铃薯会中毒哟！",
            ],
            "Fallill Event 被砍"=>[
                "1"=>"§b你被剑砍而感染开放性伤口",
                "2"=>"§e有流血、虚弱、损伤效果",
                "3"=>2,
                "4"=>2,
                "5"=>40
            ],
            "Fallill Event 骨折"=>[
                "1"=>"§a你腿摔断了",
                "2"=>"§6需要接骨！",
                "3"=>2,
                "4"=>2,
                "5"=>40
            ],
            "Fallill Event 燃烧"=>[
                "广播"=>"§a玩家§b§l {名字} §a§r浴火纵身,直接死亡",
                "1"=>"§c你浴火纵身",
                "2"=>"§6直接死亡",
                "3"=>2,
                "4"=>2,
                "5"=>40
            ],
            "Fallill Event 溺水"=>[
                "1"=>"§e你疲劳过度溺水了",
                "2"=>"§c有虚弱,眩晕,疲劳效果！",
                "3"=>2,
                "4"=>2,
                "5"=>40
            ],
            "Fallill Event 窒息"=>[
                "广播"=>"§a玩家§b§l {名字} §a§r因为窒息了,导致无法呼吸,直接死亡！",
                "1"=>"§c你窒息了",
                "2"=>"§6导致无法呼吸,直接死亡！",
                "3"=>2,
                "4"=>2,
                "5"=>40
            ],
            "Fallill Event 被炸"=>[
                "广播"=>"§a玩家§b§l {名字} §a§r被tnt炸碎得粉身碎骨,直接死亡！",
                "1"=>"§c你被tnt炸碎得粉身碎骨",
                "2"=>"§6直接死亡！",
                "3"=>2,
                "4"=>2,
                "5"=>40
            ],
            "Fallill Event 饥饿"=>[
                "1"=>"§6你的血液血糖浓度太低",
                "2"=>"§a导致饥饿,并且出现眩晕!",
                "3"=>2,
                "4"=>2,
                "5"=>40
            ]
        ));
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    public function onBed(PlayerBedLeaveEvent $event)
    {
        $player = $event->getPlayer();
        $num = mt_rand(0,150);
        $effect9 = Effect::getEffect(Effect::NAUSEA)->setVisible(true)->setAmplifier(0)->setDuration(20*20); //反胃
        $effect18 = Effect::getEffect(Effect::WEAKNESS)->setVisible(true)->setAmplifier(0)->setDuration(20*20);//变弱

        $x1=$this->mgc->get("BedLeaveXT")["1"];
        $x2=$this->mgc->get("BedLeaveXT")["2"];
        $x3=$this->mgc->get("BedLeaveXT")["3"];
        $x4=$this->mgc->get("BedLeaveXT")["4"];
        $x5=$this->mgc->get("BedLeaveXT")["5"];

        $b1=$this->mgc->get("BedLeaveBX")["1"];
        $b2=$this->mgc->get("BedLeaveBX")["2"];
        $b3=$this->mgc->get("BedLeaveBX")["3"];
        $b4=$this->mgc->get("BedLeaveBX")["4"];
        $b5=$this->mgc->get("BedLeaveBX")["5"];

        if(in_array($player->getLevel()->getFolderName(),$this->world->get("worlds")))
        {
            if($this->worlds->get("BedLeaveBX"))
            {
                $level = $player->getLevel();
                $level->addSound(new FizzSound($player));
                $player->setHealth(20);
                $player->sendTitle($b1,$b2,$b3,$b4,$b5);
            }
            if($this->worlds->get("BedLeaveXT"))
            {
                if($num < 30)
                {
                    $player->sendTitle($x1,$x2,$x3,$x4,$x5);
                    $player->addEffect($effect9);
                    $player->addEffect($effect18);
                    unset($num);
                }
            }
        }
    }
    public function onSneak(PlayerToggleSneakEvent $event)
    {
        $player = $event->getPlayer();
        $inventory = $player->getInventory();
        if(in_array($player->getLevel()->getFolderName(),$this->world->get("worlds")))
        {
            if($this->worlds->get("Fart"))
            {
                $fp=$this->mgc->get("Fart")["1"];
                $ls=$this->mgc->get("Fart")["2"];
                if($player->isSneaking())
                {
                    $player->sendPopup($fp);
                    $num = mt_rand(0,250);
                    if($num < 20)
                    {
                        $inventory->addItem(new Item(351,3,1));
                        $player->sendPopup($ls);
                        unset($num);
                    }
                }
            }
        }
    }
    public function help($s)
    {
        $s->sendMessage(C::GRAY .      "========= -=§l§dBionics§r§7=- ================");
        $s->sendMessage(C::AQUA .      "/rlset help              §a查看帮助");
        $s->sendMessage(C::GOLD .      "/rlset reload            §b重载配置文件");
        $s->sendMessage(C::YELLOW .    "/rlset true [type]       §c开启某个功能");
        $s->sendMessage(C::WHITE .     "/rlset false [type]      §d关闭某个功能");
        $s->sendMessage(C::GREEN .     "/rlset type              §e查看type");
    }
    public function ill($s)
    {
        $s->sendMessage(C::GRAY .      "======-=§l§dBionics§r§7=-====================================");
        $s->sendMessage(C::DARK_AQUA . "> 特效药   §b适合症状:§a眩晕  §d治疗方法:§e食用 §6物品:§f苹果 ");
        $s->sendMessage(C::GOLD .      "> 骨头     §b适合症状:§a骨折  §d治疗方法:§e点地 §6物品:§f骨头");
        $s->sendMessage(C::WHITE .     "> 南瓜派   §b适合症状:§a虚弱  §d治疗方法:§e食用 §6物品:§f南瓜派");
        $s->sendMessage(C::YELLOW .    "> 士力架   §b适合症状:§a饥饿  §d治疗方法:§e食用 §6物品:§f面包");
        $s->sendMessage(C::GREEN .     "> 曲奇     §b适合症状:§a疲劳  §d治疗方法:§e食用 §6物品:§f曲奇");
        $s->sendMessage(C::AQUA .      "> 维生素A  §b适合症状:§a失明  §d治疗方法:§e食用 §6物品:§f胡萝卜");
        $s->sendMessage(C::RED .       "> 云南白药 §b适合症状:§a中毒  §d治疗方法:§e点地 §6物品:§f纸");
    }
    public function type($s)
    {
        $s->sendMessage(C::GREEN . "type: [lxrz]撸仙人掌掉落水瓶、[lmt]手撸木头扣血、[lsy]手撸树叶掉落、[lbl]刀撸玻璃掉落、[lbk]刀撸冰块掉落");
        $s->sendMessage(C::GREEN . "      [ltw]手撸藤蔓掉落、[lxg]刀撸西瓜掉落、[zmqh]钻木取火、[gyfy]高原反应、[sbxt]生病系统、[ssss]肾上腺素");
        $s->sendMessage(C::GREEN . "      [px]打实体爆血");
    }
    public function onCommand(CommandSender $s, Command $command, $label, array $args)
    {
        switch($command->getName())
        {
            case "ill":
                $this->ill($s);
                return true;
            case "rlset":
                if(count($args) === 0)
                {
                    $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §a请输入§c /rlset help §a查看帮助");
                    return true;
                }
                if(isset($args[0]))
                {
                    if($args[0] == "help")
                    {
                        if($s->isOp())
                        {
                            if(!isset($args[1]))
                            {
                                $this->help($s);
                                return true;
                            }
                            if($args[1]=="type")
                            {
                                $this->type($s);
                                return true;
                            }
                        }
                        else
                        {
                            $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c您无权使用本指令!");
                            return true;
                        }
                    }
                    if($args[0] == "reload")
                    {
                        if($s->isOp())
                        {
                            $this->worlds->reload();
                            $this->world->reload();
                            $this->mgc->reload();
                            $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=-  §f配置重载完成");
                            return true;
                        }
                        else
                        {
                            $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c您无权使用本指令!");
                            return true;
                        }
                    }
                    if($args[0]=="true")
                    {
                        if($s->isOp())
                        {
                            if(!isset($args[1]))
                            {
                                $this->type($s);
                                return true;
                            }
                            if($args[1]=="px")
                            {
                                $this->worlds->set("SprayBlood",true);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c开启 打实体爆血");
                                return true;
                            }
                            if($args[1]=="qcbx")
                            {
                                $this->worlds->set("BedLeaveBX",true);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c开启 睡觉补血");
                                return true;
                            }
                            if($args[1]=="qcxt")
                            {
                                $this->worlds->set("BedLeaveXT",true);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c开启 起床几率血糖过低导致眩晕虚弱");
                                return true;
                            }
                            if($args[1]=="lcdl")
                            {
                                $this->worlds->set("LuGrass",true);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c开启 撸草掉落种子");
                                return true;
                            }
                            if($args[1]=="lksz")
                            {
                                $this->worlds->set("KuShuZhi",true);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c开启 撸枯树枝掉落木棒");
                                return true;
                            }
                            if($args[1]=="lxrz")
                            {
                                $this->worlds->set("LuCactus",true);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c开启 撸仙人掌掉落水瓶");
                                return true;
                            }
                            if($args[1]=="lmt")
                            {
                                $this->worlds->set("LuWoodBlood",true);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c开启 手撸木头扣血");
                                return true;
                            }
                            if($args[1]=="lsy")
                            {
                                $this->worlds->set("LuLeaves",true);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c开启 手撸树叶掉落");
                                return true;
                            }
                            if($args[1]=="lbl")
                            {
                                $this->worlds->set("CutGlass",true);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c开启 刀撸玻璃掉落");
                                return true;
                            }
                            if($args[1]=="lbk")
                            {
                                $this->worlds->set("CutIce",true);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c开启 刀撸冰块掉落");
                                return true;
                            }
                            if($args[1]=="ltw")
                            {
                                $this->worlds->set("LuVines",true);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c开启 手撸藤蔓掉落");
                                return true;
                            }
                            if($args[1]=="lxg")
                            {
                                $this->worlds->set("CutWatermelon",true);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c开启 刀撸西瓜掉落");
                                return true;
                            }
                            if($args[1]=="zmqh")
                            {
                                $this->worlds->set("BoreWoodFire",true);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c开启 钻木取火");
                                return true;
                            }
                            if($args[1]=="gyfy")
                            {
                                $this->worlds->set("AltitudeStress",true);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c开启 高原反应");
                                return true;
                            }
                            if($args[1]=="sbxt")
                            {
                                $this->worlds->set("Fallill",true);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c开启 生病系统");
                                return true;
                            }
                            if($args[1]=="ssss")
                            {
                                $this->worlds->set("Adrenaline",true);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c开启 肾上腺素");
                                return true;
                            }
                            if($args[1]!="lxrz" AND $args[1]!="lmt" AND $args[1]!="lsy" AND $args[1]!="lbl"
                                AND $args[1]!="lbk" AND $args[1]!="ltw" AND $args[1]!="lxg" AND $args[1]!="zmqh"
                                AND $args[1]!="gyfy" AND $args[1]!="sbxt" AND $args[1]!="ssss" AND $args[1]!="lcdl"
                                AND $args[1]!="lksz" AND $args[1]!="qcbx" AND $args[1]!="qcxt")
                            {
                                $this->type($s);
                                return true;
                            }
                        }
                        else
                        {
                            $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c您无权使用本指令!");
                            return true;
                        }
                    }
                    if($args[0]=="false")
                    {
                        if($s->isOp())
                        {
                            if(!isset($args[1]))
                            {
                                $this->type($s);
                                return true;
                            }
                            if($args[1]=="px")
                            {
                                $this->worlds->set("SprayBlood",false);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c关闭 打实体爆血");
                                return true;
                            }
                            if($args[1]=="qcbx")
                            {
                                $this->worlds->set("BedLeaveBX",false);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c关闭 睡觉补血");
                                return true;
                            }
                            if($args[1]=="qcxt")
                            {
                                $this->worlds->set("BedLeaveXT",false);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c关闭 起床几率血糖过低导致眩晕虚弱");
                                return true;
                            }
                            if($args[1]=="lcdl")
                            {
                                $this->worlds->set("LuGrass",false);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c关闭 撸草掉落种子");
                                return true;
                            }
                            if($args[1]=="lksz")
                            {
                                $this->worlds->set("KuShuZhi",false);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c关闭 撸枯树枝掉落木棒");
                                return true;
                            }
                            if($args[1]=="lxrz")
                            {
                                $this->worlds->set("LuCactus",false);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c关闭 撸仙人掌掉落水瓶");
                                return true;
                            }
                            if($args[1]=="lmt")
                            {
                                $this->worlds->set("LuWoodBlood",false);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c关闭 手撸木头扣血");
                                return true;
                            }
                            if($args[1]=="lsy")
                            {
                                $this->worlds->set("LuLeaves",false);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c关闭 手撸树叶掉落");
                                return true;
                            }
                            if($args[1]=="lbl")
                            {
                                $this->worlds->set("CutGlass",false);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c关闭 刀撸玻璃掉落");
                                return true;
                            }
                            if($args[1]=="lbk")
                            {
                                $this->worlds->set("CutIce",false);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c关闭 刀撸冰块掉落");
                                return true;
                            }
                            if($args[1]=="ltw")
                            {
                                $this->worlds->set("LuVines",false);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c关闭 手撸藤蔓掉落");
                                return true;
                            }
                            if($args[1]=="lxg")
                            {
                                $this->worlds->set("CutWatermelon",false);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c关闭 刀撸西瓜掉落");
                                return true;
                            }
                            if($args[1]=="zmqh")
                            {
                                $this->worlds->set("BoreWoodFire",false);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c关闭 钻木取火");
                                return true;
                            }
                            if($args[1]=="gyfy")
                            {
                                $this->worlds->set("AltitudeStress",false);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c关闭 高原反应");
                                return true;
                            }
                            if($args[1]=="sbxt")
                            {
                                $this->worlds->set("Fallill",false);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c关闭 生病系统");
                                return true;
                            }
                            if($args[1]=="ssss")
                            {
                                $this->worlds->set("Adrenaline",false);
                                $this->worlds->save();
                                $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c关闭 肾上腺素");
                                return true;
                            }
                            if($args[1]!="lxrz" AND $args[1]!="lmt" AND $args[1]!="lsy" AND $args[1]!="lbl"
                                AND $args[1]!="lbk" AND $args[1]!="ltw" AND $args[1]!="lxg" AND $args[1]!="zmqh"
                                AND $args[1]!="gyfy" AND $args[1]!="sbxt" AND $args[1]!="ssss" AND $args[1]!="lcdl"
                                AND $args[1]!="lksz" AND $args[1]!="qcbx" AND $args[1]!="qcxt")
                            {
                                $this->type($s);
                                return true;
                            }
                        }
                        else
                        {
                            $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c您无权使用本指令!");
                            return true;
                        }
                    }
                    if($args[0]!="help" AND $args[0]!="true" AND $args[0]!="reload" AND $args[0]!="false")
                    {
                        $s->sendMessage(C::GRAY .      "-=§l§dBionics§r§7=- §c错误！§a请输入§c /rlset help §a查看帮助");
                        return true;
                    }
                }
        }
    }
    public function onPlayerInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $inventory = $player->getInventory();
        $playername = $player->getName();
        $level = $player->getLevel();

        $Tip=$this->worlds->get("Tip");

        $x=$this->mgc->get("BoreWoodFire");

        $x2=$this->mgc->get("Fart")["3"];
        $x2_41=$this->mgc->get("Fart")["4"];

        $x3=$this->mgc->get("Adrenaline")["1"];
        $x3_2=$this->mgc->get("Adrenaline")["2"];
        $x3_3=$this->mgc->get("Adrenaline")["3"];
        $x3_4=$this->mgc->get("Adrenaline")["4"];
        $x3_5=$this->mgc->get("Adrenaline")["5"];

        $a1=$this->mgc->get("Fallill Interact")["1"];
        $a2=$this->mgc->get("Fallill Interact")["2"];

        if(in_array($player->getLevel()->getFolderName(),$this->world->get("worlds")))
        {
            switch($event->getItem()->getId())
            {
                case 351:
                    if($event->getItem()->getDamage() == 3)
                    {
                        if($this->worlds->get("Fart"))
                        {
                            $player->getInventory()->removeItem(new Item(351,3,1));
                            $player->addEffect(Effect::getEffect(9)->setDuration(20*60)->setAmplifier(1)->setVisible(true));
                            $player->sendMessage($x2);
                            $x2_4=str_replace("{玩家}",$playername,$x2_41);
                            $this->getServer()->broadcastMessage($x2_4);
                        }
                    }
                    break;
                case 437:
                    if($this->worlds->get("Adrenaline"))
                    {
                        $level->addSound(new GhastShootSound($player));
                        $player->sendTitle($x3,$x3_2,$x3_3,$x3_4,$x3_5);
                        $player->addEffect(Effect::getEffect(Effect::SPEED)->setVisible(true)->setAmplifier(3)->setDuration(20*60*2));
                        $player->addEffect(Effect::getEffect(Effect::HASTE)->setVisible(true)->setAmplifier(3)->setDuration(20*60*2));
                        $player->addEffect(Effect::getEffect(Effect::STRENGTH)->setVisible(true)->setAmplifier(3)->setDuration(20*60*2));
                        $player->getInventory()->removeItem(new Item(437, 0, 1));
                    }
                    break;
                case 280:
                    if(($event->getBlock() instanceof Wood) OR ($event->getBlock() instanceof Wood2))
                    {
                        if($this->worlds->get("BoreWoodFire"))
                        {
                            $num1 = explode(":",$this->worlds->get("FrictionProbability"));
                            $num = mt_rand($num1[0],$num1[1]);
                            if($num <= $this->worlds->get("Probability"))
                            {
                                $level->addSound(new PopSound($player));
                                $inventory->removeItem(new Item(280, 0, 1));
                                $inventory->addItem(new Item(50,0,1));
                                if($Tip)
                                {
                                    $player->sendMessage($x);
                                }
                                unset($num);
                            }
                        }
                    }
                    break;
                case 352:
                    if($player->hasEffect(Effect::SLOWNESS))
                    {
                        if($this->worlds->get("Fallill"))
                        {
                            $level->addSound(new AnvilFallSound($player));
                            $player->removeEffect(Effect::SLOWNESS);
                            $inventory->removeItem(new Item(352, 0, 1));
                            $player->sendMessage(C::GRAY . "-=§l§dBionics§r§7=- $a1");
                        }
                    }
                    break;
                case 339:
                    if($player->hasEffect(Effect::POISON))
                    {
                        if($this->worlds->get("Fallill"))
                        {
                            $level->addSound(new AnvilFallSound($player));
                            $player->removeEffect(Effect::POISON);
                            $inventory->removeItem(new Item(339, 0, 1));
                            $player->sendMessage(C::GRAY . "-=§l§dBionics§r§7=- $a2");
                        }
                    }
                    break;
            }
        }
    }
    public function OnBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand()->getId();
        $block = $event->getBlock();
        $health = $player->getHealth();

        $Tip=$this->worlds->get("Tip");
        $b=$this->mgc->get("LuLeaves");
        $b1=$this->mgc->get("CutGlass");
        $b2=$this->mgc->get("CutIce");
        $b3=$this->mgc->get("LuVines");
        $b4=$this->mgc->get("CutWatermelon");

        $b6=$this->mgc->get("LuCactus")["1"];
        $b6_21=$this->mgc->get("LuCactus")["2"];
        $b6_3=$this->mgc->get("LuCactus")["3"];
        $b6_4=$this->mgc->get("LuCactus")["4"];
        $b6_5=$this->mgc->get("LuCactus")["5"];

        $b7=$this->mgc->get("LuWoodBlood")["1"];
        $b7_2=$this->mgc->get("LuWoodBlood")["2"];
        $b7_3=$this->mgc->get("LuWoodBlood")["3"];
        $b7_4=$this->mgc->get("LuWoodBlood")["4"];
        $b7_5=$this->mgc->get("LuWoodBlood")["5"];

        $b8=$this->mgc->get("LuGrass");
        $b9=$this->mgc->get("KuShuZhi");
        if(in_array($level = $player->getLevel()->getFolderName(),$this->world->get("worlds")))
        {
            if($block instanceof Wood)
            {
                if($this->worlds->get("LuWoodBlood"))
                {
                    if($item == "0")
                    {
                        $player->setHealth($health-1);
                        $player->sendTitle($b7,$b7_2,$b7_3,$b7_4,$b7_5);
                    }
                }
            }
            if($block instanceof TallGrass)
            {
                if($this->worlds->get("LuGrass"))
                {
                    $event->setDrops(array(Item::get(295,0,1)));
                    if($Tip)
                    {
                        $player->sendMessage($b8);
                    }
                }
            }
            if($block instanceof DeadBush)
            {
                if($this->worlds->get("KuShuZhi"))
                {
                    $event->setDrops(array(Item::get(280,0,1)));
                    if($Tip)
                    {
                        $player->sendMessage($b9);
                    }
                }
            }
            if($block instanceof Vine )
            {
                if($this->worlds->get("LuLeaves"))
                {
                    if($item == "0")
                    {
                        $event->setDrops(array(Item::get(106,0,1)));
                        if($Tip)
                        {
                            $player->sendMessage($b3);
                        }
                    }
                }
            }
            if( ($block instanceof Leaves) OR ($block instanceof Leaves2) )
            {
                if($this->worlds->get("LuLeaves"))
                {
                    if($item == "0")
                    {
                        $blockid = $block->getId();
                        $blockide = $block->getDamage();
                        $event->setDrops(array(Item::get($blockid,$blockide,1)));
                        if($Tip)
                        {
                            $player->sendMessage($b);
                        }
                    }
                }
            }
            if($block instanceof Cactus)
            {
                if($this->worlds->get("LuCactus"))
                {
                    $num1 = explode(":",$this->worlds->get("WaterBottle"));
                    $num = mt_rand($num1[0],$num1[1]);
                    $event->setDrops(array(Item::get(373,0,$num)));
                    $b6_2=str_replace("{瓶数}",$num,$b6_21);
                    $player->sendTitle($b6,$b6_2,$b6_3,$b6_4,$b6_5);
                    unset($num);
                }
            }
            if($block instanceof Melon)
            {
                if($this->worlds->get("CutWatermelon"))
                {
                    if($item == "267" || $item == "272" || $item == "283" || $item == "276" || $item == "268" || $item == "359" )
                    {
                        $event->setDrops(array(Item::get(103,0,1)));
                        if($Tip)
                        {
                            $player->sendMessage($b4);
                        }
                    }
                }
            }
            if($block instanceof Ice)
            {
                if($this->worlds->get("CutIce"))
                {
                    if($item == "267" || $item == "272" || $item == "283" || $item == "276" || $item == "268" )
                    {
                        $event->setDrops(array(Item::get(79,0,1)));
                        if($Tip)
                        {
                            $player->sendMessage($b2);
                        }
                    }
                }
            }
            if($block instanceof PackedIce)
            {
                if($this->worlds->get("CutIce"))
                {
                    if($item == "267" || $item == "272" || $item == "283" || $item == "276" || $item == "268" )
                    {
                        $event->setDrops(array(Item::get(174,0,1)));
                        if($Tip)
                        {
                            $player->sendMessage($b2);
                        }
                    }
                }
            }
            if($block instanceof Glass)
            {
                if($this->worlds->get("CutGlass"))
                {
                    if($item == "267" || $item == "272" || $item == "283" || $item == "276" || $item == "268" || $item == "359" )
                    {
                        $event->setDrops(array(Item::get(20,0,1)));
                        if($Tip)
                        {
                            $player->sendMessage($b1);
                        }
                    }
                }
            }
            if($block instanceof GlassPane)
            {
                if($this->worlds->get("CutGlass"))
                {
                    if($item == "267" || $item == "272" || $item == "283" || $item == "276" || $item == "268" || $item == "359" )
                    {
                        $event->setDrops(array(Item::get(102,0,1)));
                        if($Tip)
                        {
                            $player->sendMessage($b1);
                        }
                    }
                }
            }
        }
    }
    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        $y = $player->getY();

        $a1=$this->mgc->get("AltitudeStress")["a1"];
        $a2=$this->mgc->get("AltitudeStress")["a2"];
        $a3=$this->mgc->get("AltitudeStress")["a3"];
        $a4=$this->mgc->get("AltitudeStress")["a4"];
        $a5=$this->mgc->get("AltitudeStress")["a5"];
        $b1=$this->mgc->get("AltitudeStress")["b1"];
        $b2=$this->mgc->get("AltitudeStress")["b2"];
        $b3=$this->mgc->get("AltitudeStress")["b3"];
        $b4=$this->mgc->get("AltitudeStress")["b4"];
        $b5=$this->mgc->get("AltitudeStress")["b5"];
        $c1=$this->mgc->get("AltitudeStress")["c1"];
        $c2=$this->mgc->get("AltitudeStress")["c2"];
        $c3=$this->mgc->get("AltitudeStress")["c3"];
        $c4=$this->mgc->get("AltitudeStress")["c4"];
        $c5=$this->mgc->get("AltitudeStress")["c5"];

        $effect9 = Effect::getEffect(Effect::NAUSEA)->setVisible(false)->setAmplifier(0)->setDuration(20*120); //反胃
        $effect18 = Effect::getEffect(Effect::WEAKNESS)->setVisible(true)->setAmplifier(0)->setDuration(20*120);//变弱
        $level = $player->getLevel()->getFolderName();
        if(in_array($level,$this->world->get("worlds")))
        {
            if($this->worlds->get("AltitudeStress"))
            {
                if ($y >= 80 AND $y <= 100)
                {
                    $this->high_80++;
                    if($this->high_80 == 5)
                    {
                        $player->addEffect($effect18);
                        $player->sendTitle($a1,$a2,$a3,$a4,$a5);
                    }
                }
                if ($y >= 100)
                {
                    $this->high_100++;
                    if($this->high_100 == 5)
                    {
                        $player->addEffect($effect9);
                        $player->sendTitle($b1,$b2,$b3,$b4,$b5);
                    }
                }
                if ($y >= 120)
                {
                    $this->high_120++;
                    if($this->high_120 == 5)
                    {
                        $player->sendTitle($c1,$c2,$c3,$c4,$c5);
                        sleep(2);
                        $player->kill();
                    }
                }
                if ($y <= 80)
                {
                    $this->high_80 = 0;
                    $this->high_100 = 0;
                    $this->high_120 = 0;
                }
            }
        }
    }
    public function onHeld(PlayerItemHeldEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $itemid = $item->getId();

        $a1=$this->mgc->get("Fallill ItemTip")["苹果"];
        $a2=$this->mgc->get("Fallill ItemTip")["骨头"];
        $a3=$this->mgc->get("Fallill ItemTip")["南瓜派"];
        $a4=$this->mgc->get("Fallill ItemTip")["面包"];
        $a5=$this->mgc->get("Fallill ItemTip")["曲奇"];
        $a6=$this->mgc->get("Fallill ItemTip")["胡萝卜"];
        $a7=$this->mgc->get("Fallill ItemTip")["纸"];
        $a8=$this->mgc->get("Adrenaline")["Tip"];

        if(in_array($player->getLevel()->getFolderName(),$this->world->get("worlds")))
        {
            if($this->worlds->get("Fallill"))
            {
                switch($itemid)
                {
                    case 260://苹果
                        $player->sendMessage($a1);
                        break;
                    case 352://骨头
                        $player->sendMessage($a2);
                        break;
                    case 400: //南瓜派
                        $player->sendMessage($a3);
                        break;
                    case 297: //面包
                        $player->sendMessage($a4);
                        break;
                    case 357://曲奇
                        $player->sendMessage($a5);
                        break;
                    case 391://胡萝卜
                        $player->sendMessage($a6);
                        break;
                    case 339://纸
                        $player->sendMessage($a7);
                        break;
                    case 437://龙息
                        $player->sendMessage($a8);
                        break;
                }
            }
        }
    }
    public function onPlayerEat(PlayerItemConsumeEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $itemid = $item->getId();

        $a1=$this->mgc->get("Fallill Consume")["1"];
        $a2=$this->mgc->get("Fallill Consume")["2"];
        $a3=$this->mgc->get("Fallill Consume")["3"];
        $a4=$this->mgc->get("Fallill Consume")["4"];
        $a5=$this->mgc->get("Fallill Consume")["5"];
        $a6=$this->mgc->get("Fallill Consume")["6"];

        $effect19 = Effect::getEffect(Effect::POISON)->setVisible(true)->setAmplifier(0)->setDuration(20*120);//中毒
        if(in_array($player->getLevel()->getFolderName(),$this->world->get("worlds")))
        {
            if($this->worlds->get("Fallill"))
            {
                switch($itemid)
                {
                    case 260: //苹果---特效药
                        if($player->hasEffect(Effect::NAUSEA))
                        {
                            $player->removeEffect(Effect::NAUSEA);
                            $player->sendMessage(C::GRAY . "-=§l§dBionics§r§7=- $a1");
                        }
                        break;
                    case 297://面包---士力架
                        if($player->hasEffect(Effect::HUNGER))
                        {
                            $player->removeEffect(Effect::HUNGER);
                            $player->sendMessage(C::GRAY . "-=§l§dBionics§r§7=- $a2");
                        }
                        break;
                    case 357://曲奇
                        if($player->hasEffect(Effect::FATIGUE))
                        {
                            $player->removeEffect(Effect::FATIGUE);
                            $player->sendMessage(C::GRAY . "-=§l§dBionics§r§7=- $a3");
                        }
                        break;
                    case 400://南瓜派
                        if($player->hasEffect(Effect::WEAKNESS))
                        {
                            $player->removeEffect(Effect::WEAKNESS);
                            $player->sendMessage(C::GRAY . "-=§l§dBionics§r§7=- $a4");
                        }
                        break;
                    case 391://胡萝卜---维生素A
                        if($player->hasEffect(Effect::BLINDNESS))
                        {
                            $player->removeEffect(Effect::BLINDNESS);
                            $player->sendMessage(C::GRAY . "-=§l§dBionics§r§7=- $a5");
                        }
                        break;
                    case 392://马铃薯
                        $player->addEffect($effect19);
                        $player->sendMessage(C::GRAY . "-=§l§dBionics§r§7=- $a6");
                        break;
                }
            }
        }
    }
    public function onDamage(EntityDamageEvent $event)
    {
        $player = $event->getEntity();
        $playername = $event->getEntity()->getName();
        $cause = $event->getCause();
        $health = $player->getHealth();

        $a1=$this->mgc->get("Fallill Event 被砍")["1"];
        $a2=$this->mgc->get("Fallill Event 被砍")["2"];
        $a3=$this->mgc->get("Fallill Event 被砍")["3"];
        $a4=$this->mgc->get("Fallill Event 被砍")["4"];
        $a5=$this->mgc->get("Fallill Event 被砍")["5"];

        $b1=$this->mgc->get("Fallill Event 骨折")["1"];
        $b2=$this->mgc->get("Fallill Event 骨折")["2"];
        $b3=$this->mgc->get("Fallill Event 骨折")["3"];
        $b4=$this->mgc->get("Fallill Event 骨折")["4"];
        $b5=$this->mgc->get("Fallill Event 骨折")["5"];

        $c1=$this->mgc->get("Fallill Event 燃烧")["1"];
        $c2=$this->mgc->get("Fallill Event 燃烧")["2"];
        $c3=$this->mgc->get("Fallill Event 燃烧")["3"];
        $c4=$this->mgc->get("Fallill Event 燃烧")["4"];
        $c5=$this->mgc->get("Fallill Event 燃烧")["5"];
        $c6=$this->mgc->get("Fallill Event 燃烧")["广播"];

        $d1=$this->mgc->get("Fallill Event 溺水")["1"];
        $d2=$this->mgc->get("Fallill Event 溺水")["2"];
        $d3=$this->mgc->get("Fallill Event 溺水")["3"];
        $d4=$this->mgc->get("Fallill Event 溺水")["4"];
        $d5=$this->mgc->get("Fallill Event 溺水")["5"];

        $e1=$this->mgc->get("Fallill Event 窒息")["1"];
        $e2=$this->mgc->get("Fallill Event 窒息")["2"];
        $e3=$this->mgc->get("Fallill Event 窒息")["3"];
        $e4=$this->mgc->get("Fallill Event 窒息")["4"];
        $e5=$this->mgc->get("Fallill Event 窒息")["5"];
        $e6=$this->mgc->get("Fallill Event 窒息")["广播"];

        $f1=$this->mgc->get("Fallill Event 被炸")["1"];
        $f2=$this->mgc->get("Fallill Event 被炸")["2"];
        $f3=$this->mgc->get("Fallill Event 被炸")["3"];
        $f4=$this->mgc->get("Fallill Event 被炸")["4"];
        $f5=$this->mgc->get("Fallill Event 被炸")["5"];
        $f6=$this->mgc->get("Fallill Event 被炸")["广播"];

        $g1=$this->mgc->get("Fallill Event 饥饿")["1"];
        $g2=$this->mgc->get("Fallill Event 饥饿")["2"];
        $g3=$this->mgc->get("Fallill Event 饥饿")["3"];
        $g4=$this->mgc->get("Fallill Event 饥饿")["4"];
        $g5=$this->mgc->get("Fallill Event 饥饿")["5"];

        $effect = Effect::getEffect(Effect::SLOWNESS)->setVisible(true)->setAmplifier(1)->setDuration(20*120);//缓慢
        $effect4 = Effect::getEffect(Effect::FATIGUE)->setVisible(true)->setAmplifier(0)->setDuration(20*120);//疲劳
        $effect9 = Effect::getEffect(Effect::NAUSEA)->setVisible(false)->setAmplifier(0)->setDuration(20*120); //反胃
        $effect17 = Effect::getEffect(Effect::HUNGER)->setVisible(true)->setAmplifier(0)->setDuration(20*120);//饥饿
        $effect18 = Effect::getEffect(Effect::WEAKNESS)->setVisible(true)->setAmplifier(0)->setDuration(20*120);//变弱

        if(in_array($player->getLevel()->getFolderName(),$this->world->get("worlds")))
        {
            if($this->worlds->get("SprayBlood"))
            {
                if($event instanceof EntityDamageByEntityEvent)
                {
                    $damager = $event->getDamager();
                    if($damager instanceof Player)
                    {
                        $player->getLevel()->addSound(new DoorBumpSound($player));
                        $player->getLevel()->addParticle(new DestroyBlockParticle(new Vector3($player->getX(), $player->getY(), $player->getZ()), Block::get(152)));
                        $damager->sendMessage("Boom!!!");
                    }
                }
            }
            if($this->worlds->get("Fallill"))
            {
                if($event instanceof EntityDamageByEntityEvent)
                {
                    $entity = $event->getEntity();
                    $damager = $event->getDamager();
                    $item = $event->getDamager()->getInventory()->getItemInHand()->getId();
                    if($damager instanceof Player && $entity instanceof Player)
                    {
                        if($item === 272  OR $item ===283  OR $item ===276  OR $item ===267  OR $item === 268)
                        {
                            $event->setCancelled(true);
                            $player->sendTitle($a1,$a2,$a3,$a4,$a5);
                            $player->addEffect($effect18);
                            $player->setHealth($health-5);
                        }
                    }
                }
                if($cause == EntityDamageEvent::CAUSE_FALL)//摔
                {
                    $player->addEffect($effect);//缓慢
                    $player->getLevel()->addParticle(new DestroyBlockParticle(new Vector3($player->getX(), $player->getY(), $player->getZ()), Block::get(155)));
                    $player->sendTitle($b1,$b2,$b3,$b4,$b5);
                }
                if($cause == EntityDamageEvent::CAUSE_LAVA OR $cause == EntityDamageEvent::CAUSE_FIRE)//熔岩、烧
                {
                    $c7=str_replace("{名字}",$playername,$c6);
                    $player->sendTitle($c1,$c2,$c3,$c4,$c5);
                    sleep(2);
                    $player->kill();
                    $this->getServer()->broadcastMessage(C::GRAY . "-=§l§dBionics§r§7=- $c7");
                }
                if($cause == EntityDamageEvent::CAUSE_DROWNING)//溺水
                {
                    $player->addEffect($effect4); //疲劳
                    $player->addEffect($effect18); //变弱
                    $player->addEffect($effect9); //反胃
                    $player->sendTitle($d1,$d2,$d3,$d4,$d5);
                }
                if($cause === EntityDamageEvent::CAUSE_SUFFOCATION)//窒息
                {
                    $e7=str_replace("{名字}",$playername,$e6);
                    $player->sendTitle($e1,$e2,$e3,$e4,$e5);
                    sleep(2);
                    $player->kill();
                    $this->getServer()->broadcastMessage(C::GRAY . "-=§l§dBionics§r§7=- $e7");
                }
                if($cause == EntityDamageEvent::CAUSE_BLOCK_EXPLOSION)//方块爆炸(tnt)
                {
                    $f7=str_replace("{名字}",$playername,$f6);
                    $player->sendTitle($f1,$f2,$f3,$f4,$f5);
                    sleep(2);
                    $player->kill();
                    $this->getServer()->broadcastMessage(C::GRAY . "-=§l§dBionics§r§7=- $f7");
                }
                if($cause == EntityDamageEvent::CAUSE_STARVATION) //饥饿
                {
                    $player->addEffect($effect17); //饥饿
                    $player->addEffect($effect9); //反胃
                    $player->sendTitle($g1,$g2,$g3,$g4,$g5);
                }
            }
        }
    }
}