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

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\item\LegacyStringToItemParser;
use lenlenlL6\oneblock\Oneblock;
use lenlenlL6\oneblock\task\SetBlockTask;
use lenlenlL6\oneblock\event\TierChangedEvent;

final class EventListener implements Listener{
  
  public function onJoin(PlayerJoinEvent $event) : void{
    if(!Oneblock::$player->exists(Oneblock::convertPlayerNameHasASpace($event->getPlayer()))){
      Oneblock::$player->setNested(Oneblock::convertPlayerNameHasASpace($event->getPlayer()) . ".tier", 1);
      Oneblock::$player->setNested(Oneblock::convertPlayerNameHasASpace($event->getPlayer()) . ".invitations", []);
      Oneblock::$player->setNested(Oneblock::convertPlayerNameHasASpace($event->getPlayer()) . ".friends", []);
      Oneblock::$player->setNested(Oneblock::convertPlayerNameHasASpace($event->getPlayer()) . ".level", 0);
      Oneblock::$player->save();
      (new TierChangedEvent($event->getPlayer()))->call();
    }
  }
  
  public function onBreak(BlockBreakEvent $event) : void{
    $player = $event->getPlayer();
    if(explode("@@", $player->getWorld()->getDisplayName())[0] === "oneblock"){
      if(explode("@@", $player->getWorld()->getDisplayName())[1] !== Oneblock::convertPlayerNameHasASpace($player) and !in_array(Oneblock::convertPlayerNameHasASpace($player), Oneblock::$player->getNested(explode("@@", $player->getWorld()->getDisplayName())[1] . ".friends"))){
        $event->cancel();
        $player->sendMessage("§7[OneBlock] " . Oneblock::$config->getNested("lang.DO_NOT_HAVE_PERMISSION"));
        return;
      }
      if($event->getBlock()->getPosition()->getX() === 256 and $event->getBlock()->getPosition()->getY() === 64 and $event->getBlock()->getPosition()->getZ() === 256){
        Oneblock::$player->setNested(Oneblock::convertPlayerNameHasASpace($player) . ".level", (Oneblock::$player->getNested(Oneblock::convertPlayerNameHasASpace($player) . ".level") + 1));
        Oneblock::$player->save();
        if(Oneblock::$player->getNested(Oneblock::convertPlayerNameHasASpace($player) . ".level") >= 100*(Oneblock::$player->getNested(Oneblock::convertPlayerNameHasASpace($player) . ".tier"))){
          if(!isset(Oneblock::$config->get("tier")[(string)(Oneblock::$player->getNested(Oneblock::convertPlayerNameHasASpace($player) . ".tier") + 1)])){
            $player->sendMessage("§7[OneBlock] " . Oneblock::$config->getNested("lang.MAXIMUM_TIER"));
            Oneblock::$player->setNested(Oneblock::convertPlayerNameHasASpace($player) . ".level", 0);
            Oneblock::$player->save();
          }else{
            Oneblock::$player->setNested(Oneblock::convertPlayerNameHasASpace($player) . ".tier", (Oneblock::$player->getNested(Oneblock::convertPlayerNameHasASpace($player) . ".tier") + 1));
            Oneblock::$player->setNested(Oneblock::convertPlayerNameHasASpace($player) . ".level", 0);
            Oneblock::$player->save();
            $player->sendMessage("§7[OneBlock] " . str_replace("{tier}", Oneblock::$player->getNested(Oneblock::convertPlayerNameHasASpace($player) . ".tier"), Oneblock::$config->getNested("lang.UP_TIER")));
            (new TierChangedEvent($player))->call();
          }
        }
        $tier = Oneblock::$config->getNested("tier." . Oneblock::$player->getNested(Oneblock::convertPlayerNameHasASpace($player) . ".tier"));
        $prob = [];
        for($i = 0; $i < 100; $i++){
          $rand = array_rand($tier);
          if(count(array_keys($prob, $rand)) >= $tier[$rand]){
            unset($tier[$rand]);
            continue;
          }
          $prob[] = $rand;
        }
        $blockC = explode(":", $prob[array_rand($prob)]);
        Oneblock::$plugin->getScheduler()->scheduleDelayedTask(new SetBlockTask($event->getBlock()->getPosition(), LegacyStringToItemParser::getInstance()->parse((int)$blockC[0].':'.(int)$blockC[1])->getBlock()), 5);
      }
    }
  }
  
  public function onPlace(BlockPlaceEvent $event) : void{
    $player = $event->getPlayer();
    if(explode("@@", $player->getWorld()->getDisplayName())[0] === "oneblock"){
      if(explode("@@", $player->getWorld()->getDisplayName())[1] !== Oneblock::convertPlayerNameHasASpace($player) and !in_array(Oneblock::convertPlayerNameHasASpace($player), Oneblock::$player->getNested(explode("@@", $player->getWorld()->getDisplayName())[1] . ".friends"))){
        $event->cancel();
        $player->sendMessage("§7[OneBlock] " . Oneblock::$config->getNested("lang.DO_NOT_HAVE_PERMISSION"));
        return;
      }
    }
  }
}
