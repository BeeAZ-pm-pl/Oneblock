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

class Tier2Task extends Task{

  /** @var Oneblock $tier */
  public Oneblock $main;

  public $block;
  
  public function __construct(Oneblock $main, $block){
    $this->main = $main;
    $this->block = $block;
  }
  
  public function onRun() : void{
    $world = $this->block->getPosition()->getWorld();
    switch(mt_rand(1, 20)){
      case 1:
        $world->setBlock($this->block->getPosition(), VanillaBlocks::OAK_LOG());
        break;
        
        case 3:
          $world->setBlock($this->block->getPosition(), VanillaBlocks::ACACIA_LOG());
          break;
          
          case 4:
         $world->setBlock($this->block->getPosition(), VanillaBlocks::BIRCH_LOG()); 
        break;
        
        case 7:
          $world->setBlock($this->block->getPosition(), VanillaBlocks::GOLD_ORE());
          break;
          
          case 8:
            $world->setBlock($this->block->getPosition(), VanillaBlocks::DIAMOND_ORE());
            break;
            
            case 10:
              $world->setBlock($this->block->getPosition(), VanillaBlocks::DARK_OAK_LOG());
              break;
              
              case 15:
                $world->setBlock($this->block->getPosition(), VanillaBlocks::STRIPPED_SPRUCE_LOG());
                break;
          
        default:
        $world->setBlock($this->block->getPosition(), VanillaBlocks::STONE());
        break;
    }
  }
} 