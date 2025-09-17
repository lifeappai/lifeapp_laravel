<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class FileTypeEnum extends Enum
{
    const DISTRICT =   1;
    const STUDENT =   2;
}
