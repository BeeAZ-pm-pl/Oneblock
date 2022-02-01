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
 
 
 Called when the player remove friend
*/ 

namespace lenlenlL6\oneblock\event;

use pocketmine\player\Player;
use lenlenlL6\oneblock\Oneblock;
use lenlenlL6\oneblock\event\OneblockEvent;

class RemoveFriendEvent extends OneblockEvent{

  /** @var Oneblock $tier */
  public Oneblock $main;

  /** @var Player $player */
  public Player $player;

  public $target;
  
  public function __construct(Oneblock $main, Player $player, string $target){
    $this->main = $main;
    $this->player = $player;
    $this->target = $target;
  }
  
  public function getPlayer() : Player{
    return $this->player;
  }
  
  public function getTarget() : string{
    return $this->target;
  }
} 