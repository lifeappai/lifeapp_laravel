<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static ADMIN()
 * @method static static SCHOOL()
 * @method static static STUDENT()
 * @method static static MENTOR()
 */
final class UserType extends Enum
{
    const Admin =   1;
    const School =   2;
    const Student = 3;
    const Mentor = 4;
    const Teacher = 5;
}
