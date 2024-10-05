<?php

declare(strict_types=1);

namespace App\Enum;



enum CertAuthMode: int
{
    case CERT_AUTHNO_REGISTER = 1;
    case CERT_AUTHNO_FIND_ID = 2;
    case CERT_AUTHNO_FIND_PWD = 3;
    case CERT_AUTHNO_CHANGE_HP = 4;
}