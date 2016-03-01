<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 WW Software House
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

namespace WW\Vfs\Iterator;

use WW\Vfs\Iterator\Exception\VfsException;

/**
 * Class Virtual
 * @package AppBundle\Iterator
 */
class Virtual implements IteratorInterface
{
    /**
     * Contains the virtual file system definition data.
     * This is being iterated on.
     *
     * @var array
     */
    private $vfs;

    /**
     * Currently parsed path.
     *
     * @var string
     */
    private $path;

    /**
     * The current data.
     *
     * @var
     */
    private $cursor;

    /**
     * Virtual constructor.
     * @param $vfsDefinition
     * @param string $path
     * @throws \Exception
     */
    public function __construct($path, $vfsDefinition)
    {
        if (is_string($vfsDefinition)) {
            if (false === file_exists($vfsDefinition)) {
                throw new VfsException('File not found');
            }

            if (false === is_readable($vfsDefinition)) {
                throw new VfsException('No permission to read file: ' . $vfsDefinition);
            }

            $contents = file_get_contents($vfsDefinition);
            $this->vfs = json_decode($contents, true);
        } elseif (is_array($vfsDefinition)) {
            $this->vfs = $vfsDefinition;
        }

        if (null === $vfsDefinition || empty($vfsDefinition) || empty($this->vfs)) {
            throw new \Exception('VFS empty');
        }

        if ($path === null) {
            $path = key($this->vfs);
        }

        $this->path = $path;

        if (!isset($this->vfs[$path])) {
            throw new \Exception('Path not found');
        }

        $this->cursor = $this->vfs[$path];
        $this->rewind();
    }

    /**
     * Whenever we need to convert the current iterator to string,
     * in other words, iterator must be printable (return current filename).
     * 
     * @return string
     */
    public function __toString()
    {
        if (!current($this->cursor)) {
            return '';
        }
        $key = key($this->cursor);

        $result = !is_numeric($key) ? $key : current($this->cursor);

        return (string)$result;
    }

    /**
     * Getting the filename. Using the __toString()
     * 
     * @return string
     */
    public function getFilename()
    {
        $current = $this->current();

        return (string)$current;
    }

    /**
     * Getting the path being iterated.
     * 
     * @return null|string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Getting the isDirectory flag.
     * 
     * Actually, we don't keep such information. 
     * We presume that numeric keys mean we have reached the file level. 
     * 
     * @return bool
     */
    public function isDir()
    {
        $key = key($this->cursor);

        return $this->valid() ? !is_numeric($key) : false;
    }

    /**
     * Always false. Not supported.
     * 
     * @return bool
     */
    public function isDot()
    {
        return false;
    }

    /**
     * Inversion of isDir()
     * 
     * @return bool
     */
    public function isFile()
    {
        return $this->valid() ? !$this->isDir() : false;
    }

    /**
     * The classic iterator returns a file structure. 
     * We are returning ourselves just to keep simplicity.
     * 
     * @return mixed
     */
    public function current()
    {
        $current = current($this->cursor);

        if (!$current) {
            return false;
        }

        return $this;
    }

    /**
     * This is internally driven by the php array key mechanism. 
     * 
     * @return mixed
     */
    public function next()
    {
        return next($this->cursor);
    }

    /**
     * If we are at the end, php's current no longer returns anything
     * except false.
     * 
     * @return bool
     */
    public function valid()
    {
        $current = current($this->cursor);

        if (!$current) {
            return false;
        }

        return true;
    }

    /**
     * Entering a sub-element in the data tree.
     * 
     * Determining a new "mounting point" in the array 
     * and creating an iterator copy with it.
     * This makes the recursion possible.
     * 
     * @return Virtual|bool
     */
    public function getChildren()
    {
        $current = current($this->cursor);
        $key = key($this->cursor);

        if (!$current) {
            return false;
        }

        $newPath = $this->path . '/' . $key;

        $subVfs = [
            $newPath => $current
        ];

        return new self($newPath, $subVfs);
    }

    /**
     * Obviously files cannot have children :)
     * 
     * @return bool
     */
    public function hasChildren()
    {
        return $this->isDir();
    }

    /**
     * Based on classic php's rewind() method.
     */
    public function rewind()
    {
        reset($this->cursor);
    }


}
