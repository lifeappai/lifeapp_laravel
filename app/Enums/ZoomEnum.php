<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ZoomEnum extends Enum
{
    /*
        1 - An instant meeting.
        2 - A scheduled meeting.
        3 - A recurring meeting with no fixed time.
        8 - A recurring meeting with fixed time.
    */
    const MEETING_TYPE_INSTANT = 1;
    const MEETING_TYPE_SCHEDULE = 2;
    const MEETING_TYPE_RECURRING = 3;
    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;
}