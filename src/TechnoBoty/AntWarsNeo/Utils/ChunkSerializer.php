<?php

namespace TechnoBoty\AntWarsNeo\Utils;

use pocketmine\world\format\Chunk;
use pocketmine\world\format\io\FastChunkSerializer;
use pocketmine\world\World;

class ChunkSerializer{
    /**
     * @return array<int, Chunk>
     */
    public static function getChunks(World $level, int $sx, int $sz, int $ex, int $ez) : array{
        $chunks = [];
        for($x = $sx; $x - 16 <= $ex; $x += 16){
            for($z = $sz; $z - 16 <= $ez; $z += 16){
                $chunk = $level->loadChunk($x >> 4, $z >> 4);
                if($chunk === null){
                    $level->loadChunk($x,$z);
                }
                $chunks[World::chunkHash($x >> 4, $z >> 4)] = $chunk;
            }
        }
        return $chunks;
    }

    /**
     * @return \Generator<int, Chunk>
     */
    public static function getChunksByGenerator(World $level, int $sx, int $sz, int $ex, int $ez) : \Generator{
        for($x = $sx; $x - 16 <= $ex; $x += 16){
            for($z = $sz; $z - 16 <= $ez; $z += 16){
                ob_start();
                $chunk = $level->loadChunk($x >> 4, $z >> 4);
                ob_end_clean();
                if($chunk === null){
                    throw new \RuntimeException("\$chunk === null");
                }
                (yield World::chunkHash($x >> 4, $z >> 4) => $chunk);
                $level->unloadChunk($x >> 4, $z >> 4, true, false);
            }
        }
    }

    /**
     * @param string $chunks
     * @return array<int, Chunk>
     */
    public static function DecodeChunks(string $chunks) : array{
        $chunks = unserialize($chunks);
        $returnchunks = [];
        foreach($chunks as $hash => $binary){
            $returnchunks[$hash] = FastChunkSerializer::deserializeTerrain($binary);
        }
        return $returnchunks;
    }

    /**
     * @param Chunk[] $chunks
     * @return string[]
     */
    public static function EncodeChunks(array $chunks) : string{
        $returnchunks = [];
        foreach($chunks as $hash => $chunk){
            $returnchunks[$hash] = FastChunkSerializer::serializeTerrain($chunk);
        }
        return serialize($returnchunks);
    }
}