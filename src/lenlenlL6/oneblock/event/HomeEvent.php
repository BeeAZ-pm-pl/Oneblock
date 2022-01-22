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
 
 
 Called when the player returns to their island.
*/

namespace lenlenlL6\oneblock\event;

use pocketmine\player\Player; 
use pocketmine\world\Position;
use lenlenlL6\oneblock\Oneblock;
use lenlenlL6\oneblock\event\OneblockEvent;

class HomeEvent extends OneblockEvent{
  
  public function __construct(Oneblock $main, Player $player, Position $pos){
    $this->main = $main;
    $this->player = $player; //Player returns
    $this->pos = $pos; //Position when player returns their home
  }
  
  public function getPlayer() : Player{
    return $this->player;
  }
  
  public function getPosition() : Position{
    return $this->pos;
  }
}