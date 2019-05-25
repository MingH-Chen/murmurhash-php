<?php

/**
 * MurmurHash算法 工具类
 * 目前只实现hash32
 *
 * @author: echo
 * @date:2019.05.25
 */
class MurmurHashUtils
{


    /**
     * 生成  32 bit hash
     * @param string $key
     * @param int $seed 种子值
     * @return float|int|string
     */
    public static function hash32(string $key, int $seed = 0)
    {

        $key = array_values(unpack('C*', $key));
        $kLen = count($key);
        $remainder = $kLen & 3;// key.length % 4
        $bytes = $kLen - $remainder;
        $h1 = $seed < 0 ? -$seed : $seed;

        $c1 = 0xcc9e2d51;
        $c2 = 0x1b873593;

        $i = 0;
        while ($i < $bytes) {
            $k1 =
                (($key[$i] & 0xff)) |
                (($key[++$i] & 0xff) << 8) |
                (($key[++$i] & 0xff) << 16) |
                (($key[++$i] & 0xff) << 24);
            ++$i;

            $k1 = (((($k1 & 0xffff) * $c1) + ((((self::uint32Right($k1, 16)) * $c1) & 0xffff) << 16))) & 0xffffffff;
            $k1 = ($k1 << 15) | (self::uint32Right($k1, 17));
            $k1 = (((($k1 & 0xffff) * $c2) + ((((self::uint32Right($k1, 16)) * $c2) & 0xffff) << 16))) & 0xffffffff;

            $h1 ^= $k1;
            $h1 = ($h1 << 13) | (self::uint32Right($h1, 19));
            $h1b = (((($h1 & 0xffff) * 5) + ((((self::uint32Right($h1, 16)) * 5) & 0xffff) << 16))) & 0xffffffff;
            $h1 = ((($h1b & 0xffff) + 0x6b64) + ((((self::uint32Right($h1b, 16)) + 0xe654) & 0xffff) << 16));
        }

        $k1 = 0;

        switch ($remainder) {
            case 3:
                $k1 ^= ($key[$i + 2] & 0xff) << 16;
            case 2:
                $k1 ^= ($key[$i + 1] & 0xff) << 8;
            case 1:
                $k1 ^= ($key[$i] & 0xff);

                $k1 = ((($k1 & 0xffff) * $c1) + ((((self::uint32Right($k1, 16)) * $c1) & 0xffff) << 16)) & 0xffffffff;
                $k1 = ($k1 << 15) | (self::uint32Right($k1, 17));
                $k1 = ((($k1 & 0xffff) * $c2) + ((((self::uint32Right($k1, 16)) * $c2) & 0xffff) << 16)) & 0xffffffff;
                $h1 ^= $k1;
        }

        $h1 ^= $kLen;

        $h1 ^= self::uint32Right($h1, 16);
        $h1 = ((($h1 & 0xffff) * 0x85ebca6b) + ((((self::uint32Right($h1, 16)) * 0x85ebca6b) & 0xffff) << 16)) & 0xffffffff;
        $h1 ^= self::uint32Right($h1, 13);
        $h1 = (((($h1 & 0xffff) * 0xc2b2ae35) + ((((self::uint32Right($h1, 16)) * 0xc2b2ae35) & 0xffff) << 16))) & 0xffffffff;
        $h1 ^= self::uint32Right($h1, 16);

        return self::uint32Right($h1, 0);
    }

    /**
     * 无符号右移  >>>
     * @param $v
     * @param $n 移位
     * @return float|int|string
     */
    private static function uint32Right($v, $n)
    {
        if ($n === 0) {
            if ((0 > $v) || ($v > 4294967295)) {
                $v &= 4294967295;
                if (0 > $v) {
                    $v = sprintf('%u', $v);
                }
            }
            return $v;
        }
        $c = 2147483647 >> ($n - 1);
        return $c & ($v >> $n);
    }
}