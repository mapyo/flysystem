<?php


use League\Flysystem\Plugin\ListFiles;
use Prophecy\PhpUnit\ProphecyTestCase;

class ListFilesTests extends ProphecyTestCase
{
    private $filesystem;
    private $actualFilesystem;

    /**
     * @before
     */
    public function setupFilesystem()
    {
        $this->filesystem = $this->prophesize('League\Flysystem\FilesystemInterface');
        $this->actualFilesystem = $this->filesystem->reveal();
    }

    public function testHandle()
    {
        $plugin = new ListFiles();
        $this->assertEquals('listFiles', $plugin->getMethod());
        $this->filesystem->listContents('dirname', true)->willReturn(array(
            array('path' => 'dirname', 'type' => 'dir'),
            array('path' => 'dirname/path.txt', 'type' => 'file'),
        ));
        $plugin->setFilesystem($this->actualFilesystem);
        $output = $plugin->handle('dirname', true);
        $this->assertEquals(array(array('path' => 'dirname/path.txt', 'type' => 'file')), $output);
    }
}
