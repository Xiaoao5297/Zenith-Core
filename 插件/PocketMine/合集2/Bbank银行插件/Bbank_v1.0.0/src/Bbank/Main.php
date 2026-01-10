<?php
namespace Bbank;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TM;
use pocketmine\plugin\Plugin;
use onebone\economyapi\EconomyAPI;
use pocketmine\scheduler\CallbackTask;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;

class Main extends PluginBase implements Listener
{

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TM::GREEN . "[银行插件]作者BRD");
        $this->getLogger()->info(TM::GREEN . "不要歧视无知的新人,有一天,他可能会达到你只能仰慕的境界");
        @mkdir($this->getDataFolder(), 0777, true);
        $this->jiedai = new Config($this->getDataFolder() . "贷款数据.yml", Config::YAML, array());
        $this->cunhuan = new Config($this->getDataFolder() . "存款数据.yml", Config::YAML, array());
        $this->banname = new Config($this->getDataFolder() . "封杀名单.yml", Config::YAML, array(
            "提示" => "您付不起高利贷的利息，而被封杀！",
            "被封玩家" => array(),
            "贷款玩家" => array()
        ));
        $this->config = new Config($this->getDataFolder() . " config.yml", Config::YAML, array(
            "存款利率" => 1,
            "贷款利率" => 10,
            "最大贷款数" => 10000,
            "贷款手续费(false or 数字)" => 2,
            "利息添加时间" => "00:00:00",
            "是否开启存款有利息" => true,
            "进服提示" => true,
            "不还钱被BAN"=> true
        ));
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new CallbackTask([$this, "timer"]), 20);
    }

    public function onDisable()
    {
        $this->getLogger()->info(TM::RED . "营业终");
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args)
    {
        $name = $sender->getName();
        $name1 = strtolower($sender->getName());
        $money = EconomyAPI::getInstance()->myMoney($name1);
        $jie = $this->banname->get("贷款玩家");
        if ($this->cunhuan->exists($name1)) {
            $qianshu = $this->cunhuan->get($name1);
        } else {
            $qianshu = 0;
        }
        if ($this->jiedai->exists($name1)) {
            $daishu = $this->jiedai->get($name1);
        } else {
            $daishu = 0;
        }

        if ($command->getName() == "bbank") {
            if (isset($args[0])) {
                switch ($args[0]) {
                    case "存款":
                        if ($name == "CONSOLE") {
                            $sender->sendMessage(TM::RED . "[Bbank]控制台存个鸟钱！");
                            return true;
                        }
                        if (isset($args[1])) {
                            if ($args[1] > 0) {
                                if ($money >= $args[1]) {
                                    if ($this->cunhuan->exists($name1)) {
                                        $this->cunhuan->set($name1, $args[1] + $this->cunhuan->get($name1));
                                        $this->cunhuan->save();
                                        EconomyAPI::getInstance()->reduceMoney($name1, $args[1]);
                                        $sender->sendMessage(TM::GREEN . "[Bbank]已存款" . $args[1] . "元！");
                                        return true;
                                    } else {
                                        $this->cunhuan->set($name1, $args[1]);
                                        $this->cunhuan->save();
                                        $sender->sendMessage(TM::GREEN . "[Bbank]已存款" . $args[1] . "元！");
                                        EconomyAPI::getInstance()->reduceMoney($name1, $args[1]);
                                        return true;
                                    }
                                } else {
                                    $sender->sendMessage(TM::RED . "[Bbank]你没有足够的钱！");
                                    return true;
                                }
                            } else {
                                $sender->sendMessage(TM::RED . "[Bbank]请输入有效的数值！");
                                return true;
                            }
                        } else {
                            $sender->sendMessage(TM::RED . "[Bbank]请输入/bbank 存款 存款数目");
                            return true;
                        }
                        break;

                    case "取款":
                        if ($name == "CONSOLE") {
                            $sender->sendMessage(TM::RED . "[Bbank]控制台取个鸟钱！");
                            return true;
                        }
                        if (isset($args[1])) {
                            if ($args[1] > 0) {
                                if ($this->cunhuan->exists($name1)) {
                                    if ($this->cunhuan->get($name1) >= $args[1]) {
                                        $this->cunhuan->set($name1, $this->cunhuan->get($name1) - $args[1]);
                                        $this->cunhuan->save();
                                        EconomyAPI::getInstance()->addMoney($name1, $args[1]);
                                        $sender->sendMessage(TM::GREEN . "[Bank]已取款" . $args[1] . "元！");
                                        return true;
                                    } else {
                                        $sender->sendMessage(TM::RED . "[Bank]你的银行账号余额不足！");
                                        return true;
                                    }
                                } else {
                                    $sender->sendMessage(TM::RED . "[Bank]你并没有存款进银行！");
                                    return true;
                                }
                            } else {
                                $sender->sendMessage(TM::RED . "[Bbank]请输入有效的数值！");
                                return true;
                            }
                        } else {
                            $sender->sendMessage(TM::RED . "[Bbank]请输入/bbank 取款 取款数目");
                            return true;
                        }
                        break;

                    case "贷款":
                        if ($name == "CONSOLE") {
                            $sender->sendMessage(TM::RED . "[Bbank]控制台贷个鸟钱！");
                            return true;
                        }
                        if (isset($args[1])) {
                            if ($args[1] > 0) {
                                $sb = $this->config->get("贷款手续费(false or 数字)");
                                if ($sb == false) {
                                    if ($this->jiedai->exists($name1)) {
                                        if ($args[1] > $this->config->get("最大贷款数") - $this->jiedai->get($name1)) {
                                            $xiaohei = $this->config->get("最大贷款数") - $this->jiedai->get($name1);
                                            $sender->sendMessage(TM::RED . "[Bbank]您最多只能再贷" . $xiaohei . "元！");
                                            return true;
                                        } else {
                                            $this->jiedai->set($name1, $args[1] + $this->jiedai->get($name1));
                                            $this->jiedai->save();
                                            $sender->sendMessage(TM::GREEN . "[Bbank]已添加贷款" . $args[1] . "元！");
                                            $sender->sendMessage(TM::GREEN . "[Bbank]总共贷款" . $this->jiedai->get($name1) . "元！");
                                            EconomyAPI::getInstance()->addMoney($name1, $args[1]);
                                            return true;
                                        }
                                    } else {
                                        if ($args[1] > $this->config->get("最大贷款数")) {
                                            $sender->sendMessage(TM::RED . "[Bbank]您输入的数值大于最大贷款数" . $this->config->get("最大贷款数") . "元！");
                                            return true;
                                        } else {
                                            $this->jiedai->set($name1, $args[1]);
                                            $this->jiedai->save();
                                            $jie[] = $name1;
                                            $this->banname->set("贷款玩家",$jie);
                                            $this->banname->save();
                                            $sender->sendMessage(TM::GREEN . "[Bbank]已贷款" . $args[1] . "元！");
                                            EconomyAPI::getInstance()->addMoney($name1, $args[1]);
                                            return true;
                                        }
                                    }
                                }
                                if ($sb > 0) {
                                    $zzh = $sb * 0.01;
                                    if ($money >= $args[1] * $zzh) {
                                        if ($this->jiedai->exists($name1)) {
                                            if ($args[1] > $this->config->get("最大贷款数") - $this->jiedai->get($name1)) {
                                                $zhazhahei = $this->config->get("最大贷款数") - $this->jiedai->get($name1);
                                                $sender->sendMessage(TM::RED . "[Bbank]您最多只能再贷" . $zhazhahei . "元！");
                                                return true;
                                            } else {
                                                $sender->sendMessage(TM::GREEN . "[Bbank]已扣除手续费" . $args[1] * $zzh . "元！");
                                                EconomyAPI::getInstance()->reduceMoney($name1, $args[1] * $zzh);
                                                $this->jiedai->set($name1, $args[1] + $this->jiedai->get($name1));
                                                $this->jiedai->save();
                                                $sender->sendMessage(TM::GREEN . "[Bbank]已添加贷款" . $args[1] . "元！");
                                                $sender->sendMessage(TM::GREEN . "[Bbank]总共贷款" . $this->jiedai->get($name1) . "元！");
                                                EconomyAPI::getInstance()->addMoney($name1, $args[1]);
                                                return true;
                                            }
                                        } else {
                                            if ($args[1] > $this->config->get("最大贷款数")) {
                                                $sender->sendMessage(TM::RED . "[Bbank]您输入的数值大于最大贷款数" . $this->config->get("最大贷款数") . "元！");
                                                return true;
                                            } else {
                                                $sender->sendMessage(TM::GREEN . "[Bbank]已扣除手续费" . $args[1] * $zzh . "元！");
                                                EconomyAPI::getInstance()->reduceMoney($name1, $args[1] * $zzh);
                                                $this->jiedai->set($name1, $args[1]);
                                                $this->jiedai->save();
                                                $jie[] = $name1;
                                                $this->banname->set("贷款玩家",$jie);
                                                $this->banname->save();
                                                $sender->sendMessage(TM::GREEN . "[Bbank]已贷款" . $args[1] . "元！");
                                                EconomyAPI::getInstance()->addMoney($name1, $args[1]);
                                                return true;
                                            }
                                        }
                                    } else {
                                        $sender->sendMessage(TM::RED . "[Bbank]您无法支付为 " . $args[1] * $zzh . "元 的昂贵高利贷手续费！");
                                        return true;
                                    }
                                } else {
                                    $sender->sendMessage(TM::RED . "[Bbank]您亲爱的腐竹在配置文件输入了无效手续费数值！");
                                    return true;
                                }
                            } else {
                                $sender->sendMessage(TM::RED . "[Bbank]请输入有效的数值！");
                                return true;
                            }
                        } else {
                            $sender->sendMessage(TM::RED . "[Bbank]请输入/bbank 贷款 贷款数目");
                            return true;
                        }
                        break;

                    case "还贷":
                        if ($name == "CONSOLE") {
                            $sender->sendMessage(TM::RED . "[Bbank]控制台还个鸟钱！");
                            return true;
                        }
                        if (isset($args[1])) {
                            if ($args[1] > 0) {
                                if ($this->jiedai->exists($name1)) {
                                    if ($money >= $args[1]) {
                                        if ($this->jiedai->get($name1) >= $args[1]) {
                                            $this->jiedai->set($name1, $this->jiedai->get($name1) - $args[1]);
                                            $this->jiedai->save();
                                            $sender->sendMessage(TM::GREEN . "[Bbank]已还贷" . $args[1] . "元！");
                                            EconomyAPI::getInstance()->reduceMoney($name1, $args[1]);
                                            if ($this->jiedai->get($name1) <= 0) {
                                                $jie2 = array_search($name1, $jie);
                                                    unset($jie[$jie2]);
                                                    $this->banname->set("贷款玩家", $jie);
                                                    $this->banname->save();
                                                    $this->jiedai->remove($name1);
                                                    $this->jiedai->save();
                                                    $sender->sendMessage(TM::GREEN . "[Bbank]恭喜你还清了所有高利贷！无债一身清！");
                                                    return true;
                                            } else {
                                                $sender->sendMessage(TM::GREEN . "[Bbank]还有贷款" . $this->jiedai->get($name1) . "元！");
                                                return true;
                                            }
                                        } else {
                                            $sender->sendMessage(TM::GREEN . "[Bbank]你并没有欠那么多债！");
                                            return true;
                                        }
                                    } else {
                                        $sender->sendMessage(TM::RED . "[Bbank]你没有足够的钱！");
                                        return true;
                                    }
                                } else {
                                    $sender->sendMessage(TM::RED . "[Bbank]你并没有贷款过！");
                                    return true;
                                }
                            } else {
                                $sender->sendMessage(TM::RED . "[Bbank]请输入有效的数值！");
                                return true;
                            }
                        } else {
                            $sender->sendMessage(TM::RED . "[Bbank]请输入/bbank 还贷 还贷数目");
                            return true;
                        }
                        break;
                    case "查询":
                        if ($name == "CONSOLE") {
                            $sender->sendMessage(TM::RED . "[Bbank]控制台查个鸟钱！");
                            return true;
                        }
                        $sender->sendMessage(TM::BLUE . "======银行账户======");
                        $sender->sendMessage(TM::YELLOW . "尊贵的" . $name . "！");
                        $sender->sendMessage(TM::GREEN . "您的余额为" . $qianshu . "元！");
                        $sender->sendMessage(TM::GREEN . "您已经贷款" . $daishu . "元！");
                        return true;
                        break;
                    case "查看":
                        if ($sender->isOP()) {
                            if (isset($args[1])) {
                                if ($this->cunhuan->exists(strtolower($args[1]))) {
                                    $laji = $this->cunhuan->get(strtolower($args[1]));
                                } else {
                                    $laji = 0;
                                }
                                if ($this->jiedai->exists(strtolower($args[1]))) {
                                    $zhaa = $this->jiedai->get(strtolower($args[1]));
                                } else {
                                    $zhaa = 0;
                                }
                                $sender->sendMessage(TM::GREEN . $args[1] . "的余额为" . $laji . "元！");
                                $sender->sendMessage(TM::GREEN . $args[1] . "已经贷款" . $zhaa . "元！");
                                return true;
                            } else {
                                $sender->sendMessage(TM::RED . "[Bbank]请输入/bbank 查看 玩家名");
                                return true;
                            }
                        }
                        break;
                    case "设置时间":
                        if ($sender->isOP()) {
                            if (isset($args[1]) AND isset($args[2]) AND isset($args[3])) {
                                $this->config->set("利息添加时间", $args[1] . ":" . $args[2] . ":" . $args[3]);
                                $this->config->save();
                                $sender->sendMessage(TM::GREEN . "[Bbank]成功设置利息添加时间");
                                return true;
                            } else {
                                $sender->sendMessage(TM::RED . "[Bbank]请输入/bbank 设置时间 时(24) 分(59) 秒(59)");
                                return true;
                            }
                        }
                        break;
                    case "帮助":
                        if ($sender->isOP()) {
                            $sender->sendMessage("§e[Bbank 银行帮助]");
                            $sender->sendMessage("§a<1>§b/bbank 存款 存款数目  §6把钱存入银行");
                            $sender->sendMessage("§a<2>§b/bbank 取款 取款数目  §6把钱从银行取出");
                            $sender->sendMessage("§a<3>§b/bbank 贷款 贷款数目  §6向银行借高利贷");
                            $sender->sendMessage("§a<4>§b/bbank 还贷 还贷数目  §6还了欠银行的债");
                            $sender->sendMessage("§a<5>§b/bbank 查看 玩家名    §6查看玩家银行信息");
                            $sender->sendMessage("§a<6>§b/bbank 解封 玩家名    §6解封该名玩家(仅控制台可用)");
                            $sender->sendMessage("§a<7>§b/bbank 设置时间 时(24) 分(59) 秒(59) §6设置利息添加时间");
                            $sender->sendMessage("§a<8>§b/bbank 查询 §6查看自己的银行信息");
                            $sender->sendMessage("§a<9>§b/bbank 帮助 §6查看指令帮助");
                            return true;
                        } else {

                            $sender->sendMessage("§e[Bbank 银行帮助]");
                            $sender->sendMessage("§a<1>§b/bbank 存款 存款数目  §6把钱存入银行");
                            $sender->sendMessage("§a<2>§b/bbank 取款 取款数目  §6把钱从银行取出");
                            $sender->sendMessage("§a<3>§b/bbank 贷款 贷款数目  §6向银行借高利贷");
                            $sender->sendMessage("§a<4>§b/bbank 还贷 还贷数目  §6还了欠银行的债");
                            $sender->sendMessage("§a<5>§b/bbank 查询 §6查看自己的银行信息");
                            $sender->sendMessage("§a<6>§b/bbank 帮助 §6查看指令帮助");
                            return true;
                        }
                        break;

                    case"解封":
                        $ban2 = $this->config->get("不还钱被BAN");
                        $feng = $this->banname->get("被封玩家");
                        if ($ban2 == true) {
                            if ($name == "CONSOLE") {
                                if (isset($args[1])) {
                                    if(in_array($args[1], $feng)){
                                        $feng2 = array_search($args[1], $feng);
                                        unset($feng[$feng2]);
                                        $this->banname->set("被封玩家", $feng);
                                        $this->banname->save();
                                        $sender->sendMessage(TM::GREEN . "[Bbank]" . $args[1] . "已移除出被封名单！");
                                        return true;
                                        } else {
                                        $sender->sendMessage(TM::RED . "[Bbank]" . $args[1] . "根本没有被封禁！");
                                        return true;
                                       }
                                    } else {
                                        $sender->sendMessage(TM::RED . "[Bbank]请输入/bbank 解封 玩家名");
                                        return true;
                                    }
                                } else {
                                    $sender->sendMessage(TM::RED . "[Bbank]您没有权限使用此指令！");
                                    return true;
                                }
                            } else {
                                $sender->sendMessage(TM::RED . "[Bbank]你没有开启“不还钱被BAN”功能！");
                                return true;
                            }
                        }
                return true;
            } else {
                $sender->sendMessage(TM::RED . "[Bbank]请输入/bbank 帮助 查看帮助！");
                return true;
            }
        }
    }

    public function timer()
    {
        $jjk = $this->config->get("是否开启存款有利息");
            $hr = date("H");
            $mn = date("i");
            $sc = date("s");
            if ($this->config->get("利息添加时间") == $hr . ":" . $mn . ":" . $sc) {
                if ($jjk == true) {
                    foreach ($this->cunhuan->getAll() as $n => $m) {
                        $lil = $this->config->get("存款利率") * 0.01;
                        $this->cunhuan->set($n, $m + $lil * $m);
                        $this->cunhuan->save();
                        $this->getServer()->BroadCastMessage("[Bbank]所有玩家的存款已添加利息！");
                    }
                }
                $ban = $this->config->get("不还钱被BAN");
                if ($ban == true) {
                    foreach ($this->jiedai->getAll() as $n2 => $m1) {
                        $lil2 = $this->config->get("贷款利率") * 0.01;
                        $money1 = EconomyAPI::getInstance()->myMoney($n2);
                        $lil3 = $lil2 * $m1;
                        if ($money1 >= $lil3) {
                            EconomyAPI::getInstance()->reduceMoney($n2, $lil3);
                            $this->getServer()->BroadCastMessage("[Bbank]所有玩家的贷款已收取利息！");
                        }else{
                            $jie2 = $this->banname->get("贷款玩家");
                            $jie3 = array_search($n2, $jie2);
                            $jie = $this->banname->get("被封玩家");
                            $jie[] = $n2;
                            $this->banname->set("被封玩家", $jie);
                            $this->banname->save();
                            foreach ($this->getServer()->getOnlinePlayers() as $p) {
                                if($n2 == strtolower($p->getName())){
                                    $msg1 = $this->banname->get("提示");
                                    $p->kick($reason = $msg1);
                                }
                            }
                            unset($jie2[$jie3]);
                            $this->banname->set("贷款玩家", $jie2);
                            $this->banname->save();
                            $this->jiedai->remove($n2);
                            $this->jiedai->save();
                            $this->getServer()->BroadCastMessage("[Bbank]" . $n2 . "等人因欠债不还，无法支付高额高利贷利息而被封杀！");
                        }
                    }
                } else {
                    foreach ($this->jiedai->getAll() as $n3 => $m2) {
                        $lil2 = $this->config->get("贷款利率") * 0.01;
                        $this->jiedai->set($n3, $m2 + $lil2 * $m2);
                        $this->jiedai->save();
                        $this->getLogger()->info("[Bbank]所有玩家贷款已收取利息！");
                        $this->getServer()->BroadCastMessage("[Bbank]您的贷款已收取利息！");
                }
            }
        }
    }
    public function onPlayerJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $name = $player->getName();
        $name1 = strtolower($name);
        $zhizhang = $this->config->get("进服提示");
        if($zhizhang == true){
            if ($this->cunhuan->exists($name1)) {
                $qianshu = $this->cunhuan->get($name1);
            } else {
                $qianshu = 0;
            }
            if ($this->jiedai->exists($name1)) {
                $daishu = $this->jiedai->get($name1);
            } else {
                $daishu = 0;
            }
                $player->sendMessage(TM::BLUE . "尊贵的" . $name . "，欢迎来到服务器！");
                $player->sendMessage("§6您的银行帐号信息如下：");
                $player->sendMessage(TM::GREEN . "您的余额为" . $qianshu . "元！");
                $player->sendMessage(TM::GREEN . "您已经贷款" . $daishu . "元！");
                $player->sendMessage("§6您可输入/bbank 帮助 获取更多信息！");
            }
        }
    public function onPreLogin(PlayerPreLoginEvent $event)
    {
        $player = $event->getPlayer();
        $name = $player->getName();
        $msg = $this->banname->get("提示");
        $fengsha1 = $this->banname->get("被封玩家");
        if(in_array($name, $fengsha1)){
            $player->kick($reason = $msg);
            $event->setCancelled();
        }
    }
}
