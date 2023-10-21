<?php

namespace Ternaryop\PhotoshelfUtil\String;

class StringUtil
{
    const UNICODE_MAP = array(
        "\u{0096}" => "-",
        "\u{00AB}" => "\"",
        "\u{00AD}" => "-",
        "\u{00B4}" => "'",
        "\u{00BB}" => "\"",
        "\u{00F7}" => "/",
        "\u{01C0}" => "|",
        "\u{01C3}" => "!",
        "\u{02B9}" => "'",
        "\u{02BA}" => "\"",
        "\u{02BC}" => "'",
        "\u{02C4}" => "^",
        "\u{02C6}" => "^",
        "\u{02C8}" => "'",
        "\u{02CB}" => "`",
        "\u{02CD}" => "_",
        "\u{02DC}" => "~",
        "\u{0300}" => "`",
        "\u{0301}" => "'",
        "\u{0302}" => "^",
        "\u{0303}" => "~",
        "\u{030B}" => "\"",
        "\u{030E}" => "\"",
        "\u{0331}" => "_",
        "\u{0332}" => "_",
        "\u{0338}" => "/",
        "\u{0430}" => "a",
        "\u{0432}" => "b",
        "\u{0435}" => "e",
        "\u{043c}" => "m",
        "\u{0440}" => "p",
        "\u{0442}" => "t",
        "\u{0445}" => "x",
        "\u{0455}" => "s",
        "\u{0456}" => "i",
        "\u{0589}" => ":",
        "\u{05C0}" => "|",
        "\u{05C3}" => ":",
        "\u{066A}" => "%",
        "\u{066D}" => "*",
        "\u{200B}" => "",
        "\u{2010}" => "-",
        "\u{2011}" => "-",
        "\u{2012}" => "-",
        "\u{2013}" => "-",
        "\u{2014}" => "-",
        "\u{2015}" => "--",
        "\u{2016}" => "||",
        "\u{2017}" => "_",
        "\u{2018}" => "'",
        "\u{2019}" => "'",
        "\u{201A}" => ",",
        "\u{201B}" => "'",
        "\u{201C}" => "\"",
        "\u{201D}" => "\"",
        "\u{201E}" => "\"",
        "\u{201F}" => "\"",
        "\u{2032}" => "'",
        "\u{2033}" => "\"",
        "\u{2034}" => "'''",
        "\u{2035}" => "`",
        "\u{2036}" => "\"",
        "\u{2037}" => "'''",
        "\u{2038}" => "^",
        "\u{2039}" => "<",
        "\u{203A}" => ">",
        "\u{203D}" => "?",
        "\u{2044}" => "/",
        "\u{204E}" => "*",
        "\u{2052}" => "%",
        "\u{2053}" => "~",
        "\u{2060}" => "",
        "\u{20E5}" => "\\",
        "\u{2212}" => "-",
        "\u{2215}" => "/",
        "\u{2216}" => "\\",
        "\u{2217}" => "*",
        "\u{2223}" => "|",
        "\u{2236}" => ":",
        "\u{223C}" => "~",
        "\u{2264}" => "<=",
        "\u{2265}" => ">=",
        "\u{2266}" => "<=",
        "\u{2267}" => ">=",
        "\u{2303}" => "^",
        "\u{2329}" => "<",
        "\u{232A}" => ">",
        "\u{266F}" => "#",
        "\u{2731}" => "*",
        "\u{2758}" => "|",
        "\u{2762}" => "!",
        "\u{27E6}" => "[",
        "\u{27E8}" => "<",
        "\u{27E9}" => ">",
        "\u{2983}" => "{",
        "\u{2984}" => "}",
        "\u{3003}" => "\"",
        "\u{3008}" => "<",
        "\u{3009}" => ">",
        "\u{301B}" => "]",
        "\u{301C}" => "~",
        "\u{301D}" => "\"",
        "\u{301E}" => "\"",
        "\u{FEFF}" => "",
        "\u{00E0}" => "a'"
    );

    public static function startsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * @param string $str
     * @return array|string|null
     */
    public static function normalizeWhitespaces(string $str): array|string|null
    {
        // use the unicode flag to avoid problem when json is encoded
        return preg_replace("/\s{2,}/u", " ", $str);
    }

    public static function replaceUnicodeWithClosestAscii(string $str): string
    {
        $result = '';

        for ($i = 0; $i < strlen($str); $i++) {
            $ch = mb_substr($str, $i, 1, "UTF-8");
            if (isset(self::UNICODE_MAP[$ch])) {
                $result .= self::UNICODE_MAP[$ch];
            } else {
                $result .= $ch;
            }
        }
        return $result;
    }

    public static function capitalizeAll(string $str): string
    {
        $sb = '';
        $upcase = true;

        for ($i = 0; $i < strlen($str); $i++) {
            $ch = $str[$i];
            if ($ch == '_' || $ch == '-' || $ch == ' ' || $ch == "\t") {
                $upcase = true;
            } else {
                if ($upcase) {
                    $ch = strtoupper($ch);
                    $upcase = false;
                } else {
                    $ch = strtolower($ch);
                }
            }
            $sb .= $ch;
        }

        return $sb;
    }

    public static function stripAccents(string $string): string
    {
        if (!preg_match('/[\x80-\xff]/', $string))
            return $string;

        $chars = array(
            // Decompositions for Latin-1 Supplement
            chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
            chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
            chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
            chr(195) . chr(135) => 'C', chr(195) . chr(136) => 'E',
            chr(195) . chr(137) => 'E', chr(195) . chr(138) => 'E',
            chr(195) . chr(139) => 'E', chr(195) . chr(140) => 'I',
            chr(195) . chr(141) => 'I', chr(195) . chr(142) => 'I',
            chr(195) . chr(143) => 'I', chr(195) . chr(145) => 'N',
            chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
            chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
            chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
            chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
            chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
            chr(195) . chr(159) => 's', chr(195) . chr(160) => 'a',
            chr(195) . chr(161) => 'a', chr(195) . chr(162) => 'a',
            chr(195) . chr(163) => 'a', chr(195) . chr(164) => 'a',
            chr(195) . chr(165) => 'a', chr(195) . chr(167) => 'c',
            chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',
            chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',
            chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',
            chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',
            chr(195) . chr(177) => 'n', chr(195) . chr(178) => 'o',
            chr(195) . chr(179) => 'o', chr(195) . chr(180) => 'o',
            chr(195) . chr(181) => 'o', chr(195) . chr(182) => 'o',
            chr(195) . chr(182) => 'o', chr(195) . chr(185) => 'u',
            chr(195) . chr(186) => 'u', chr(195) . chr(187) => 'u',
            chr(195) . chr(188) => 'u', chr(195) . chr(189) => 'y',
            chr(195) . chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',
            chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',
            chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',
            chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',
            chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',
            chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',
            chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',
            chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',
            chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
            chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',
            chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',
            chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',
            chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',
            chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',
            chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',
            chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',
            chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',
            chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',
            chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',
            chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',
            chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',
            chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',
            chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',
            chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',
            chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',
            chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',
            chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',
            chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',
            chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',
            chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',
            chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',
            chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',
            chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',
            chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',
            chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',
            chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',
            chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',
            chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',
            chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',
            chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',
            chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',
            chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
            chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',
            chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',
            chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',
            chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',
            chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',
            chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',
            chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
            chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',
            chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',
            chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',
            chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',
            chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',
            chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',
            chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',
            chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',
            chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',
            chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',
            chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',
            chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',
            chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',
            chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',
            chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's'
        );

        return strtr($string, $chars);
    }

    /**
     * @param string $input
     * @param int $multiplier
     * @param string|null $lastInput the last input appended
     * @return string
     */
    public static function repeat(string $input, int $multiplier, string $lastInput = null): string
    {
        if (is_null($lastInput)) {
            return str_repeat($input, $multiplier);
        }
        return str_repeat($input, $multiplier - 1) . $lastInput;
    }
}
