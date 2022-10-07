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

namespace lenlenlL6\oneblock\event;

use pocketmine\event\Event;
use pocketmine\player\Player;

final class TierChangedEvent extends Event{
  
  private Player $player;
  
  public function __construct(Player $player){
    $this->player = $player;
  }
  
  public function getPlayer() : Player{
    return $this->player;
  }
}
