<?php

namespace Square\Hyrule\Tests;

use Generator;
use Hoa\File\File;
use Illuminate\Http\UploadedFile;
use Square\Hyrule\Hyrule;
use Square\Hyrule\Nodes\FileNode;
use Square\Hyrule\Rules\Dimensions;
use Square\Hyrule\Rules\MIMEType;

class FileNodeTest extends NodeTestAbstract
{
    protected function getBuilderMethodName(): string
    {
        return 'file';
    }

    public function dataValid()
    {
        yield 'image' => [
            UploadedFile::fake()->image('foo.jpg'),
            static function(FileNode $node) {
                return $node->image();
            },
        ];

        yield 'image: matches max width & height' => [
            UploadedFile::fake()->image('foo.jpg', 800, 600),
            static function(FileNode $node) {
                $node->dimensions()->maxWidth(800)->maxHeight(600);
            },
        ];

        yield 'image: smaller than max width & height' => [
            UploadedFile::fake()->image('foo.jpg', 800, 600),
            static function(FileNode $node) {
                $node->dimensions()->maxWidth(1000)->maxHeight(1000);
            }
        ];

        yield 'image: exact dimensions' => [
            UploadedFile::fake()->image('foo.jpg', 1024, 768),
            static function(FileNode $node) {
                $node->dimensions()->width(1024)->height(768);
            },
        ];

        yield 'image: ratio' => [
            UploadedFile::fake()->image('foo.jpg', 800, 800),
            static function(FileNode $node) {
                $node->dimensions()->ratio(1);
            }
        ];

        yield 'mime-type: pdf' => [
            UploadedFile::fake()->create('foo.pdf', 0, 'application/pdf'),
            static function(FileNode $node) {
                $node->mimeType()->pdf();
            }
        ];

        yield 'mime-type: json' => [
            UploadedFile::fake()->create('foo.json', 0, 'application/json'),
            static function(FileNode $node) {
                $node->mimeType()->json();
            }
        ];

        yield 'mime-type: text/plain' => [
            UploadedFile::fake()->create('foo.txt', 0, 'text/plain'),
            static function(FileNode $node) {
                $node->mimeType()->text('plain');
            }
        ];

        yield 'mime-type explicit: text/plain' => [
            UploadedFile::fake()->create('foo.txt', 0, 'text/plain'),
            static function(FileNode $node) {
                $node->mimeType()->allow('text/plain');
            },
        ];
    }
    public function dataInvalid()
    {
        yield 'image' => [
            UploadedFile::fake()->create('foo.txt'),
            static function(FileNode $node) {
                return $node->image();
            },
        ];

        yield 'image: too large' => [
            UploadedFile::fake()->image('foo.jpg', 800, 600),
            static function(FileNode $node) {
                $node->dimensions()->maxWidth(500)->maxHeight(600);
            },
        ];

        yield 'image: too small' => [
            UploadedFile::fake()->image('foo.jpg', 800, 600),
            static function(FileNode $node) {
                $node->dimensions()->minHeight(1000)->minWidth(1000);
            }
        ];

        yield 'image: does not match dimensions' => [
            UploadedFile::fake()->image('foo.jpg', 800, 600),
            static function(FileNode $node) {
                $node->dimensions()->width(1024)->height(768);
            },
        ];

        yield 'image: does not match ratio' => [
            UploadedFile::fake()->image('foo.jpg', 800, 800),
            static function(FileNode $node) {
                $node->dimensions()->ratio(0.5);
            }
        ];

        yield 'mime-type: not pdf' => [
            UploadedFile::fake()->create('foo.txt'),
            static function(FileNode $node) {
                $node->mimeType()->pdf();
            }
        ];

        yield 'mime-type: not json' => [
            UploadedFile::fake()->create('foo.pdf'),
            static function(FileNode $node) {
                $node->mimeType()->json();
            }
        ];
    }

    protected function getNodeClassName(): string
    {
        return FileNode::class;
    }

    protected function defaultRules(): array
    {
        return [];
    }

    public function testDimensionsFluentAPI(): void
    {
        $file = Hyrule::create()->file('file');
        $dimensions = $file->dimensions();
        $this->assertInstanceOf(Dimensions::class, $dimensions);
        $this->assertSame($dimensions, $file->dimensions());
        $this->assertSame($file, $dimensions->end());
    }

    public function testMIMETypeFluentAPI(): void
    {
        $file = Hyrule::create()->file('file');
        $mime = $file->mimeType();
        $this->assertInstanceOf(MIMEType::class, $mime);
        $this->assertSame($mime, $file->mimeType());
        $this->assertSame($file, $mime->end());
    }

    /**
     * @param array<mixed> $expectedRules
     * @param callable $callback
     * @dataProvider dataBuiltRules
     * @return void
     */
    public function testBuiltRules(array $expectedRules, callable $callback): void
    {
        $file = Hyrule::create()->file('file');
        $callback($file);
        $this->assertSame($expectedRules, $file->build()['file']);
    }

    /**
     * @return Generator
     */
    public function dataBuiltRules(): Generator
    {
        yield 'dimensions: max width & height' => [
            ['dimensions:max_width=100,max_height=100'],
            static function(FileNode $node) {
                $node->dimensions()->maxWidth(100)->maxHeight(100);
            },
        ];

        yield 'dimensions: ratio' => [
            ['dimensions:ratio=0.5'],
            static function(FileNode $node) {
                $node->dimensions()->ratio(0.5);
            },
        ];

        yield 'mime-type: allow multiple' => [
            ['mimetypes:text/plain,application/json,image/jpeg'],
            static function(FileNode $node) {
                $node->mimeType()
                    ->allow('text/plain')
                    ->allow('application/json')
                    ->allow('image/jpeg');
            },
        ];

        yield 'mime-type: via top-level type method' => [
            ['mimetypes:text/plain,application/json,application/pdf,image/jpeg,image/png,video/webm,video/mp4'],
            static function(FileNode $node) {
                $node->mimeType()
                    ->text('plain')
                    ->application('json', 'pdf')
                    ->image('jpeg', 'png')
                    ->video('webm', 'mp4');
            },
        ];

        yield 'mime-type: de-duped' => [
            ['mimetypes:text/plain,application/json,application/pdf'],
            static function(FileNode $node) {
                $node->mimeType()
                    ->text('plain')
                    ->application('json', 'pdf')
                    ->application('pdf', 'json')
                    ->allow('application/json')
                    ->allow('application/pdf');
            },
        ];
    }
}
