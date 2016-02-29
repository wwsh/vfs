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

namespace spec\WW\Vfs\Iterator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class VirtualSpec extends ObjectBehavior
{
    private $exampleDefinition = [
        '/root' => [
            '1993 Haddaway - What Is Love (Remixes)' => [
                "1 - Haddaway - What Is Love (7'' Mix).wav",
                "2 - Haddaway - What Is Love (Eat This Mix).wav",
                "3 - Haddaway - What Is Love (Tour De Trance Mix).wav"
            ],
            "1985 Paul Hardcastle - 19 (The Final Story) [601 814]" => [
                "01 - Paul Hardcastle - 19 (The Final Story).wav",
                "02 - Paul Hardcastle - 19 (Destruction Mix).wav"
            ]
        ]
    ];

    function it_should_accept_vfs_filepath_in_constructor()
    {
        $this->beConstructedWith('/music', __DIR__ . '/../../../../vfs-example.json');
        $this->shouldHaveType('WW\Vfs\Iterator\Virtual');
    }

    function it_should_throw_exception_if_vfs_file_doesnt_exist()
    {
        $this->beConstructedWith('/root', 'some_silly_name.json');
        $this->shouldThrow(new \Exception('File not found'))->duringInstantiation();
    }

    function it_should_accept_vfs_array_definition_in_constructor()
    {
        $this->beConstructedWith('/root', $this->exampleDefinition);
        $this->shouldHaveType('WW\Vfs\Iterator\Virtual');
    }

    function it_should_iterate_through_directories()
    {
        $this->beConstructedWith('/root', $this->exampleDefinition);
        $this->rewind();
        $this->current()->__toString()->shouldBe('1993 Haddaway - What Is Love (Remixes)');
        $this->valid()->shouldBe(true);
        $this->next();
        $this->current()->__toString()->shouldBe('1985 Paul Hardcastle - 19 (The Final Story) [601 814]');
        $this->valid()->shouldBe(true);
        $this->next();
        $this->current()->shouldBe(false);
        $this->valid()->shouldBe(false);
    }

    function it_should_be_recursive_and_detailed()
    {
        $this->beConstructedWith('/root', $this->exampleDefinition);
        $this->rewind();
        $this->current()->__toString()->shouldBe('1993 Haddaway - What Is Love (Remixes)');
        $this->valid()->shouldBe(true);
        $this->isDir()->shouldBe(true);
        $iterator = $this->getChildren();
        $iterator->shouldHaveType('WW\Vfs\Iterator\Virtual');
        $iterator->current()->__toString()->shouldBe('1 - Haddaway - What Is Love (7\'\' Mix).wav');
        $iterator->valid()->shouldBe(true);
        $iterator->isDir()->shouldBe(false);
        $iterator->isFile()->shouldBe(true);
        $iterator->next();
        $iterator->current()->__toString()->shouldBe('2 - Haddaway - What Is Love (Eat This Mix).wav');
        $iterator->valid()->shouldBe(true);
        $iterator->isDir()->shouldBe(false);
        $iterator->isFile()->shouldBe(true);
        $iterator->next();
        $iterator->current()->__toString()->shouldBe('3 - Haddaway - What Is Love (Tour De Trance Mix).wav');
        $iterator->valid()->shouldBe(true);
        $iterator->isDir()->shouldBe(false);
        $iterator->isFile()->shouldBe(true);
        $iterator->next();
        $iterator->current()->shouldBe(false);
        $iterator->valid()->shouldBe(false);
        $iterator->isDir()->shouldBe(false);
        $iterator->isFile()->shouldBe(false);
    }

    public function it_should_not_return_dots()
    {
        $this->beConstructedWith('/root', $this->exampleDefinition);
        $this->isDot()->shouldBe(false);
    }
    
    function it_should_return_functional_iterator_objects()
    {
        $this->beConstructedWith('/music', __DIR__ . '/../../../../vfs-example.json');
        $this->current();
        $iterator = $this->getChildren();
        $iterator->getPath()->shouldBe('/music/1993 Haddaway - What Is Love (Remixes)');
        $iterator->current()->__toString()->shouldBe('1 - Haddaway - What Is Love (7\'\' Mix).wav');
        $iterator->valid()->shouldBe(true);
        $file = $iterator->current();
        $file->isDir()->shouldBe(false);
        $file->isFile()->shouldBe(true);
        $file->getFilename()->shouldBe('1 - Haddaway - What Is Love (7\'\' Mix).wav');
        
    }

}
