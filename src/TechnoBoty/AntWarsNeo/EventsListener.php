<?php

namespace TechnoBoty\AntWarsNeo;

use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\Experiments;
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

    private Experiments $experiments;

    public function __construct(){
        self::setInstance($this);
        $this->experiments = new Experiments(
            [
                "data_driven_items" => true,
                "upcoming_creator_features" => true,
                "experimental_molang_features" => true
            ], true);
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
                    if(mt_rand(1,5) == 2){
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
                case VanillaBlocks::ANCIENT_DEBRIS()->getIdInfo()->getBlockTypeId():
                    $event->setDrops([VanillaItems::NETHERITE_SCRAP()->setCount(1)]);
            }
        }
    }
    public function onExplode(EntityExplodeEvent $event){
        $event->setYield(0.0);
    }
    public function onCraft(CraftItemEvent $event){
        $player = $event->getPlayer();
        $arena = ArenaManager::getInstance()->getArenaByPlayer($player);
        if($arena == null || $arena?->getStage() == Arena::WAIT_STAGE || $arena?->getStage() == Arena::START_STAGE){
            $event->cancel();
        }
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
        $player->getEffects()->clear();
        $player->getXpManager()->setXpAndProgress(0,0.0);
        $event->setJoinMessage("");
    }
    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        $arena = ArenaManager::getInstance()->getArenaByPlayer($player);
        $arena?->quit($player);
        $event->setQuitMessage("");
    }
    public function onTransaction(InventoryTransactionEvent $event){
        $player = $event->getTransaction()->getSource();
        $arena = ArenaManager::getInstance()->getArenaByPlayer($player);
        if($player->getWorld()->getFolderName() == Server::getInstance()->getWorldManager()->getDefaultWorld()->getFolderName()){
            $event->cancel();
        }elseif($arena != NULL && $arena->getStage() == Arena::WAIT_STAGE || $arena != NULL && $arena->getStage() == Arena::START_STAGE){
            $event->cancel();
        }
    }
    public function onDrop(PlayerDropItemEvent $event){
        $player = $event->getPlayer();
        $arena = ArenaManager::getInstance()->getArenaByPlayer($player);
        if($player->getWorld()->getFolderName() == Server::getInstance()->getWorldManager()->getDefaultWorld()->getFolderName()){
            $event->cancel();
        }elseif($arena != NULL && $arena->getStage() == Arena::WAIT_STAGE || $arena != NULL && $arena->getStage() == Arena::START_STAGE){
            $event->cancel();
        }
    }
    public function onDropLeaves(LeavesDecayEvent $event){
        $world = $event->getBlock()->getPosition()->getWorld();
        ($gen = new ReflectionProperty($world,"generator"))->setAccessible(TRUE);
        if($gen->getValue($world) == VoidGenerator::class){$event->cancel();}
    }
    public function onDamage(EntityDamageEvent $event){
        if($event instanceof EntityDamageByEntityEvent) {
            if($event->getEntity() instanceof Player) {
                $arena = ArenaManager::getInstance()->getArenaByPlayer($event->getEntity());
                if($event->getEntity()->getWorld()->getFolderName() == Server::getInstance()->getWorldManager()->getDefaultWorld()->getFolderName()) {
                    $event->cancel();
                }elseif($arena != NULL && $arena->getStage() == Arena::WAIT_STAGE || $arena != NULL && $arena->getStage() == Arena::START_STAGE){
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
        $player->getArmorInventory()->clearAll();
        $games = VanillaItems::EMERALD()->setCustomName(TextFormat::GOLD."Выбрать игру")->setLore(["SummerWorld"]);
        $friends = VanillaItems::BOOK()->setCustomName(TextFormat::RED."Друзья/Пати")->setLore(["SummerWorld"]);
        $donate = VanillaItems::NETHERITE_SCRAP()->setCustomName(TextFormat::DARK_PURPLE."Донат")->setLore(["SummerWorld"]);
        $show = VanillaItems::DYE()->setColor(DyeColor::LIME())->setCustomName(TextFormat::DARK_AQUA."Показать/Скрыть игроков")->setLore(["SummerWorld"]);
        $inv->setItem(1,$games);
        $inv->setItem(3,$friends);
        $inv->setItem(5,$show);
        $inv->setItem(7,$donate);
    }
    public function onDataPacketSend(DataPacketSendEvent $event) : void{
        foreach($event->getPackets() as $packet){
            if($packet instanceof StartGamePacket){
                $packet->levelSettings->experiments = $this->experiments;
            }
            if($packet instanceof ResourcePackStackPacket){
                $packet->experiments = $this->experiments;
            }
        }
    }
}