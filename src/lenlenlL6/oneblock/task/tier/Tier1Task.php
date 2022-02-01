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

namespace lenlenlL6\oneblock\task\tier;

use lenlenlL6\oneblock\Oneblock;
use pocketmine\scheduler\Task;
use pocketmine\block\VanillaBlocks;

class Tier1Task extends Task{

  /** @var Oneblock $tier */
  public Oneblock $main;

  public $block;
  
  public function __construct(Oneblock $main, $block){
    $this->main = $main;
    $this->block = $block;
  }
  
  public function onRun() : void{
    $world = $this->block->getPosition()->getWorld();
    switch(mt_rand(1, 15)){
      case 1:
        $world->setBlock($this->block->getPosition(), VanillaBlocks::OAK_LOG());
        break;
        
        case 2:
          $world->setBlock($this->block->getPosition(), VanillaBlocks::GRASS());
          break;
          
          case 5:
            $world->setBlock($this->block->getPosition(), VanillaBlocks::TNT());
            break;
            
            case 9:
              $world->setBlock($this->block->getPosition(), VanillaBlocks::DIAMOND_ORE());
              break;
              
              case 12:
                $world->setBlock($this->block->getPosition(), VanillaBlocks::IRON_ORE());
                break;
                
                case 15:
                  $world->setBlock($this->block->getPosition(), VanillaBlocks::PACKED_ICE());
                  break;
        
        default:
        $world->setBlock($this->block->getPosition(), VanillaBlocks::STONE());
        break;
    }
  }
}
