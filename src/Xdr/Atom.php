<?php
/**

 */

namespace Irelance\Mozjs34\Xdr;

trait Atom
{
    protected function getLatin1Chars($length)
    {
        $end = $this->parseIndex + $length;
        $atom = '';
        for (; $this->parseIndex < $end; $this->parseIndex++) {
            $atom .= chr($this->bytecodes[$this->parseIndex]);
        }
        return $atom;
    }

    protected function getTwoByteChar()
    {
        $char = sprintf("\u%02X%02X", $this->bytecodes[$this->parseIndex + 1],$this->bytecodes[$this->parseIndex ]);
        // $char = '\u' . $this->dec2hex($this->bytecodes[$this->parseIndex + 1]) . $this->dec2hex($this->bytecodes[$this->parseIndex]);
        $this->parseIndex += 2;
        return $char;
    }

    protected function getTwoByteChars($length)
    {
        $atom = '';
        for ($i = 0; $i < $length; $i++) {
            $atom .= $this->getTwoByteChar();
        }
        return json_decode('"' . $atom . '"');
    }

    public function XDRAtom()
    {
        $lengthAndEncoding = $this->todec();
        $hasLatin1Chars = $lengthAndEncoding & 1;
        $length = $lengthAndEncoding >> 1;
        if ($hasLatin1Chars) {
            $atom = $this->getLatin1Chars($length);
        } else {
            $atom = $this->getTwoByteChars($length);
        }
        return $atom;
    }
}
