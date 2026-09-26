<?php

namespace App\Utils;

class InternalCodeUtil
{
    const CODE_TYPE_OWNED_BOOK = '00';
    const CODE_TYPE_SHELF = '01';
    const CODE_TYPE_BOOK_CASE = '02';
    const CODE_TYPE_ROOM = '03';
    const CODE_TYPE_USER = '04';

    public static function calculateBcdCd(string|int $code): int {
        $code = str_split(strval($code));
        $sum = 0;
        foreach ($code as $c) {
            if (!is_numeric($c))
                throw new \InvalidArgumentException('Given code contains non numeric characters');

            $c = intval($c);
            $sum += $c;
            $sum = $sum % 10;
        }

        return $sum % 10;
    }

    public static function validateCodeType(string $codeType): bool {
        return in_array($codeType, [
            self::CODE_TYPE_OWNED_BOOK,
            self::CODE_TYPE_SHELF,
            self::CODE_TYPE_BOOK_CASE,
            self::CODE_TYPE_ROOM,
            self::CODE_TYPE_USER,
        ]);
    }

    public static function generateCode(string $codeType, string|int $id): string {
        if (!self::validateCodeType($codeType)) {
            throw new \InvalidArgumentException(sprintf('Invalid code type: %s', $codeType));
        }
        $id_str = strval($id);
        $bcd = self::calculateBcdCd($id_str);
        return $codeType . $id_str . $bcd;
    }
}
