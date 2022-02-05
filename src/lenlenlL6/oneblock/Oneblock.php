<?php

/*
___             _     _            _    
  / _ \ _ __   ___| |__ | | ___   ___| | __
 | | | | '_ \ / _ \ '_ \| |/ _ \ / __| |/ /
 | |_| | | | |  __/ |_) | | (_) | (__|   < 
  \___/|_| |_|\___|_.__/|_|\___/ \___|_|\_\
 
 ((/)) An upgrade of oneblock pm3 made by lenlenlL6 and Dora.
 ((/)) If you have problems with the plugin, contact me.
 ---> Facebook: https://www.facebook.com/profile.php?id=100071316150096
 ---> Github: https://github.com/lenlenlL6
 ((/)) Copyright by lenlenlL6 and Dora.
*/

namespace lenlenlL6\oneblock;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\{Command, CommandSender};
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\world\World;
use pocketmine\world\Position;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use lenlenlL6\oneblock\OneblockManager;
use lenlenlL6\oneblock\event\{CreateIslandEvent, DeleteIslandEvent, HomeEvent, AddFriendEvent, RemoveFriendEvent, TeleportEvent, TierChangeEvent};
use lenlenlL6\oneblock\task\CreateIslandTask;
use lenlenlL6\oneblock\task\tier\{Tier1Task, Tier2Task, Tier3Task, Tier4Task, Tier5Task, Tier6Task, Tier7Task, Tier8Task, Tier9Task, Tier10Task};
use lenlenlL6\oneblock\libs\jojoe77777\FormAPI\SimpleForm;
use lenlenlL6\oneblock\libs\jojoe77777\FormAPI\CustomForm;
use czechpmdevs\multiworld\util\WorldUtils;
use pocketmine\world\WorldCreationOptions;
use czechpmdevs\multiworld\MultiWorld;

class Oneblock extends PluginBase implements Listener {

    public $prefix = "§r§l§a[§b• §eONE BLOCK §b•§a]";

    public $maxtier = 10;

    /** @var Config $island */
    public Config $island;

    /** @var Config $level */
    public Config $level;

    /** @var Config $lang */
    public Config $lang;

    /** @var Config $tier */
    public Config $tier;

    public $levelname;

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveResource("lang.yml");
        $this->saveDefaultConfig();
        $this->lang = new Config($this->getDataFolder() . "lang.yml", Config::YAML);
        $this->tier = new Config($this->getDataFolder() . "tier.yml", Config::YAML);
        $this->level = new Config($this->getDataFolder() . "level.yml", Config::YAML);
        $this->island = new Config($this->getDataFolder() . "islands.yml", Config::YAML);
        if (!class_exists(MultiWorld::class)) {
            $this->getServer()->getLogger()->info("\n\n§cOneBlock > Missing multiworld plugin.\n");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
    }

    public function onDisable(): void {
        $this->saveAll();
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        if (!$this->tier->exists($player->getName())) {
            $this->tier->set($player->getName(), 1);
            $this->level->set($player->getName(), 0);
            $this->saveAll();
        }
    }

    public function onCommand(CommandSender $player, Command $cmd, String $label, array $args): bool {
        switch ($cmd->getName()) {
            case "oneblock":
                if ($player instanceof Player) {
                    $this->MenuForm($player);
                } else {
                    $player->sendMessage($this->prefix . "§c This command just work in game !");
                }
                break;
        }
        return true;
    }
    //Main Form
    public function MenuForm(Player $player) {

        $form = new SimpleForm(function (Player $player, int $data = null) {

            if ($data === null) {
                return true;
            }
            switch ($data) {
                case 0:
                    if (!$this->isHaveIsland($player)) {
                        #$this->getServer()->dispatchCommand(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), "mw create oneblock-" . $player->getName() . " 0 void");
                        $levelname = "oneblock-" . $player->getName();
                        $generator = WorldUtils::getGeneratorByName($generatorName = "void");
                        $this->getServer()->getWorldManager()->generateWorld(
                            name: $levelname,
                            options: WorldCreationOptions::create()
                                ->setSeed(0)
                                ->setGeneratorClass($generator->getGeneratorClass())
                        );
                        $this->island->setNested("islands.oneblock-" . $player->getName() . ".friends", $player->getName());
                        $this->island->setNested("islands.oneblock-" . $player->getName() . ".lock", false);
                        $this->island->setNested("islands.oneblock-" . $player->getName() . ".lockpvp", false);
                        $this->island->setNested("islands.oneblock-" . $player->getName() . ".spawn", "256 65 256 oneblock-" . $player->getName());
                        $this->island->save();
                        $this->getScheduler()->scheduleDelayedTask(new CreateIslandTask($this, $player), 5 * 20);
                        $msg = $this->lang->get("WAITING_CREATE_ISLAND");
                        $player->sendMessage($this->prefix . " $msg");
                        (new CreateIslandEvent($this, $player))->call();
                    } else {
                        $msg = $this->lang->get("ALREADY_HAVE_ISLAND");
                        $player->sendMessage($this->prefix . " $msg");
                    }
                    break;

                case 1:
                    if ($this->isHaveIsland($player)) {
                        $this->ManageIslandForm($player);
                    } else {
                        $msg = $this->lang->get("NO_ISLAND");
                        $player->sendMessage($this->prefix . " $msg");
                    }
                    break;

                case 2:
                    $this->TpAnother($player);
                    break;

                case 3:
                    $this->Top($player);
                    break;
            }
        });
        $form->setTitle("§l§a【 §bONE BLOCK MENU §a】");
        $form->addButton("§l§a• CREATE ISLAND •", 1, "https://www.vhv.rs/dpng/d/453-4533087_how-to-create-png-images-create-icon-transparent.png");
        $form->addButton("§l§a• MANAGE YOUR ISLAND •", 1, "https://png.pngtree.com/png-clipart/20190619/original/pngtree-file-manager-glyph-black-icon-png-image_4008309.jpg");
        $form->addButton("§l§a• TELEPORT TO ANOTHER ISLAND •", 1, "https://www.clipartmax.com/png/middle/169-1690744_address-1-visit-icon.png");
        $form->addButton("§l§a• TOP TIER •", 1, "https://toppng.com/uploads/preview/top-with-upwards-arrow-11523878763m2mn4hdnyd.png");
        $form->sendToPlayer($player);
        return $form;
    }

    public function Top(Player $player) {

        $form = new CustomForm(function (Player $player, array $data = null) {

            if ($data === null) {
                $this->MenuForm($player);
                return true;
            }
        });
        $txt = "";
        $all = $this->tier->getAll();
        arsort($all);
        $all = array_slice($all, 0, 5);
        $top = 1;
        foreach ($all as $name => $tier) {
            $txt .= "TOP $top belongs to $name WITH TIER IS $tier";
            $top++;
        }
        $form->setTitle("§l§e• §bTOP TIER §e•");
        $form->addLabel($txt);
        $form->sendToPlayer($player);
        return $form;
    }

    public function TpAnother(Player $player) {

        $form = new CustomForm(function (Player $player, array $data = null) {

            if ($data === null) {
                $this->MenuForm($player);
                return true;
            }
            if ($data[0] === null) {
                $msg = $this->lang->get("EMPTY_INPUT");
                $player->sendMessage($this->prefix . " $msg");
                return true;
            }
            if ($data[0] === $player->getName()) {
                $msg = $this->lang->get("NAME");
                $player->sendMessage($this->prefix . " $msg");
                return true;
            }
            if ($this->island->getNested("islands.oneblock-" . $data[0])) {
                if (!$this->isLock("oneblock-" . $data[0])) {
                    $pos = $this->island->getNested("islands.oneblock-" . $data[0] . ".spawn");
                    $ex = explode(" ", $pos);
                    $x = ((int)$ex[0]);
                    $y = ((int)$ex[1]);
                    $z = ((int)$ex[2]);
                    $worldp = $ex[3];
                    $this->getServer()->getWorldManager()->loadWorld($worldp);
                    $world = $this->getServer()->getWorldManager()->getWorldByName($worldp);
                    $player->teleport(new Position($x, $y, $z, $world));
                    $msg = $this->lang->get("TELEPORT_ISLAND");
                    $change = str_replace("{name}", $data[0], $msg);
                    $player->sendMessage($this->prefix . " $change");
                    $pos = new Position($x, $y, $z, $world);
                    (new TeleportEvent($this, $player, $pos))->call();
                } else {
                    $msg = $this->lang->get("ISLAND_LOCK");
                    $change = str_replace("{name}", $data[0], $msg);
                    $player->sendMessage($this->prefix . " $change");
                }
            } else {
                $msg = $this->lang->get("TARGET_NOT_ISLAND");
                $change = str_replace("{name}", $data[0], $msg);
                $player->sendMessage($this->prefix . " $change");
            }
        });
        $form->setTitle("§l§e• §bISLAND TELEPORT §e•");
        $form->addInput("§cEnter the name of the person you want to teleport:");
        $form->sendToPlayer($player);
        return $form;
    }

    public function ManageIslandForm(Player $player) {

        $form = new SimpleForm(function (Player $player, int $data = null) {

            if ($data === null) {
                $this->MenuForm($player);
                return true;
            }
            switch ($data) {
                case 0:
                    if ($this->isHaveIsland($player)) {
                        $pos = $this->island->getNested("islands.oneblock-" . $player->getName() . ".spawn");
                        $ex = explode(" ", $pos);
                        $x = ((int)$ex[0]);
                        $y = ((int)$ex[1]);
                        $z = ((int)$ex[2]);
                        $lv = $ex[3];
                        $this->getServer()->getWorldManager()->loadWorld($lv);
                        $world = $this->getServer()->getWorldManager()->getWorldByName($lv);
                        $player->teleport(new Position($x, $y, $z, $world));
                        $msg = $this->lang->get("BACK_ISLAND");
                        $player->sendMessage($this->prefix . " $msg");
                        $pos = new Position($x, $y, $z, $world);
                        (new HomeEvent($this, $player, $pos))->call();
                    } else {
                        $msg = $this->lang->get("NO_ISLAND");
                        $player->sendMessage($this->prefix . " $msg");
                    }
                    break;



                case 1:
                    $this->addFriend($player);
                    break;

                case 2:
                    $this->removeFriend($player);
                    break;

                case 3:
                    $this->LockForm($player);
                    break;

                case 4:
                    $this->LockPvpForm($player);
                    break;

                case 5:
                    if ($this->isHaveIsland($player)) {
                        $x = $player->getLocation()->getFloorX();
                        $y = $player->getLocation()->getFloorY();
                        $z = $player->getLocation()->getFloorZ();
                        $world = $player->getWorld()->getDisplayName();
                        $this->island->setNested("islands.oneblock-" . $player->getName() . ".spawn", "$x $y $z $world");
                        $this->island->save();
                        $msg = $this->lang->get("SET_NEW_SPAWN");
                        $player->sendMessage($this->prefix . " $msg");
                    } else {
                        $msg = $this->lang->get("NO_ISLAND");
                        $player->sendMessage($this->prefix . " $msg");
                    }
                    break;

                case 6:
                    if ($this->isHaveIsland($player)) {
                        $this->getServer()->dispatchCommand(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), "mw delete oneblock-" . $player->getName());
                        $this->island->removeNested("islands.oneblock-" . $player->getName());
                        $this->island->save();
                        $msg = $this->lang->get("DELETE_ISLAND");
                        $player->sendMessage($this->prefix . " $msg");
                        (new DeleteIslandEvent($this, $player))->call();
                    } else {
                        $msg = $this->lang->get("NO_ISLAND");
                        $player->sendMessage($this->prefix . " $msg");
                    }
                    break;
            }
        });
        $form->setTitle("§l§e• §bMANAGE YOUR ISLAND §e•");
        $form->addButton("§l§a• TELEPORT TO YOUR ISLAND •", 1, "https://w7.pngwing.com/pngs/336/478/png-transparent-computer-icons-house-house-angle-building-black.png");

        $form->addButton("§l§a• ADD FRIEND •", 1, "https://png.pngtree.com/png-clipart/20190614/original/pngtree-add-vector-icon-png-image_3791307.jpg");
        $form->addButton("§l§a• REMOVE FRIEND •", 1, "https://img.favpng.com/0/6/22/subtraction-plus-and-minus-signs-button-computer-icons-png-favpng-FLpZhrq8S9cXq5Ms1zVp41PPx.jpg");
        $form->addButton("§l§a• LOCK/UNLOCK ISLAND •", 1, "https://w7.pngwing.com/pngs/485/864/png-transparent-padlock-s-locked-files-website-pin-tumbler-lock-padlock.png");
        $form->addButton("§l§a• LOCK/UNLOCK PVP •", 1, "https://www.pinclipart.com/picdir/middle/168-1688988_open-two-swords-crossed-png-clipart.png");
        $form->addButton("§l§a• SET NEW SPAWN •", 1, "https://img.favpng.com/19/14/16/map-symbolization-mountain-pass-pictogram-png-favpng-Lw01c0uWMuFVkMBK9LTFDRmmc.jpg");
        $form->addButton("§l§a• DELETE ISLAND •", 1, "https://png.pngtree.com/element_our/20190528/ourmid/pngtree-delete-icon-image_1129289.jpg");
        $form->sendToPlayer($player);
        return $form;
    }

    public function ListFriend(Player $player) {

        $form = new CustomForm(function (Player $player, array $data = null) {

            if ($data === null) {
                $this->ManageIslandForm($player);
                return true;
            }
        });
        $ex = explode(", ", $this->island->getNested("islands.oneblock-" . $player->getName() . ".friends"));
        $tru = array_diff([$player->getName()], $ex);
        $im = implode(", ", $tru);
        $form->setTitle("§l§e• §bLIST FRIEND §e•");
        $form->addLabel($im);
        $form->sendToPlayer($player);
        return $form;
    }

    public function addFriend(Player $player) {

        $form = new CustomForm(function (Player $player, array $data = null) {

            if ($data === null) {
                $this->ManageIslandForm($player);
                return true;
            }
            if ($data[0] === null) {
                $msg = $this->lang->get("EMPTY_INPUT");
                $player->sendMessage($this->prefix . " $msg");
                return true;
            }
            if ($data[0] === $player->getName()) {
                $msg = $this->lang->get("NAME");
                $player->sendMessage($this->prefix . " $msg");
                return true;
            }
            if ($this->isHaveIsland($player)) {
                $ex = explode(", ", $this->island->getNested("islands.oneblock-" . $player->getName() . ".friends"));
                if (!in_array($data[0], $ex)) {
                    $im = implode(", ", $ex);
                    $txt = "$im, " . $data[0];
                    $this->island->setNested("islands.oneblock-" . $player->getName() . ".friends", $txt);
                    $this->island->save();
                    $msg = $this->lang->get("ADD_FRIEND");
                    $change = str_replace("{name}", $data[0], $msg);
                    $player->sendMessage($this->prefix . " $change");
                    (new AddFriendEvent($this, $player, $data[0]))->call();
                } else {
                    $msg = $this->lang->get("FRIEND_EXIST");
                    $change = str_replace("{name}", $data[0], $msg);
                    $player->sendMessage($this->prefix . " $change");
                }
            } else {
                $msg = $this->lang->get("NO_ISLAND");
                $player->sendMessage($this->prefix . " $msg");
            }
        });
        $form->setTitle("§l§e• §bADD FRIENDS §e•");
        $form->addInput("§cEnter the name of the person you want to add as a friend:");
        $form->sendToPlayer($player);
        return $form;
    }

    public function removeFriend(Player $player) {

        $form = new CustomForm(function (Player $player, array $data = null) {

            if ($data === null) {
                $this->ManageIslandForm($player);
                return true;
            }
            if ($data[0] === null) {
                $msg = $this->lang->get("EMPTY_INPUT");
                $player->sendMessage($this->prefix . " $msg");
                return true;
            }
            if ($data[0] === $player->getName()) {
                $msg = $this->lang->get("NAME");
                $player->sendMessage($this->prefix . " $msg");
                return true;
            }
            if ($this->isHaveIsland($player)) {
                $ex = explode(", ", $this->island->getNested("islands.oneblock-" . $player->getName() . ".friends"));
                if (in_array($data[0], $ex)) {
                    $im = implode(", ", $ex);
                    $replace = str_replace(", " . $data[0], "", $im);
                    $this->island->setNested("islands.oneblock-" . $player->getName() . ".friends", $replace);
                    $this->island->save();
                    $msg = $this->lang->get("REMOVE_FRIEND");
                    $change = str_replace("{name}", $data[0], $msg);
                    $player->sendMessage($this->prefix . " $change");
                    (new RemoveFriendEvent($this, $player, $data[0]))->call();
                } else {
                    $msg = $this->lang->get("NOT_FRIEND_FOUND");
                    $change = str_replace("{name}", $data[0], $msg);
                    $player->sendMessage($this->prefix . " $change");
                }
            } else {
                $msg = $this->lang->get("NO_ISLAND");
                $player->sendMessage($this->prefix . " $msg");
            }
        });
        $form->setTitle("§l§e• §bREMOVE FRIENDS §e•");
        $form->addInput("§cEnter the name of the person you want to remove from your friends list:");
        $form->sendToPlayer($player);
        return $form;
    }

    public function LockForm(Player $player) {

        $form = new CustomForm(function (Player $player, array $data = null) {

            if ($data === null) {
                $this->ManageIslandForm($player);
                return true;
            }
            if ($data[0] === false) {
                if ($this->isHaveIsland($player)) {
                    $this->island->setNested("islands.oneblock-" . $player->getName() . ".lock", false);
                    $this->island->save();
                    $msg = $this->lang->get("UNLOCK");
                    $player->sendMessage($this->prefix . " $msg");
                } else {
                    $msg = $this->lang->get("NO_ISLAND");
                    $player->sendMessage($this->prefix . " $msg");
                }
                return true;
            }
            if ($data[0] === true) {
                if ($this->isHaveIsland($player)) {
                    $this->island->setNested("islands.oneblock-" . $player->getName() . ".lock", true);
                    $this->island->save();
                    $msg = $this->lang->get("LOCK");
                    $player->sendMessage($this->prefix . " $msg");
                } else {
                    $msg = $this->lang->get("NO_ISLAND");
                    $player->sendMessage($this->prefix . " $msg");
                }
                return true;
            }
        });
        $form->setTitle("§l§e• §bLOCK ISLAND §e•");
        $form->addToggle("§cOff / On", false);
        $form->sendToPlayer($player);
        return $form;
    }

    public function LockPvpForm(Player $player) {

        $form = new CustomForm(function (Player $player, array $data = null) {

            if ($data === null) {
                $this->ManageIslandForm($player);
                return true;
            }
            if ($data[0] === false) {
                if ($this->isHaveIsland($player)) {
                    $this->island->setNested("islands.oneblock-" . $player->getName() . ".lockpvp", false);
                    $this->island->save();
                    $msg = $this->lang->get("UNLOCK_PVP");
                    $player->sendMessage($this->prefix . " $msg");
                } else {
                    $msg = $this->lang->get("NO_ISLAND");
                    $player->sendMessage($this->prefix . " $msg");
                }
                return true;
            }
            if ($data[0] === true) {
                if ($this->isHaveIsland($player)) {
                    $this->island->setNested("islands.oneblock-" . $player->getName() . ".lockpvp", true);
                    $this->island->save();
                    $msg = $this->lang->get("LOCK_PVP");
                    $player->sendMessage($this->prefix . " $msg");
                } else {
                    $msg = $this->lang->get("NO_ISLAND");
                    $player->sendMessage($this->prefix . " $msg");
                }
                return true;
            }
        });
        $form->setTitle("§l§e• §bLOCK PVP");
        $form->addToggle("§cOff / On", false);
        $form->sendToPlayer($player);
        return $form;
    }

    public function onDamage(EntityDamageByEntityEvent $event) {
        $player = $event->getEntity();
        if ($player instanceof Player) {
            $worldp = $player->getWorld()->getDisplayName();
            $ex = explode("-", $worldp);
            if ($ex[0] === "oneblock") {
                if ($this->island->getNested("islands." . $worldp . ".lockpvp") === true) {
                    $event->cancel();
                    $msg = $this->lang->get("PVP");
                    $damager = $event->getDamager();
                    if ($damager instanceof Player) {
                        $damager->sendMessage($this->prefix . " $msg");
                    }
                }
            }
        }
    }

    public function onBreak(BlockBreakEvent $event) {
        $player = $event->getPlayer();
        $worldp = $player->getWorld()->getDisplayName();
        $block = $event->getBlock();
        $ex = explode("-", $worldp);
        if ($ex[0] === "oneblock") {
            $friends = explode(", ", $this->island->getNested("islands." . $worldp . ".friends"));
            if (!in_array($player->getName(), $friends)) {
                $event->cancel();
                $msg = $this->lang->get("BREAK");
                $player->sendMessage($this->prefix . " $msg");
            } else {
                if (!$event->isCancelled()) {
                    if ($block->getPosition()->getX() === 256) {
                        if ($block->getPosition()->getY() === 64) {
                            if ($block->getPosition()->getZ() === 256) {
                                switch ($this->getTier($player)) {
                                    case 1:
                                        $this->level->set($player->getName(), ($this->level->get($player->getName()) + 1));
                                        $this->level->save();
                                        $this->getScheduler()->scheduleDelayedTask(new Tier1Task($this, $block), 5);
                                        break;

                                    case 2:
                                        $this->level->set($player->getName(), ($this->level->get($player->getName()) + 1));
                                        $this->level->save();
                                        $this->getScheduler()->scheduleDelayedTask(new Tier2Task($this, $block), 5);
                                        break;

                                    case 3:
                                        $this->level->set($player->getName(), ($this->level->get($player->getName()) + 1));
                                        $this->level->save();
                                        $this->getScheduler()->scheduleDelayedTask(new Tier3Task($this, $block), 5);
                                        break;

                                    case 4:
                                        $this->level->set($player->getName(), ($this->level->get($player->getName()) + 1));
                                        $this->level->save();
                                        $this->getScheduler()->scheduleDelayedTask(new Tier4Task($this, $block), 5);
                                        break;

                                    case 5:
                                        $this->level->set($player->getName(), ($this->level->get($player->getName()) + 1));
                                        $this->level->save();
                                        $this->getScheduler()->scheduleDelayedTask(new Tier5Task($this, $block), 5);
                                        break;

                                    case 6:
                                        $this->level->set($player->getName(), ($this->level->get($player->getName()) + 1));
                                        $this->level->save();
                                        $this->getScheduler()->scheduleDelayedTask(new Tier6Task($this, $block), 5);
                                        break;

                                    case 7:
                                        $this->level->set($player->getName(), ($this->level->get($player->getName()) + 1));
                                        $this->level->save();
                                        $this->getScheduler()->scheduleDelayedTask(new Tier7Task($this, $block), 5);
                                        break;

                                    case 8:
                                        $this->level->set($player->getName(), ($this->level->get($player->getName()) + 1));
                                        $this->level->save();
                                        $this->getScheduler()->scheduleDelayedTask(new Tier8Task($this, $block), 5);
                                        break;

                                    case 9:
                                        $this->level->set($player->getName(), ($this->level->get($player->getName()) + 1));
                                        $this->level->save();
                                        $this->getScheduler()->scheduleDelayedTask(new Tier9Task($this, $block), 5);
                                        break;

                                    case 10:
                                        $this->level->set($player->getName(), ($this->level->get($player->getName()) + 1));
                                        $this->level->save();
                                        $this->getScheduler()->scheduleDelayedTask(new Tier10Task($this, $block), 5);
                                        break;
                                }
                                if ($this->getLevelTier($player) >= $this->getTier($player) * 100) {
                                    switch ($this->getTier($player)) {
                                        case $this->getMaxTier():
                                            $player->sendTitle("§cMAX TIER", "§cMORE TIER IN NEXT UPDATE");
                                            $this->level->set($player->getName(), 0);
                                            $this->tier->set($player->getName(), $this->getMaxTier());
                                            $this->level->save();
                                            $this->tier->save();
                                            break;

                                        default:
                                            $this->tier->set($player->getName(), ($this->tier->get($player->getName()) + 1));
                                            $this->tier->save();
                                            $this->level->set($player->getName(), 0);
                                            $this->level->save();
                                            $player->sendTitle("§l§eTIER UP", "§aTIER §c" . $this->tier->get($player->getName()));
                                            (new TierChangeEvent($this, $player))->call();
                                            break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function onPlace(BlockPlaceEvent $event) {
        $player = $event->getPlayer();
        $worldp = $player->getWorld()->getDisplayName();
        $ex = explode("-", $worldp);
        if ($ex[0] === "oneblock") {
            $friends = explode(", ", $this->island->getNested("islands." . $worldp . ".friends"));
            if (!in_array($player->getName(), $friends)) {
                $event->cancel();
                $msg = $this->lang->get("PLACE");
                $player->sendMessage($this->prefix . " $msg");
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $worldp = $player->getWorld()->getDisplayName();
        $ex = explode("-", $worldp);
        if ($ex[0] === "oneblock") {
            $friends = explode(", ", $this->island->getNested("islands." . $worldp . ".friends"));
            if (!in_array($player->getName(), $friends)) {
                $event->cancel();
                $msg = $this->lang->get("INTERACT");
                $player->sendMessage($this->prefix . " $msg");
            }
        }
    }

    public function isHaveIsland(Player $player): bool {
        return $this->getServer()->getWorldManager()->isWorldGenerated("oneblock-" . $player->getName());
    }

    public function getMaxTier(): int {
        return $this->maxtier;
    }

    public function saveAll(): void {
        $this->tier->save();
        $this->level->save();
        $this->island->save();
    }

    public function getTier(Player $player): int {
        return $this->tier->get($player->getName());
    }

    public function getLevelTier(Player $player): int {
        return $this->level->get($player->getName());
    }

    public function isLock(string $nameisland): bool {
        return $this->island->getNested("islands." . $nameisland . ".lock") === true;
    }
}
