<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class SubjectEnum extends Enum
{
    const MISSION_STATUS = [
        'YES' => 1,
        'NO' => 2,
    ];
    
    const QUIZ_STATUS = [
        'YES' => 1,
        'NO' => 2,
    ];
}
