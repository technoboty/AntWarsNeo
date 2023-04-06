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
        if($chunkX == 0){
            if($chunkZ == 0){
                for($i = $chunkX;$i <= ($chunkX + 15);$i++){
                    for($k = $chunkZ + 1;$k <= ($chunkZ + 15);$k++){
                        $world->setBlockAt($i,0,$k,VanillaBlocks::GOLD());
                    }
                }
            }
        }
        if($chunkX == 1){
            if($chunkZ == 1){
                for($i = $chunkX - 1;$i >= ($chunkX - 15);$i--){
                    for($k = $chunkZ - 1;$k >= ($chunkZ - 15);$k--){
                        $world->setBlockAt($i,0,$k,VanillaBlocks::LAPIS_LAZULI());
                    }
                }
            }
        }
        if($chunkX == 0){
            if($chunkZ == 1){
                for($i = $chunkX + 1;$i >= ($chunkX - 15);$i--){
                    for($k = $chunkZ - 1;$k <= ($chunkZ + 15);$k++){
                        $world->setBlockAt($i,0,$k,VanillaBlocks::REDSTONE());
                    }
                }
            }
        }
    }
    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ) : void{}
}