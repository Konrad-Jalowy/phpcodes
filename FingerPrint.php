<?php
class FingerPrint {
    public static function fingerprint(){
        return md5(implode("|", [$_SERVER['HTTP_USER_AGENT'], $_SERVER['REMOTE_ADDR'] ]));
    }
}