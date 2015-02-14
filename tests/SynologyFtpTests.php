<?php

namespace League\Flysystem\Adapter;

use League\Flysystem\Config;

class SynologyFtpTests extends \PHPUnit_Framework_TestCase
{
    protected $options = array(
        'host' => 'example.org',
        'port' => 40,
        'ssl' => true,
        'timeout' => 35,
        'root' => '/somewhere',
        'permPublic' => 0777,
        'permPrivate' => 0000,
        'passive' => false,
        'username' => 'user',
        'password' => 'password',
    );

    public function testInstantiable()
    {
        if (!defined('FTP_BINARY')) {
            $this->markTestSkipped('The FTP_BINARY constant is not defined');
        }

        $adapter = new SynologyFtp($this->options);
        $listing = $adapter->listContents('', true);
        $this->assertInternalType('array', $listing);
        $this->assertFalse($adapter->has('syno.not.found'));
        $result = $adapter->getMimetype('something.txt');
        $this->assertEquals('text/plain', $result['mimetype']);
        $this->assertInternalType('array', $adapter->write('syno.unknowndir/file.txt', 'contents', new Config(array('visibility' => 'public'))));
        $this->assertInternalType('array', $adapter->getTimestamp('some/file.ext'));
    }

    /**
     * @depends testInstantiable
     */
    public function testRawlistFail()
    {
        $adapter = new SynologyFtp($this->options);
        $result = $adapter->listContents('fail.rawlist');
        $this->assertEquals(array(), $result);
    }
}
