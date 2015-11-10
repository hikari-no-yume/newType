<?php declare(strict_types=1);

namespace ajf\newType;

const EXTREMELY_TRUE = true;

class Test extends \PHPUnit_Framework_TestCase
{
    public function testCorrectUsage() {
        newType(FilePath::class, 'string');

        $path = new FilePath('foobar');
        $this->assertEquals('foobar', $path->unbox());

        newType(Age::class, 'int');

        $age = new Age(16);
        $this->assertEquals(16, $age->unbox());

        newType(Opacity::class, 'float');

        $opacity = new Opacity(0.75);
        $this->assertEquals(0.75, $opacity->unbox());

        newType(UltraBoolean::class, 'bool');

        $ultraBool = new UltraBoolean(EXTREMELY_TRUE);
        $this->assertEquals(EXTREMELY_TRUE, $ultraBool->unbox());

        newType(MegaObject::class, \StdClass::class);

        $inferiorObj = (object)[];
        $megaObj = new MegaObject($inferiorObj);
        $this->assertEquals($inferiorObj, $megaObj->unbox());
        
        // note explicit leading slash
        newType(SuperMegaObject::class, '\StdClass');

        $superMegaObj = new SuperMegaObject($inferiorObj);
        $this->assertEquals($inferiorObj, $superMegaObj->unbox());

        newType('\UltraMegaObject', \StdClass::class);

        $ultraMegaObj = new \UltraMegaObject($inferiorObj);
        $this->assertEquals($inferiorObj, $ultraMegaObj->unbox());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBadNewTypeName() {
        newType(' l\x00iterally, ga\vrbag\ne', 'string');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBadWrappedTypeName() {
        newType('TrashCan', ' l\x00iterally, ga\vrbag\ne');
    }
}
