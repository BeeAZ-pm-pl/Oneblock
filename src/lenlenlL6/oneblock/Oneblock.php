<?php
/*
___             _     _            _    
  / _ \ _ __   ___| |__ | | ___   ___| | __
 | | | | '_ \ / _ \ '_ \| |/ _ \ / __| |/ /
 | |_| | | | |  __/ |_) | | (_) | (__|   < 
  \___/|_| |_|\___|_.__/|_|\___/ \___|_|\_\
 
 ((/)) An upgrade of oneblock pm3 made by lenlenlL6, BeeAZ-pm-pl.
 ((/)) If you have problems with the plugin, contact me.
 ---> Facebook: https://www.facebook.com/profile.php?id=100071316150096
 ---> Github: https://github.com/lenlenlL6
 ((/)) Copyright by lenlenlL6, BeeAZ-pm-pl.
*/

namespace lenlenlL6\oneblock;

use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;
use pocketmine\utils\Config;
use pocketmine\player\Player;
use lenlenlL6\oneblock\EventListener;
use lenlenlL6\oneblock\command\OneblockCommand;

final class Oneblock extends PluginBase{
  
  public static Config $config;
  
  public static Config $is;
  
  public static Config $player;
  
  public static Plugin $plugin;
  
  public function onEnable() : void{
    self::$plugin = $this;
    $this->saveDefaultConfig();
    self::$config = $this->getConfig();
    self::$is = new Config($this->getDataFolder() . "is.yml", Config::YAML);
    self::$player = new Config($this->getDataFolder() . "player.yml", Config::YAML);
    foreach(self::$config->get("tier") as $tier => $block){
      if(count($block) <= 0){
        $this->getServer()->getPluginManager()->disablePlugin($this);
        $this->getLogger()->info("The number of blocks of a tier must be greater than 0");
        break;
      }
      if(array_sum($block) < 100 or array_sum($block) > 100){
        $this->getServer()->getPluginManager()->disablePlugin($this);
        $this->getLogger()->info("The sum of the probabilities should be 100");
        break;
      }
    }
    $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
    $this->getServer()->getCommandMap()->register("oneblock", new OneblockCommand());
  }
  
  public static function convertPlayerNameHasASpace(Player $player) : string{
    if(strpos($player->getName(), " ")){
      return str_replace(" ", "_", $player->getName());
    }
    return $player->getName();
  }
}
