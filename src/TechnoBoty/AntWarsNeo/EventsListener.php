<?php

namespace TechnoBoty\AntWarsNeo;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

class EventsListener implements Listener{

    use SingletonTrait;

    public function __construct(){
        self::setInstance($this);
    }
    public function onPlace(BlockPlaceEvent $event){
        $p = $event->getBlockAgainst()->getPosition();
        Server::getInstance()->broadcastMessage($p->getFloorX()." | ".$p->getFloorY()." | ".$p->getFloorZ());
    }
    public function onBreak(BlockBreakEvent $event){
        $world = $event->getBlock()->getPosition()->getWorld();
        //TODO
    }

}