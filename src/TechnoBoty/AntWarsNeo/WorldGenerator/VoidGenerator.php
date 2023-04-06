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
                           $world->setBlockAt($i,0,$k,VanillaBlocks::SNOW());
                           $world->setBlockAt($i,63,$k,VanillaBlocks::SNOW());
                       }
                   }
                   break;
               case 1:
                   for($i = ($chunkX * 16) - 16;$i <= ($chunkX * 16 - 1);$i++){
                       for($k = ($chunkZ * 16) - 16;$k <= ($chunkZ * 16 - 1);$k++){
                           $world->setBlockAt($i,0,$k,VanillaBlocks::REDSTONE());
                           $world->setBlockAt($i,63,$k,VanillaBlocks::REDSTONE());
                       }
                   }
                   break;
               case -1:
                   for($i = ($chunkX * 16) - 16;$i <= ($chunkX * 16 - 1);$i++){
                       for($k = ($chunkZ * 16) + 15;$k >= ($chunkZ * 16);$k--){
                           $world->setBlockAt($i,0,$k,VanillaBlocks::COAL());
                           $world->setBlockAt($i,63,$k,VanillaBlocks::COAL());
                       }
                   }
                   break;
               case -2:
                   for($i = ($chunkX * 16) - 16;$i <= ($chunkX * 16 - 1);$i++){
                       for($k = ($chunkZ * 16) + 15;$k >= ($chunkZ * 16);$k--){
                           $world->setBlockAt($i,0,$k,VanillaBlocks::IRON());
                           $world->setBlockAt($i,63,$k,VanillaBlocks::IRON());
                       }
                   }
                   break;
           }
        }elseif($chunkX == -1){
            switch($chunkZ){
                case 2:
                    for($i = ($chunkX * 16) + 15;$i >= ($chunkX * 16);$i--){
                        for($k = ($chunkZ * 16) - 16;$k <= ($chunkZ * 16 - 1);$k++){
                            $world->setBlockAt($i,0,$k,VanillaBlocks::GLOWSTONE());
                            $world->setBlockAt($i,63,$k,VanillaBlocks::GLOWSTONE());
                        }
                    }
                    break;
                case 1:
                    for($i = ($chunkX * 16) + 15;$i >= ($chunkX * 16);$i--){
                        for($k = ($chunkZ * 16) - 16;$k <= ($chunkZ * 16 - 1);$k++){
                            $world->setBlockAt($i,0,$k,VanillaBlocks::COAL());
                            $world->setBlockAt($i,63,$k,VanillaBlocks::COAL());
                        }
                    }
                    break;
                case -1:
                    for($i = ($chunkX * 16) + 15;$i >= ($chunkX * 16);$i--){
                        for($k = ($chunkZ * 16) + 15;$k >= ($chunkZ * 16);$k--){
                            $world->setBlockAt($i,0,$k,VanillaBlocks::IRON());
                            $world->setBlockAt($i,63,$k,VanillaBlocks::IRON());
                        }
                    }
                    break;
                case -2:
                    for($i = ($chunkX * 16) + 15;$i >= ($chunkX * 16);$i--){
                        for($k = ($chunkZ * 16) + 15;$k >= ($chunkZ * 16);$k--){
                            $world->setBlockAt($i,0,$k,VanillaBlocks::REDSTONE());
                            $world->setBlockAt($i,63,$k,VanillaBlocks::REDSTONE());
                        }
                    }
                    break;
            }
        }elseif($chunkX == -2){
            switch($chunkZ) {
                case 2:
                    for($i = ($chunkX * 16) + 15; $i >= ($chunkX * 16); $i--) {
                        for($k = ($chunkZ * 16) - 16; $k <= ($chunkZ * 16 - 1); $k++) {
                            $world->setBlockAt($i, 0, $k, VanillaBlocks::OAK_LOG());
                            $world->setBlockAt($i, 63, $k, VanillaBlocks::OAK_LOG());
                        }
                    }
                    break;
                case 1:
                    for($i = ($chunkX * 16) + 15; $i >= ($chunkX * 16); $i--) {
                        for($k = ($chunkZ * 16) - 16; $k <= ($chunkZ * 16 - 1); $k++) {
                            $world->setBlockAt($i, 0, $k, VanillaBlocks::BEDROCK());
                            $world->setBlockAt($i, 63, $k, VanillaBlocks::BEDROCK());
                        }
                    }
                    break;
                case -1:
                    for($i = ($chunkX * 16) + 15; $i >= ($chunkX * 16); $i--) {
                        for($k = ($chunkZ * 16) + 15; $k >= ($chunkZ * 16); $k--) {
                            $world->setBlockAt($i, 0, $k, VanillaBlocks::MELON());
                            $world->setBlockAt($i, 63, $k, VanillaBlocks::MELON());
                        }
                    }
                    break;
                case -2:
                    for($i = ($chunkX * 16) + 15; $i >= ($chunkX * 16); $i--) {
                        for($k = ($chunkZ * 16) + 15; $k >= ($chunkZ * 16); $k--) {
                            $world->setBlockAt($i, 0, $k, VanillaBlocks::GOLD());
                            $world->setBlockAt($i, 63, $k, VanillaBlocks::GOLD());
                        }
                    }
                    break;
            }
        }elseif($chunkX == 2){
            switch($chunkZ){
                case 2:
                    for($i = ($chunkX * 16) - 16;$i <= ($chunkX * 16 - 1);$i++){
                        for($k = ($chunkZ * 16) - 16;$k <= ($chunkZ * 16 - 1);$k++){
                            $world->setBlockAt($i,0,$k,VanillaBlocks::OAK_LOG());
                            $world->setBlockAt($i,63,$k,VanillaBlocks::OAK_LOG());
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
                            $world->setBlockAt($i,0,$k,VanillaBlocks::MELON());
                            $world->setBlockAt($i,63,$k,VanillaBlocks::MELON());
                        }
                    }
                    break;
                case -2:
                    for($i = ($chunkX * 16) - 16;$i <= ($chunkX * 16 - 1);$i++){
                        for($k = ($chunkZ * 16) + 15;$k >= ($chunkZ * 16);$k--){
                            $world->setBlockAt($i,0,$k,VanillaBlocks::GOLD());
                            $world->setBlockAt($i,63,$k,VanillaBlocks::GOLD());
                            //
                        }
                    }
                    break;
            }
        }
    }
    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ) : void{}
}