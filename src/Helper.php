<?php

namespace Yocto;

use YoHang88\LetterAvatar\LetterAvatar;

class Helper
{

    /**
     * @param Database $member Instance du membre
     * @param int $size Taille de l'image à retourner
     * @return string
     */
    public static function getMemberPicture($member, $size = 36)
    {
        $avatar = new LetterAvatar(strtolower($member->name), 'square', $size);
        return '<img src="' . $avatar . '" class="rounded-circle" alt="' . $member->name . '">';
    }

}