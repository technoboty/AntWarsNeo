<?php

namespace TechnoBoty\AntWarsNeo;

use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\NoteInstrument;
use pocketmine\world\sound\NoteSound;
use pocketmine\world\sound\XpCollectSound;
use ReflectionException;
use ReflectionProperty;
use TechnoBoty\AntWarsNeo\Arenas\Arena;
use TechnoBoty\AntWarsNeo\Arenas\ArenaManager;
use TechnoBoty\AntWarsNeo\Forms\InGameForms;
use TechnoBoty\AntWarsNeo\GameSession\BaseGameSession;
use TechnoBoty\AntWarsNeo\SettingsArenas\SquadSettings;
use TechnoBoty\AntWarsNeo\WorldGenerator\VoidGenerator;

class EventsListener implements Listener{

    use SingletonTrait;

    public function __construct(){
        self::setInstance($this);
    }
    public function onPlace(BlockPlaceEvent $event){
        $arena = ArenaManager::getInstance()->getArenaByPlayer($event->getPlayer());
        if($arena != NULL && $event->getPlayer()->getGamemode()->equals(GameMode::CREATIVE())) {
            $p = $event->getBlockAgainst()->getPosition();
            Server::getInstance()->broadcastMessage($p->getFloorX() . " | " . $p->getFloorY() . " | " . $p->getFloorZ());
        } elseif($arena != NULL && $arena?->getStage() == Arena::WAIT_STAGE){
            $event->cancel();
        }
    }

    /**
     * @throws ReflectionException
     */
    public function onBreak(BlockBreakEvent $event){
        $world = $event->getBlock()->getPosition()->getWorld();
        if(Server::getInstance()->getWorldManager()->getDefaultWorld()->getFolderName() == $world->getFolderName()){$event->cancel();return;}
        $arena = ArenaManager::getInstance()->getArenaByPlayer($event->getPlayer());
        if($arena != null && $event->getPlayer()->getGamemode()->equals(GameMode::CREATIVE())){return;}
        if($arena != NULL && $arena?->getStage() == Arena::WAIT_STAGE){$event->cancel();return;}
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
    public function onInteract(PlayerInteractEvent $event){
        $world = Server::getInstance()->getWorldManager()->getDefaultWorld();
        $world2 = $event->getPlayer()->getWorld();
        $arena = ArenaManager::getInstance()->getArenaByPlayer($event->getPlayer());
        if($event->getAction() != PlayerInteractEvent::RIGHT_CLICK_BLOCK && $arena != NULL && $arena?->getStage() == Arena::WAIT_STAGE){$event->cancel();return;}
        if( $world->getFolderName() == $world2->getFolderName()){$event->cancel();return;}
        if($event->getBlock()->getIdInfo()->getBlockTypeId() != VanillaBlocks::ENCHANTING_TABLE()->getIdInfo()->getBlockTypeId()){return;}
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
                $event->getPlayer()->getInventory()->remove(VanillaItems::LAPIS_LAZULI()->setCount(1));
                $event->getPlayer()->getWorld()->addSound($event->getPlayer()->getPosition(),new XpCollectSound(),[$event->getPlayer()]);
                $event->getPlayer()->sendMessage(TextFormat::GOLD."Успешно зачаровано предмет в руке!");
            } else {
                $event->getPlayer()->getWorld()->addSound($event->getPlayer()->getPosition(),new NoteSound(NoteInstrument::BASS_DRUM(),3),[$event->getPlayer()]);
                $event->getPlayer()->sendMessage(TextFormat::RED."Недостаточно опыта!");
            }
        } else {
            $event->getPlayer()->getWorld()->addSound($event->getPlayer()->getPosition(),new NoteSound(NoteInstrument::BASS_DRUM(),3),[$event->getPlayer()]);
            $event->getPlayer()->sendMessage(TextFormat::RED."Недостаточно лазурита!");
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
    public function onUse(PlayerItemUseEvent $event){
        if($event->getItem()->getTypeId() == VanillaBlocks::WOOL()->asItem()->getTypeId()){
            InGameForms::getInstance()->onSelectTeam($event->getPlayer());
        }elseif($event->getItem()->getTypeId() == VanillaBlocks::REDSTONE()->asItem()->getTypeId()){
            $arena = ArenaManager::getInstance()->getArenaByPlayer($event->getPlayer());
            $arena?->quit($event->getPlayer());
            $this->equip($event->getPlayer());
        }elseif($event->getItem()->getTypeId() == VanillaItems::EMERALD()->getTypeId()){
            $event->getPlayer()->sendMessage(TextFormat::GREEN."Выполняеться поиск игры...");
            ArenaManager::getInstance()->getArena(new SquadSettings())->join($event->getPlayer());
        }
    }
    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $this->equip($player);
        Main::getInstance()->sendDefaultScoreBoard($player);
    }
    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        $arena = ArenaManager::getInstance()->getArenaByPlayer($player);
        $arena?->quit($player);
        $arena?->getTeamGroup()->quitTeam($player);
    }
    public function onTransaction(InventoryTransactionEvent $event){
        $player = $event->getTransaction()->getSource();
        $arena = ArenaManager::getInstance()->getArenaByPlayer($player);
        if($player->getWorld()->getFolderName() == Server::getInstance()->getWorldManager()->getDefaultWorld()->getFolderName()){
            $event->cancel();
        }elseif($arena != NULL && $arena->getStage() == Arena::WAIT_STAGE){
            $event->cancel();
        }
    }
    public function onDrop(PlayerDropItemEvent $event){
        $player = $event->getPlayer();
        $arena = ArenaManager::getInstance()->getArenaByPlayer($player);
        if($player->getWorld()->getFolderName() == Server::getInstance()->getWorldManager()->getDefaultWorld()->getFolderName()){
            $event->cancel();
        }elseif($arena != NULL && $arena->getStage() == Arena::WAIT_STAGE){
            $event->cancel();
        }
    }
    public function onDamage(EntityDamageEvent $event){
        if($event instanceof EntityDamageByEntityEvent) {
            if($event->getEntity() instanceof Player) {
                $arena = ArenaManager::getInstance()->getArenaByPlayer($event->getEntity());
                if($event->getEntity()->getWorld()->getFolderName() == Server::getInstance()->getWorldManager()->getDefaultWorld()->getFolderName()) {
                    $event->cancel();
                }elseif($arena != NULL && $arena->getStage() == Arena::WAIT_STAGE){
                    $event->cancel();
                }
            }
        }
    }
    public function equip(Player $player) : void{
        $default = Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn();
        $player->setGamemode(GameMode::ADVENTURE());
        $player->teleport($default);
        $inv = $player->getInventory();
        $inv->clearAll();
        $games = VanillaItems::EMERALD()->setCustomName(TextFormat::GOLD."Выбрать игру")->setLore(["SummerWorld"]);
        $friends = VanillaItems::BOOK()->setCustomName(TextFormat::RED."Друзья/Пати")->setLore(["SummerWorld"]);
        $donate = VanillaItems::NETHERITE_SCRAP()->setCustomName(TextFormat::DARK_PURPLE."Донат")->setLore(["SummerWorld"]);
        $show = VanillaItems::DYE()->setColor(DyeColor::LIME())->setCustomName(TextFormat::DARK_AQUA."Показать/Скрыть игроков")->setLore(["SummerWorld"]);
        $inv->setItem(1,$games);
        $inv->setItem(3,$friends);
        $inv->setItem(5,$show);
        $inv->setItem(7,$donate);
    }
}