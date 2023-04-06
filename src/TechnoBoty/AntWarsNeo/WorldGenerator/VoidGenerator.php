<?php

namespace TechnoBoty\AntWarsNeo\WorldGenerator;

use pocketmine\block\VanillaBlocks;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\world\format\SubChunk;
use pocketmine\world\generator\Generator;

class VoidGenerator extends Generator{

    private Chunk $chunk;

    public function __construct(int $seed, string $preset){
        parent::__construct($seed, $preset);
        $this->chunk = new Chunk([],FALSE);
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ) : void{
        $world->setChunk($chunkX,$chunkZ,clone $this->chunk);
        if($chunkX == 1){
           switch($chunkZ){
               case 2:
                   for($i = ($chunkX * 16) - 16;$i <= ($chunkX * 16 - 1);$i++){
                       for($k = ($chunkZ * 16) - 16;$k <= ($chunkZ * 16 - 1);$k++){
                           $world->setBlockAt($i,0,$k,VanillaBlocks::BEDROCK());
                           $world->setBlockAt($i,63,$k,VanillaBlocks::BEDROCK());
                           if($k == $chunkZ * 16 - 1){
                               for($j = 1;$j < 63;$j++){
                                   $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                               }
                           }
                       }
                   }
                   break;
               case 1:
                   for($i = ($chunkX * 16) - 16;$i <= ($chunkX * 16 - 1);$i++){
                       for($k = ($chunkZ * 16) - 16;$k <= ($chunkZ * 16 - 1);$k++){
                           $world->setBlockAt($i,0,$k,VanillaBlocks::BEDROCK());
                           $world->setBlockAt($i,63,$k,VanillaBlocks::BEDROCK());
                       }
                   }
                   break;
               case -1:
                   for($i = ($chunkX * 16) - 16;$i <= ($chunkX * 16 - 1);$i++){
                       for($k = ($chunkZ * 16) + 15;$k >= ($chunkZ * 16);$k--){
                           $world->setBlockAt($i,0,$k,VanillaBlocks::BEDROCK());
                           $world->setBlockAt($i,63,$k,VanillaBlocks::BEDROCK());
                       }
                   }
                   break;
               case -2:
                   for($i = ($chunkX * 16) - 16;$i <= ($chunkX * 16 - 1);$i++){
                       for($k = ($chunkZ * 16) + 15;$k >= ($chunkZ * 16);$k--){
                           $world->setBlockAt($i,0,$k,VanillaBlocks::BEDROCK());
                           $world->setBlockAt($i,63,$k,VanillaBlocks::BEDROCK());
                           if($k == $chunkZ * 16){
                               for($j = 1;$j < 63;$j++){
                                   $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                               }
                           }
                       }
                   }
                   break;
           }
        }elseif($chunkX == -1){
            switch($chunkZ){
                case 2:
                    for($i = ($chunkX * 16) + 15;$i >= ($chunkX * 16);$i--){
                        for($k = ($chunkZ * 16) - 16;$k <= ($chunkZ * 16 - 1);$k++){
                            $world->setBlockAt($i,0,$k,VanillaBlocks::BEDROCK());
                            $world->setBlockAt($i,63,$k,VanillaBlocks::BEDROCK());
                            if($k == $chunkZ * 16 - 1){
                                for($j = 1;$j < 63;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                        }
                    }
                    break;
                case 1:
                    for($i = ($chunkX * 16) + 15;$i >= ($chunkX * 16);$i--){
                        for($k = ($chunkZ * 16) - 16;$k <= ($chunkZ * 16 - 1);$k++){
                            $world->setBlockAt($i,0,$k,VanillaBlocks::BEDROCK());
                            $world->setBlockAt($i,63,$k,VanillaBlocks::BEDROCK());
                        }
                    }
                    break;
                case -1:
                    for($i = ($chunkX * 16) + 15;$i >= ($chunkX * 16);$i--){
                        for($k = ($chunkZ * 16) + 15;$k >= ($chunkZ * 16);$k--){
                            $world->setBlockAt($i,0,$k,VanillaBlocks::BEDROCK());
                            $world->setBlockAt($i,63,$k,VanillaBlocks::BEDROCK());
                        }
                    }
                    break;
                case -2:
                    for($i = ($chunkX * 16) + 15;$i >= ($chunkX * 16);$i--){
                        for($k = ($chunkZ * 16) + 15;$k >= ($chunkZ * 16);$k--){
                            $world->setBlockAt($i,0,$k,VanillaBlocks::BEDROCK());
                            $world->setBlockAt($i,63,$k,VanillaBlocks::BEDROCK());
                            if($k == $chunkZ * 16){
                                for($j = 1;$j < 63;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                        }
                    }
                    break;
            }
        }elseif($chunkX == -2){
            switch($chunkZ) {
                case 2:
                    for($i = ($chunkX * 16) + 15; $i >= ($chunkX * 16); $i--) {
                        for($k = ($chunkZ * 16) - 16; $k <= ($chunkZ * 16 - 1); $k++) {
                            $world->setBlockAt($i, 0, $k, VanillaBlocks::BEDROCK());
                            $world->setBlockAt($i, 63, $k, VanillaBlocks::BEDROCK());
                            if($i == $chunkX * 16){
                                for($j = 1;$j < 63;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                            if($k == $chunkZ * 16 - 1){
                                for($j = 1;$j < 63;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                        }
                    }
                    break;
                case 1:
                    for($i = ($chunkX * 16) + 15; $i >= ($chunkX * 16); $i--) {
                        for($k = ($chunkZ * 16) - 16; $k <= ($chunkZ * 16 - 1); $k++) {
                            $world->setBlockAt($i, 0, $k, VanillaBlocks::BEDROCK());
                            $world->setBlockAt($i, 63, $k, VanillaBlocks::BEDROCK());
                            if($i == $chunkX * 16){
                                for($j = 1;$j < 63;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                        }
                    }
                    break;
                case -1:
                    for($i = ($chunkX * 16) + 15; $i >= ($chunkX * 16); $i--) {
                        for($k = ($chunkZ * 16) + 15; $k >= ($chunkZ * 16); $k--) {
                            $world->setBlockAt($i, 0, $k, VanillaBlocks::BEDROCK());
                            $world->setBlockAt($i, 63, $k, VanillaBlocks::BEDROCK());
                            if($i == $chunkX * 16){
                                for($j = 1;$j < 63;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                        }
                    }
                    break;
                case -2:
                    for($i = ($chunkX * 16) + 15; $i >= ($chunkX * 16); $i--) {
                        for($k = ($chunkZ * 16) + 15; $k >= ($chunkZ * 16); $k--) {
                            $world->setBlockAt($i, 0, $k, VanillaBlocks::BEDROCK());
                            $world->setBlockAt($i, 63, $k, VanillaBlocks::BEDROCK());
                            if($i == $chunkX * 16){
                                for($j = 1;$j < 63;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                            if($k == $chunkZ * 16){
                                for($j = 1;$j < 63;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                        }
                    }
                    break;
            }
        }elseif($chunkX == 2){
            switch($chunkZ){
                case 2:
                    for($i = ($chunkX * 16) - 16;$i <= ($chunkX * 16 - 1);$i++){
                        for($k = ($chunkZ * 16) - 16;$k <= ($chunkZ * 16 - 1);$k++){
                            $world->setBlockAt($i,0,$k,VanillaBlocks::BEDROCK());
                            $world->setBlockAt($i,63,$k,VanillaBlocks::BEDROCK());
                            if($i == $chunkX * 16 - 1){
                                for($j = 1;$j < 63;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                            if($k == $chunkZ * 16 - 1){
                                for($j = 1;$j < 63;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                        }
                    }
                    break;
                case 1:
                    for($i = ($chunkX * 16) - 16;$i <= ($chunkX * 16 - 1);$i++){
                        for($k = ($chunkZ * 16) - 16;$k <= ($chunkZ * 16 - 1);$k++){
                            $world->setBlockAt($i,0,$k,VanillaBlocks::BEDROCK());
                            $world->setBlockAt($i,63,$k,VanillaBlocks::BEDROCK());
                            if($i == $chunkX * 16 - 1){
                                for($j = 1;$j < 63;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                        }
                    }
                    break;
                case -1:
                    for($i = ($chunkX * 16) - 16;$i <= ($chunkX * 16 - 1);$i++){
                        for($k = ($chunkZ * 16) + 15;$k >= ($chunkZ * 16);$k--){
                            $world->setBlockAt($i,0,$k,VanillaBlocks::BEDROCK());
                            $world->setBlockAt($i,63,$k,VanillaBlocks::BEDROCK());
                            if($i == $chunkX * 16 - 1){
                                for($j = 1;$j < 63;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                        }
                    }
                    break;
                case -2:
                    for($i = ($chunkX * 16) - 16;$i <= ($chunkX * 16 - 1);$i++){
                        for($k = ($chunkZ * 16) + 15;$k >= ($chunkZ * 16);$k--){
                            $world->setBlockAt($i,0,$k,VanillaBlocks::BEDROCK());
                            $world->setBlockAt($i,63,$k,VanillaBlocks::BEDROCK());
                            if($i == $chunkX * 16 - 1){
                                for($j = 1;$j < 63;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                            if($k == $chunkZ * 16){
                                for($j = 1;$j < 63;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                        }
                    }
                    break;
            }
        }
    }
    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ) : void{}
}