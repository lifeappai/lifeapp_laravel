<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class GameEnrollmentTypeEnum extends Enum
{
    const TYPE = [
        "LIFE_LAB_DEMO_MODELS" => 1,
        "JIGYASA_SELF_DIY_ACTVITES" => 2,
        "PRAGYA_DIY_ACTIVITES_WITH_LIFE_LAB_KITS" => 3,
        "LIFE_LAB_ACTIVITIES_LESSION_PLANS" => 4,
        "JIGYASA" => 5,
        "PRAGYA" => 6,
    ];

    const NOT_REQUESTED = 0;
    const ACCEPTED = 1;
    const REQUESTED = 2;
}
