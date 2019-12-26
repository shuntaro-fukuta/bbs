<?php

class Storage_Premember extends Storage_Base
{
    protected $table_name = 'premember';

    // TODO: 変数、メソッド名
    public function isExpired(string $datetime)
    {
        $registration_timestamp = strtotime($datetime);
        if ($registration_timestamp === false) {
            throw new LogicException("Invalid date '{$datetime}' passed.");
        }

        if ((time() - $registration_timestamp) > 60 * 60  * 24) {
            return true;
        }

        return false;
    }
}