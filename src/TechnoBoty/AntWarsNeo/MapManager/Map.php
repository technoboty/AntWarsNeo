<?php

namespace TechnoBoty\AntWarsNeo\MapManager;

use pocketmine\Server;
use pocketmine\world\World;
use TechnoBoty\AntWarsNeo\Main;

class Map {

    private World $world;
    private bool $flag = FALSE;

    public function __construct(World $world) {
        $this->world = $world;
    }

    public function getWorld(): World {
        return $this->world;
    }

    public function deleteWorld(string $path = ""): void {
        if($path == "") {
            $folder = Server::getInstance()->getDataPath() . "worlds/" . $this->world->getFolderName() . "/";
            foreach(scandir($folder) as $file) {
                if($file == "." || $file == "..") {
                    continue;
                }
                if(is_dir($folder . $file)) {
                    $this->deleteWorld($folder . $file);
                } else {
                    unlink($folder . $file);
                }
            }
            rmdir($folder);
        } else {
            foreach(scandir($path) as $file) {
                if($file == "." || $file == "..") {
                    continue;
                }
                if(is_dir($path . "/" . $file)) {
                    $this->deleteWorld($path . "/" . $file);
                } else {
                    unlink($path ."/". $file);
                }
            }
            rmdir($path);
        }
    }
}