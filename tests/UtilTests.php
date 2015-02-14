<?php

namespace League\Flysystem;

class UtilTests extends \PHPUnit_Framework_TestCase
{
    public function testEmulateDirectories()
    {
        $input = array(
            array('dirname' => '', 'filename' => 'dummy'),
            array('dirname' => 'something', 'filename' => 'dummy'),
            array('dirname' => 'something', 'path' => 'something/dirname', 'type' => 'dir'),
        );
        $output = Util::emulateDirectories($input);
        $this->assertCount(4, $output);
    }

    public function testContentSize()
    {
        $this->assertEquals(5, Util::contentSize('12345'));
        $this->assertEquals(3, Util::contentSize('135'));
    }

    public function mapProvider()
    {
        return array(
            array(array('from.this' => 'value'), array('from.this' => 'to.this', 'other' => 'other'), array('to.this' => 'value')),
            array(array('from.this' => 'value', 'no.mapping' => 'lost'), array('from.this' => 'to.this'), array('to.this' => 'value')),
        );
    }

    /**
     * @dataProvider  mapProvider
     */
    public function testMap($from, $map, $expected)
    {
        $result = Util::map($from, $map);
        $this->assertEquals($expected, $result);
    }

    public function dirnameProvider()
    {
        return array(
            array('filename.txt', ''),
            array('dirname/filename.txt', 'dirname'),
            array('dirname/subdir', 'dirname'),
        );
    }

    /**
     * @dataProvider  dirnameProvider
     */
    public function testDirname($input, $expected)
    {
        $result = Util::dirname($input);
        $this->assertEquals($expected, $result);
    }

    public function testEnsureConfig()
    {
        $this->assertInstanceOf('League\Flysystem\Config', Util::ensureConfig(array()));
        $this->assertInstanceOf('League\Flysystem\Config', Util::ensureConfig(null));
        $this->assertInstanceOf('League\Flysystem\Config', Util::ensureConfig(new Config()));
    }

    /**
     * @expectedException  LogicException
     */
    public function testInvalidValueEnsureConfig()
    {
        Util::ensureConfig(false);
    }

    public function invalidPathProvider()
    {
        return array(
            array('something/../../../hehe'),
            array('/something/../../..'),
            array('..'),
        );
    }

    /**
     * @expectedException  LogicException
     * @dataProvider       invalidPathProvider
     */
    public function testOutsideRootPath($path)
    {
        Util::normalizePath('something/../../../hehe');
    }

    public function pathProvider()
    {
        return array(
            array('/dirname/', 'dirname'),
            array('dirname/..', ''),
            array('./dir/../././', ''),
            array('00004869/files/other/10-75..stl', '00004869/files/other/10-75..stl'),
            array('/dirname//subdir///subsubdir', 'dirname/subdir/subsubdir'),
            array('\dirname\\\\subdir\\\\\\subsubdir', 'dirname\subdir\subsubdir'),
            array('\\\\some\shared\\\\drive', 'some\shared\drive'),
            array('C:\dirname\\\\subdir\\\\\\subsubdir', 'C:\dirname\subdir\subsubdir'),
            array('C:\\\\dirname\subdir\\\\subsubdir', 'C:\dirname\subdir\subsubdir'),
        );
    }

    /**
     * @dataProvider  pathProvider
     */
    public function testNormalizePath($input, $expected)
    {
        $result = Util::normalizePath($input);
        $this->assertEquals($expected, $result);
    }

    public function pathAndContentProvider()
    {
        return array(
            array('/some/file.css', 'body { background: #000; } ', 'text/css'),
            array('/some/file.txt', 'body { background: #000; } ', 'text/plain'),
            array('/1x1', base64_decode('R0lGODlhAQABAIAAAAUEBAAAACwAAAAAAQABAAACAkQBADs='), 'image/gif'),
        );
    }

    /**
     * @dataProvider  pathAndContentProvider
     */
    public function testGuessMimeType($path, $content, $expected)
    {
        $mimeType = Util::guessMimeType($path, $content);
        $this->assertEquals($expected, $mimeType);
    }

    public function testStreamSize()
    {
        $stream = tmpfile();
        fwrite($stream, 'aaa');
        $size = Util::getStreamSize($stream);
        $this->assertEquals(3, $size);
        fclose($stream);
    }

    public function testRewindStream()
    {
        $stream = tmpfile();
        fwrite($stream, 'something');
        $this->assertNotEquals(0, ftell($stream));
        Util::rewindStream($stream);
        $this->assertEquals(0, ftell($stream));
        fclose($stream);
    }

    public function testNormalizePrefix()
    {
        $this->assertEquals('test/', Util::normalizePrefix('test', '/'));
        $this->assertEquals('test/', Util::normalizePrefix('test/', '/'));
    }
}
