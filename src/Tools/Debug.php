<?php
/**
 * This file is part of Simps
 *
 * @link     https://github.com/simps/mqtt
 * @contact  Lu Fei <lufei@simps.io>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code
 */

declare(strict_types=1);

namespace Simps\MQTT\Tools;

class Debug
{
    private $encode;

    public function __construct(string $encode = '')
    {
        $this->encode = $encode;
    }

    public function setEncode(string $encode): self
    {
        $this->encode = $encode;

        return $this;
    }

    public function getEncode(): string
    {
        return $this->encode;
    }

    public function hexDump(): string
    {
        return $this->toHexDump($this->getEncode());
    }

    public function hexDumpAscii(): string
    {
        return $this->toHexDump($this->getEncode(), true);
    }

    public function printableText(): string
    {
        return $this->getEncode();
    }

    public function hexStream(): string
    {
        return bin2hex($this->getEncode());
    }

    private function toHexDump($contents, $hasAscii = false)
    {
        $address = $column = 0;
        $result = $hexDump = $asciiDump = '';

        $sprintf = '%08x    %-48s';

        if ($hasAscii) {
            $sprintf = '%08x    %-48s   %s';
        }

        foreach (str_split($contents) as $c) {
            $hexDump = $hexDump . sprintf('%02x ', ord($c));
            if ($hasAscii) {
                if (ord($c) > 31 && ord($c) < 128) {
                    $asciiDump = $asciiDump . $c;
                } else {
                    $asciiDump = $asciiDump . '.';
                }
            }
            $column++;
            if (($column % 16) == 0) {
                $line = sprintf($sprintf, $address, $hexDump, $asciiDump);
                $result .= $line . PHP_EOL;

                $asciiDump = '';
                $hexDump = '';
                $column = 0;
                $address += 16;
            }
        }

        if ($column > 0) {
            $line = sprintf($sprintf, $address, $hexDump, $asciiDump);
            $result .= $line;
        }

        return $result;
    }
}
