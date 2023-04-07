<?php

namespace TechnoBoty\AntWarsNeo;

use pocketmine\block\TNT;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockBurnEvent;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\Listener;
use pocketmine\item\VanillaItems;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\WorldManager;
use ReflectionException;
use ReflectionProperty;
use TechnoBoty\AntWarsNeo\WorldGenerator\VoidGenerator;

class EventsListener implements Listener{

    use SingletonTrait;

    public function __construct(){
        self::setInstance($this);
    }
    public function onPlace(BlockPlaceEvent $event){
        $p = $event->getBlockAgainst()->getPosition();
        Server::getInstance()->broadcastMessage($p->getFloorX()." | ".$p->getFloorY()." | ".$p->getFloorZ());
    }

    /**
     * TODO
     * @throws ReflectionException
     */
    public function onBreak(BlockBreakEvent $event){
        $world = $event->getBlock()->getPosition()->getWorld();
        ($gen = new ReflectionProperty($world,"generator"))->setAccessible(TRUE);
        if($gen->getValue($world) == VoidGenerator::class){
            switch($event->getBlock()->getIdInfo()->getBlockTypeId()){
                case VanillaBlocks::GLASS()->getIdInfo()->getBlockTypeId():
                    if(mt_rand(1,2) == 2){
                        $event->setDrops([VanillaItems::FEATHER()]);
                    } else {
                        $event->setDrops([]);
                    }
                    break;
                case VanillaBlocks::SAND()->getIdInfo()->getBlockTypeId():
                    if(mt_rand(1,3) == 2){
                        $event->setDrops([VanillaBlocks::TNT()->asItem()]);
                    } else {
                        $event->setDrops([]);
                    }
                    break;
                case VanillaBlocks::SOUL_SAND()->getIdInfo()->getBlockTypeId():
                    if(mt_rand(1,5) == 2){
                        $event->setDrops([VanillaBlocks::NETHER_WART()->asItem()]);
                    } else {
                        $event->setDrops([]);
                    }
                    break;
                case VanillaBlocks::OAK_LEAVES()->getIdInfo()->getBlockTypeId():
                    if(mt_rand(1,5) == 2){
                        $event->setDrops([VanillaItems::APPLE()]);
                    } else {
                        $event->setDrops([]);
                    }
                    break;
                case VanillaBlocks::IRON_ORE()->getIdInfo()->getBlockTypeId():
                    if(mt_rand(1,10) == 2){
                        $event->setDrops([VanillaItems::IRON_INGOT()->setCount(2)]);
                    } else {
                        $event->setDrops([VanillaItems::IRON_INGOT()->setCount(1)]);
                    }
                    break;
                case VanillaBlocks::GOLD_ORE()->getIdInfo()->getBlockTypeId():
                    if(mt_rand(1,10) == 2){
                        $event->setDrops([VanillaItems::GOLD_INGOT()->setCount(2)]);
                    } else {
                        $event->setDrops([VanillaItems::GOLD_INGOT()->setCount(1)]);
                    }
                    break;
                case VanillaBlocks::GRAVEL()->getIdInfo()->getBlockTypeId():
                    if(mt_rand(1,4) == 2){
                        $event->setDrops([VanillaItems::FLINT()]);
                    } else {
                        $event->setDrops([]);
                    }
                    break;
            }
        }
    }
    public function onExplode(EntityExplodeEvent $event){
        $event->setYield(0.0);
    }

}