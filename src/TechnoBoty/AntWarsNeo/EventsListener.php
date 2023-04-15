<?php

namespace TechnoBoty\AntWarsNeo;

use pocketmine\block\TNT;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockBurnEvent;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\FireAspectEnchantment;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\types\PlayerAction;
use pocketmine\player\GameMode;
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
     * @throws ReflectionException
     */
    public function onBreak(BlockBreakEvent $event){
        $world = $event->getBlock()->getPosition()->getWorld();
        ($gen = new ReflectionProperty($world,"generator"))->setAccessible(TRUE);
        if($gen->getValue($world) == VoidGenerator::class){
            if($event->getPlayer()->getGamemode()->equals(GameMode::CREATIVE())){return;}
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
    public function onInteract(PlayerInteractEvent $event){
        $inv = $event->getPlayer()->getInventory();
        $hand = $inv->getItemInHand();
        if($inv->contains(VanillaItems::LAPIS_LAZULI()->setCount(1))){
            if($event->getPlayer()->getXpManager()->getXpLevel() >= 1){
                switch(TRUE){
                    case $hand->equals(VanillaItems::WOODEN_SWORD()):
                    case $hand->equals(VanillaItems::STONE_SWORD()):
                    case $hand->equals(VanillaItems::IRON_SWORD()):
                    case $hand->equals(VanillaItems::GOLDEN_SWORD()):
                    case $hand->equals(VanillaItems::DIAMOND_SWORD()):
                    case $hand->equals(VanillaItems::NETHERITE_SWORD()):
                        $inv->setItemInHand($hand->addEnchantment(new EnchantmentInstance($this->getRandomEnchant(0),1)));
                        $event->cancel();
                        break;
                }
                $event->getPlayer()->getXpManager()->setXpLevel($event->getPlayer()->getXpManager()->getXpLevel() - 1);
            }
        }
    }

    /** 0 - мечи
     * 1 - лопаты
     * 2 - кирки
     * 3 - топоры
     * 4 - шлема
     * 5 - нагрудник
     * 6 - поножи
     * 7 - ботинки
     * @param int $type
     * @return Enchantment
     */
    public function getRandomEnchant(int $type) : Enchantment{
        switch($type){
            case 0:
                $enchants = [
                    VanillaEnchantments::FIRE_ASPECT(),
                    VanillaEnchantments::SHARPNESS(),
                    VanillaEnchantments::KNOCKBACK()];
                return $enchants[array_rand($enchants)];
            case 1:
                $enchants = [
                    VanillaEnchantments::SILK_TOUCH(),
                    VanillaEnchantments::EFFICIENCY()];
                return $enchants[array_rand($enchants)];
            case 2:
                $enchants = [
                    VanillaEnchantments::EFFICIENCY(),
                    VanillaEnchantments::SILK_TOUCH()];
                return $enchants[array_rand($enchants)];
            case 3:
                $enchants = [
                    VanillaEnchantments::EFFICIENCY(),
                    VanillaEnchantments::SHARPNESS(),
                    VanillaEnchantments::KNOCKBACK()];
                return $enchants[array_rand($enchants)];
            case 4:
            case 5:
            case 6:
            case 7:
            $enchants = [
                VanillaEnchantments::PROTECTION(),
                VanillaEnchantments::FIRE_PROTECTION(),
                VanillaEnchantments::BLAST_PROTECTION()];
            return $enchants[array_rand($enchants)];
        }
        return VanillaEnchantments::MENDING();
    }

}