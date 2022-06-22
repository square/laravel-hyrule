<?php

namespace Square\Hyrule\Rules;

use Square\Hyrule\Nodes\FileNode;

class MIMEType
{
    private FileNode $node;

    /**
     * @var string[]
     */
    protected array $mimeTypes = [];

    /**
     * @param FileNode $node
     */
    public function __construct(FileNode $node)
    {
        $this->node = $node;
    }

    /**
     * @return $this
     */
    public function pdf(): self
    {
        return $this->allow('application/pdf');
    }

    /**
     * @return $this
     */
    public function json(): self
    {
        return $this->allow('application/json');
    }

    /**
     * @param string $subType
     * @param string ...$subTypes
     * @return $this
     */
    public function text(string $subType, string ...$subTypes): self
    {
        array_unshift($subTypes, $subType);
        $this->typeAndSubtypes('text', $subTypes);
        return $this;
    }

    /**
     * @param string $subType
     * @param string ...$subTypes
     * @return $this
     */
    public function image(string $subType, string ...$subTypes): self
    {
        array_unshift($subTypes, $subType);
        $this->typeAndSubtypes('image', $subTypes);
        return $this;
    }

    /**
     * @param string $subType
     * @param string ...$subTypes
     * @return $this
     */
    public function audio(string $subType, string ...$subTypes): self
    {
        array_unshift($subTypes, $subType);
        $this->typeAndSubtypes('audio', $subTypes);
        return $this;
    }

    /**
     * @param string $subType
     * @param string ...$subTypes
     * @return $this
     */
    public function video(string $subType, string ...$subTypes): self
    {
        array_unshift($subTypes, $subType);
        $this->typeAndSubtypes('video', $subTypes);
        return $this;
    }

    /**
     * @param string $subType
     * @param string ...$subTypes
     * @return $this
     */
    public function application(string $subType, string ...$subTypes): self
    {
        array_unshift($subTypes, $subType);
        $this->typeAndSubtypes('application', $subTypes);
        return $this;
    }

    /**
     * @param string $subType
     * @param string ...$subTypes
     * @return $this
     */
    public function multipart(string $subType, string ...$subTypes): self
    {
        array_unshift($subTypes, $subType);
        $this->typeAndSubtypes('multipart', $subTypes);
        return $this;
    }

    /**
     * @param string $subType
     * @param string ...$subTypes
     * @return $this
     */
    public function message(string $subType, string ...$subTypes): self
    {
        array_unshift($subTypes, $subType);
        $this->typeAndSubtypes('message', $subTypes);
        return $this;
    }

    /**
     * @param string $type
     * @param array<string> $subTypes
     * @return $this
     */
    protected function typeAndSubtypes(string $type, array $subTypes): self
    {
        foreach ($subTypes as $subType) {
            $this->allow(sprintf('%s/%s', $type, $subType));
        }
        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function allow(string $type): self
    {
        $this->mimeTypes[$type] = $type;
        return $this;
    }

    public function __toString()
    {
        if (empty($this->mimeTypes)) {
            return '';
        }

        return sprintf('mimetypes:%s', implode(',', array_values($this->mimeTypes)));
    }

    /**
     * @return FileNode
     */
    public function end(): FileNode
    {
        return $this->node;
    }
}