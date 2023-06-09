<?php

namespace TechnoBoty\AntWarsNeo\WorldGenerator;

use pocketmine\block\VanillaBlocks;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\world\format\SubChunk;
use pocketmine\world\generator\Generator;

class VoidGenerator extends Generator{

    private array $chance = [
        "sand" => 15.1,
        "glass" => 15.0,
        "cobblestone" => 19.0,
        "log" => 20.0,
        "iron_ore" => 5.0,
        "leaves" => 4.0,
        "gravel" => 4.0,
        "web" => 3.0,
        "gold_ore" => 2.0,
        "redstone_ore" => 2.0,
        "lapis_ore" => 2.0,
        "melon" => 2.0,
        "diamond_ore" => 3.0,
        "emerald_ore" => 1.0,
        "soul" => 1.0,
        "anvil" => 0.4,
        "potion" => 0.4,
        "enchant" => 0.1,
        "netherite" => 1.0
    ];

    private array $blocks;
    private Chunk $chunk;

    public function __construct(int $seed, string $preset){
        parent::__construct($seed, $preset);
        $this->chunk = new Chunk([],FALSE);
        $this->initBlocks();
    }
    private function initBlocks() : void{
        foreach($this->chance as $key => $chance){
            for($i = 0;$i < $chance * 10;$i++){
                switch($key){
                    case "sand":
                        $this->blocks[] = VanillaBlocks::SAND();
                        break;
                    case "glass":
                        $this->blocks[] = VanillaBlocks::GLASS();
                        break;
                    case "cobblestone":
                        $this->blocks[] = VanillaBlocks::COBBLESTONE();
                        break;
                    case "log":
                        $this->blocks[] = VanillaBlocks::OAK_LOG();
                        break;
                    case "iron_ore":
                        $this->blocks[] = VanillaBlocks::IRON_ORE();
                        break;
                    case "leaves":
                        $this->blocks[] = VanillaBlocks::OAK_LEAVES();
                        break;
                    case "gravel":
                        $this->blocks[] = VanillaBlocks::GRAVEL();
                        break;
                    case "web":
                        $this->blocks[] = VanillaBlocks::COBWEB();
                        break;
                    case "gold_ore":
                        $this->blocks[] = VanillaBlocks::GOLD_ORE();
                        break;
                    case "redstone_ore":
                        $this->blocks[] = VanillaBlocks::REDSTONE_ORE();
                        break;
                    case "lapis_ore":
                        $this->blocks[] = VanillaBlocks::LAPIS_LAZULI_ORE();
                        break;
                    case "melon":
                        $this->blocks[] = VanillaBlocks::MELON();
                        break;
                    case "diamond_ore":
                        $this->blocks[] = VanillaBlocks::DIAMOND_ORE();
                        break;
                    case "emerald_ore":
                        $this->blocks[] = VanillaBlocks::EMERALD_ORE();
                        break;
                    case "anvil":
                        $this->blocks[] = VanillaBlocks::ANVIL();
                        break;
                    case "potion":
                        $this->blocks[] = VanillaBlocks::BREWING_STAND();
                        break;
                    case "enchant":
                        $this->blocks[] = VanillaBlocks::ENCHANTING_TABLE();
                        break;
                    case "soul":
                        $this->blocks[] = VanillaBlocks::SOUL_SAND();
                        break;
                    case "netherite":
                        $this->blocks[] = VanillaBlocks::ANCIENT_DEBRIS();
                        break;
                }
            }
        }
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ) : void{
        $world->setChunk($chunkX,$chunkZ,clone $this->chunk);
        if($chunkX == 1){
           switch($chunkZ){
               case 2:
                   for($i = ($chunkX * 16) - 16;$i <= ($chunkX * 16 - 1);$i++){
                       for($k = ($chunkZ * 16) - 16;$k <= ($chunkZ * 16 - 1);$k++){
                           $this->onSetBlocks($i,$k,$world);
                           if($k == $chunkZ * 16 - 1){
                               for($j = 1;$j < 70;$j++){
                                   $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                               }
                           }
                       }
                   }
                   break;
               case 1:
                   for($i = ($chunkX * 16) - 16;$i <= ($chunkX * 16 - 1);$i++){
                       for($k = ($chunkZ * 16) - 16;$k <= ($chunkZ * 16 - 1);$k++){
                           $this->onSetBlocks($i,$k,$world);
                       }
                   }
                   break;
               case -1:
                   for($i = ($chunkX * 16) - 16;$i <= ($chunkX * 16 - 1);$i++){
                       for($k = ($chunkZ * 16) + 15;$k >= ($chunkZ * 16);$k--){
                           $this->onSetBlocks($i,$k,$world);
                       }
                   }
                   break;
               case -2:
                   for($i = ($chunkX * 16) - 16;$i <= ($chunkX * 16 - 1);$i++){
                       for($k = ($chunkZ * 16) + 15;$k >= ($chunkZ * 16);$k--){
                           $this->onSetBlocks($i,$k,$world);
                           if($k == $chunkZ * 16){
                               for($j = 1;$j < 70;$j++){
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
                            $this->onSetBlocks($i,$k,$world);
                            if($k == $chunkZ * 16 - 1){
                                for($j = 1;$j < 70;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                        }
                    }
                    break;
                case 1:
                    for($i = ($chunkX * 16) + 15;$i >= ($chunkX * 16);$i--){
                        for($k = ($chunkZ * 16) - 16;$k <= ($chunkZ * 16 - 1);$k++){
                            $this->onSetBlocks($i,$k,$world);
                        }
                    }
                    break;
                case -1:
                    for($i = ($chunkX * 16) + 15;$i >= ($chunkX * 16);$i--){
                        for($k = ($chunkZ * 16) + 15;$k >= ($chunkZ * 16);$k--){
                            $this->onSetBlocks($i,$k,$world);
                        }
                    }
                    break;
                case -2:
                    for($i = ($chunkX * 16) + 15;$i >= ($chunkX * 16);$i--){
                        for($k = ($chunkZ * 16) + 15;$k >= ($chunkZ * 16);$k--){
                            $this->onSetBlocks($i,$k,$world);
                            if($k == $chunkZ * 16){
                                for($j = 1;$j < 70;$j++){
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
                    //ang
                    for($i = ($chunkX * 16) + 15; $i >= ($chunkX * 16); $i--) {
                        for($k = ($chunkZ * 16) - 16; $k <= ($chunkZ * 16 - 1); $k++) {
                            $this->onSetBlocks($i,$k,$world);
                            if($i == $chunkX * 16){
                                for($j = 1;$j < 70;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                            if($k == $chunkZ * 16 - 1){
                                for($j = 1;$j < 70;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                        }
                    }
                    break;
                case 1:
                    for($i = ($chunkX * 16) + 15; $i >= ($chunkX * 16); $i--) {
                        for($k = ($chunkZ * 16) - 16; $k <= ($chunkZ * 16 - 1); $k++) {
                            $this->onSetBlocks($i,$k,$world);
                            if($i == $chunkX * 16){
                                for($j = 1;$j < 70;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                        }
                    }
                    break;
                case -1:
                    for($i = ($chunkX * 16) + 15; $i >= ($chunkX * 16); $i--) {
                        for($k = ($chunkZ * 16) + 15; $k >= ($chunkZ * 16); $k--) {
                            $this->onSetBlocks($i,$k,$world);
                            if($i == $chunkX * 16){
                                for($j = 1;$j < 70;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                        }
                    }
                    break;
                case -2:
                    //ang
                    for($i = ($chunkX * 16) + 15; $i >= ($chunkX * 16); $i--) {
                        for($k = ($chunkZ * 16) + 15; $k >= ($chunkZ * 16); $k--) {
                            $this->onSetBlocks($i,$k,$world);
                            if($i == $chunkX * 16){
                                for($j = 1;$j < 70;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                            if($k == $chunkZ * 16){
                                for($j = 1;$j < 70;$j++){
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
                    //ang
                    for($i = ($chunkX * 16) - 16;$i <= ($chunkX * 16 - 1);$i++){
                        for($k = ($chunkZ * 16) - 16;$k <= ($chunkZ * 16 - 1);$k++){
                            $this->onSetBlocks($i,$k,$world);
                            if($i == $chunkX * 16 - 1){
                                for($j = 1;$j < 70;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                            if($k == $chunkZ * 16 - 1){
                                for($j = 1;$j < 70;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                        }
                    }
                    break;
                case 1:
                    for($i = ($chunkX * 16) - 16;$i <= ($chunkX * 16 - 1);$i++){
                        for($k = ($chunkZ * 16) - 16;$k <= ($chunkZ * 16 - 1);$k++){
                            $this->onSetBlocks($i,$k,$world);
                            if($i == $chunkX * 16 - 1){
                                for($j = 1;$j < 70;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                        }
                    }
                    break;
                case -1:
                    for($i = ($chunkX * 16) - 16;$i <= ($chunkX * 16 - 1);$i++){
                        for($k = ($chunkZ * 16) + 15;$k >= ($chunkZ * 16);$k--){
                            $this->onSetBlocks($i,$k,$world);
                            if($i == $chunkX * 16 - 1){
                                for($j = 1;$j < 70;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                        }
                    }
                    break;
                case -2:
                    //ang
                    for($i = ($chunkX * 16) - 16;$i < ($chunkX * 16);$i++){
                        for($k = ($chunkZ * 16) + 15;$k >= ($chunkZ * 16);$k--){
                            $this->onSetBlocks($i,$k,$world);
                            if($i == $chunkX * 16 - 1){
                                for($j = 1;$j < 70;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                            if($k == $chunkZ * 16){
                                for($j = 1;$j < 70;$j++){
                                    $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
                                }
                            }
                        }
                    }
                    break;
            }
        }
    }
    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ) : void{
        if($chunkX == 2){
            if($chunkZ == 2){
                for($i = ($chunkX * 16) - 10;$i < ($chunkX * 16 - 5);$i++){
                    for($k = ($chunkZ * 16) - 10;$k <= ($chunkZ * 16 - 6);$k++){
                        for($j = 56;$j < 61;$j++){
                            if($j == 60 || $j == 56){
                                switch(mt_rand(1,2)){
                                    case 1:
                                        $world->setBlockAt($i,$j,$k,VanillaBlocks::COBBLESTONE());
                                        break;
                                    case 2:
                                        $world->setBlockAt($i,$j,$k,VanillaBlocks::OAK_LOG());
                                        break;
                                }
                            } else {
                                $world->setBlockAt($i, $j, $k, VanillaBlocks::AIR());
                            }
                        }
                    }
                }
            }elseif($chunkZ == -2){
                for($i = ($chunkX * 16) - 10;$i < ($chunkX * 16 - 5);$i++){
                    for($k = ($chunkZ * 16) + 9;$k >= ($chunkZ * 16 + 5);$k--){
                        for($j = 3;$j < 8;$j++){
                            if($j == 3 || $j == 7){
                                switch(mt_rand(1,2)){
                                    case 1:
                                        $world->setBlockAt($i,$j,$k,VanillaBlocks::COBBLESTONE());
                                        break;
                                    case 2:
                                        $world->setBlockAt($i,$j,$k,VanillaBlocks::OAK_LOG());
                                        break;
                                }
                            } else {
                                $world->setBlockAt($i, $j, $k, VanillaBlocks::AIR());
                            }
                        }
                    }
                }
            }
        }elseif($chunkX == -2){
            if($chunkZ == 2){
                for($i = ($chunkX * 16) + 9; $i >= ($chunkX * 16 + 5); $i--) {
                    for($k = ($chunkZ * 16) - 10; $k < ($chunkZ * 16 - 5); $k++) {
                        for($j = 3;$j < 8;$j++){
                            if($j == 3 || $j == 7){
                                switch(mt_rand(1,2)){
                                    case 1:
                                        $world->setBlockAt($i,$j,$k,VanillaBlocks::COBBLESTONE());
                                        break;
                                    case 2:
                                        $world->setBlockAt($i,$j,$k,VanillaBlocks::OAK_LOG());
                                        break;
                                }
                            } else {
                                $world->setBlockAt($i, $j, $k, VanillaBlocks::AIR());
                            }
                        }
                    }
                }
            }elseif($chunkZ == -2){
                for($i = ($chunkX * 16) + 9; $i >= ($chunkX * 16 + 5); $i--) {
                    for($k = ($chunkZ * 16) + 9; $k >= ($chunkZ * 16 + 5); $k--) {
                        for($j = 56;$j < 61;$j++){
                            if($j == 60 || $j == 56){
                                switch(mt_rand(1,2)){
                                    case 1:
                                        $world->setBlockAt($i,$j,$k,VanillaBlocks::COBBLESTONE());
                                        break;
                                    case 2:
                                        $world->setBlockAt($i,$j,$k,VanillaBlocks::OAK_LOG());
                                        break;
                                }
                            } else {
                                $world->setBlockAt($i, $j, $k, VanillaBlocks::AIR());
                            }
                        }
                    }
                }
            }
        }
    }

    private function onSetBlocks(int $i,int $k,ChunkManager $world) : void{
        for($j = 0;$j < 71;$j++){
            if($j == 0){
                $world->setBlockAt($i,$j,$k,VanillaBlocks::BEDROCK());
            }elseif($j == 63) {
                $world->setBlockAt($i, $j, $k, VanillaBlocks::BARRIER());
            }elseif($j == 70){
                $world->setBlockAt($i, $j, $k, VanillaBlocks::BARRIER());
            } elseif($j < 63) {
                $world->setBlockAt($i,$j,$k,$this->blocks[array_rand($this->blocks)]);
            }
        }
    }
}