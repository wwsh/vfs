VFS File System Iterator
========================

A Virtual File System (VFS) directory iterator for PHP, compatible with the API of RecursiveDirectoryIterator and DirectoryIterator, able to mimic original class behaviour, while creating a virtual, non-existent file system from a PHP array.

Input
-----
Factory accepts two parameters:
* string or array: either path to a JSON file, or array itself.
* (optional) the initial path to iterate, for example: "/music/oldies"

Array format
------------
Each key represents a directory, each value is the filename itself.

Structure example:

     '/music' => [
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

JSON format
-----------
Identical. See **vfs-example.json** for an example.

Output
------

The following methods are supported and fully emulated:

    public function getFilename();

    public function getPath();

    public function isDir();

    public function isDot();

    public function isFile();

    public function current();

    public function next();

    public function valid();

    public function getChildren();

    public function hasChildren();

    public function rewind();

See [**here**][1] for original API explanation.

Example working code
--------------------

        $path = ... // define starting path
        $vfsArray = []; // paste the VFS array here. See above.
        $iterator = FileSystemFactory::create('Virtual', [$path, $vfsArray]);
        $iterator->rewind();
        $this->out('Browsing path ' . $iterator->getPath() . '...');

        while ($iterator->valid()) {
            $file = $iterator->current();

            if ($file->isDot() || $file->isFile()) {
                continue;
            }

            $this->out('Processing directory ' . $file->getFilename());
            // process (string)$file.....
            $results[] = $result;

            $iterator->next();
        }

Warnings
--------

Not designed to not use in **foreach** loops.

Notes
-----

Licensed via MIT. Feel free to modify and contribute. Enjoy!

[1]: http://php.net/manual/en/class.recursivedirectoryiterator.php
