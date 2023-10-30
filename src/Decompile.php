<?php

/**
 
 */

namespace Irelance\Mozjs34;
include 'Xdr/Common.php';
include 'Xdr/Script.php';
include 'Xdr/Atom.php';
include 'Xdr/ObjectXdr.php';
include 'Xdr/Scope.php';
include 'Xdr/Operation.php';
use Irelance\Mozjs34\Helper\Stack;

class Decompile
{
    use Xdr\Common;
    use Xdr\Script;
    use Xdr\Atom;
    use Xdr\ObjectXdr;
    use Xdr\Scope;
    use Xdr\Operation;

    public $isDebug = false;
    private $fp;
    protected $parseIndex = 0;

    protected $buildId = '';
    protected $contexts = [];

    public $bytecodes = [];
    public $bytecodeLength = 0;

    public function __construct($filename)
    {
        $this->fp = fopen($filename, 'rb');
        $this->init();
    }

    public function __destruct()
    {
        fclose($this->fp);
        unset($this->bytecodes);
    }

    public function init()
    {
        $i = 0;
        while (!feof($this->fp)) {
            $c = fgetc($this->fp);
            $this->bytecodes[$i] = ord($c);
            $i++;
        }
        $this->bytecodeLength = count($this->bytecodes);
    }

    protected function parserVersion()
    {
        $this->parseIndex = 0;
        $bytecodeVer = $this->todec();
        return $bytecodeVer;
    }

    public function run()
    {
        $this->parserVersion();
        $this->XDRScript();
    }

    public function runResult()
    {
        echo '----------------ByteCode---------------', CLIENT_EOL;
        echo 'file size :', $this->bytecodeLength, CLIENT_EOL;
        echo 'parse size :', 1 + $this->parseIndex, CLIENT_EOL;
        echo '---------------------------------------', CLIENT_EOL;
    }

    public function getContexts()
    {
        return $this->contexts;
    }

    protected $localVariable = [];

    public function setLocalVariable($index, $value)
    {
        $this->localVariable[$index] = $value;
    }

    public function getLocalVariable($index)
    {
        if (!isset($this->localVariable[$index])) {
            return new Stack(['parserIndex' => 0, 'type' => 'undefined', 'value' => 'undefined']);
        }
        return $this->localVariable[$index];
    }

    protected $aliasedVariable = [];

    public function setAliasedVariable($hops, $slot, $value)
    {
        if (!isset($this->aliasedVariable[$hops])) {
            $this->aliasedVariable[$hops] = [];
        }
        $this->aliasedVariable[$hops][$slot] = $value;
    }

    public function getAliasedVariable($hops, $slot)
    {
        if (!isset($this->aliasedVariable[$hops][$slot])) {
            return new Stack(['parserIndex' => 0, 'type' => 'undefined', 'value' => 'undefined']);
        }
        return $this->aliasedVariable[$hops][$slot];
    }
}
