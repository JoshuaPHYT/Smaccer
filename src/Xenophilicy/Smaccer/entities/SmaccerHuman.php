<?php
declare(strict_types=1);

namespace Xenophilicy\Smaccer\entities;

use pocketmine\entity\Human;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\SetActorDataPacket as SetEntityDataPacket;
use pocketmine\Player;
use Xenophilicy\Smaccer\SmaccerTrait;

/**
 * Class SmaccerHuman
 * @package Xenophilicy\Smaccer\entities
 */
class SmaccerHuman extends Human {
    use SmaccerTrait;
    
    /**
     * SmaccerHuman constructor.
     * @param Level $level
     * @param CompoundTag $nbt
     */
    public function __construct(Level $level, CompoundTag $nbt){
        parent::__construct($level, $nbt);
        $this->prepareMetadata();
    }
    
    public function saveNBT(): void{
        parent::saveNBT();
        $this->saveSmaccerNbt();
    }
    
    public function entityBaseTick(int $tickDiff = 1): bool{
        return $this->update();
    }
    
    public function sendNameTag(Player $player): void{
        $pk = new SetEntityDataPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->metadata = [self::DATA_NAMETAG => [self::DATA_TYPE_STRING, $this->getDisplayName($player)]];
        $player->dataPacket($pk);
    }
    
    protected function sendSpawnPacket(Player $player): void{
        parent::sendSpawnPacket($player);
        if(($menuName = $this->namedtag->getString("MenuName", "", true)) !== ""){
            $player->getServer()->updatePlayerListData($this->getUniqueId(), $this->getId(), $menuName, $this->skin, "", [$player]);
        }
    }
}
