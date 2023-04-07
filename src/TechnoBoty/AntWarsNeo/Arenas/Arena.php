<?php

namespace TechnoBoty\AntWarsNeo\Arenas;

use TechnoBoty\AntWarsNeo\MapManager\Map;

class Arena{

    //Const
    private const MAX_PLAYERS = 16;
    private const I_STAGE = 1;
    private const II_STAGE = 2;
    private const III_STAGE = 3;
    private const IV_STAGE = 4;
    private const V_STAGE = 5;
    //

    private array $players = [];
    private int $stage;

    private Map $map;
}