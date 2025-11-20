<?php

namespace App\Actions\Invite;

class InviteCodeGenerator
{
    static function generate(string|int $code): string
    {
        return str_pad("$code", 8, "0", STR_PAD_LEFT);
    }
}
