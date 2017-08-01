<?php
/**
 * User: Sam Washington
 * Date: 4/14/17
 * Time: 9:37 PM
 */

namespace Sm\Core\Container\Mini;


class MiniCacheTest extends \PHPUnit_Framework_TestCase {
    /** @var  \Sm\Core\Container\Mini\MiniCache $Keyed_MiniCache */
    protected static $Keyed_MiniCache;
    /** @var  \Sm\Core\Container\Mini\MiniCache $MiniCache */
    protected static $MiniCache;
    public function testCanBegin() {
        $MiniCache = MiniCache::begin();
        $this->assertInstanceOf(MiniCache::class, $MiniCache);
        $this->assertTrue($MiniCache->isCaching());
    }
    
    public function testCanStartCache() {
        static::$MiniCache->start();
        $this->assertTrue(static::$MiniCache->isCaching());
        static::$Keyed_MiniCache->start('test_key');
        $this->assertTrue(static::$Keyed_MiniCache->isCaching());
    }
    /**
     * @depends testCanStartCache
     */
    public function testCanTellIfkeyMatches() {
        $this->assertTrue(static::$Keyed_MiniCache->keyMatches('test_key'));
        
    }
    /**
     * @depends   testCanStartCache
     */
    public function testCanCacheItems() {
        $item       = 'test';
        $array_key  = [ 'test', 'one', new \stdClass(), [ 'another' ] ];
        $object_key = new \stdClass;
        static::$MiniCache->cache($array_key, $item);
        $this->assertEquals($item, static::$MiniCache->resolve($array_key));
    
        static::$MiniCache->register($object_key, $item);
        $this->assertEquals($item, static::$MiniCache->resolve($object_key));
        
        static::$Keyed_MiniCache->cache($array_key, $item);
        $this->assertEquals($item, static::$Keyed_MiniCache->resolve($array_key));
    
    }
    public function testCannotStopCacheWithoutProperKey() {
        $this->expectException(InvalidCacheKeyException::class);
        $this->assertTrue(static::$Keyed_MiniCache->end()->isCaching());
    }
    public function testCanStopCache() {
        $this->assertFalse(static::$MiniCache->end()->isCaching());
        $this->assertFalse(static::$Keyed_MiniCache->end('test_key')->isCaching());
    }
    public function testCannotStartCacheWithoutProperKey() {
        $this->expectException(InvalidCacheKeyException::class);
        $this->assertTrue(static::$Keyed_MiniCache->end()->isCaching());
    }
    /**
     * @depends testCanStopCache
     */
    public function testCannotCacheStoppedCache() {
        $this->assertFalse(static::$MiniCache->cache('item', 'value')->canResolve('item'));
    }
    public static function setUpBeforeClass() {
        static::$MiniCache       = new MiniCache;
        static::$Keyed_MiniCache = new MiniCache;
    }
}
