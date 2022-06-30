<?php

namespace Square\Hyrule\Nodes;

use Square\Hyrule\Rules\Dimensions;
use Square\Hyrule\Rules\MIMEType;

class FileNode extends AbstractNode
{
    protected ?Dimensions $dimensions;

    protected ?MIMEType $mimeType;

    /**
     * @return Dimensions
     */
    public function dimensions(): Dimensions
    {
        if (!isset($this->dimensions)) {
            // Create a new Dimensions rule object, push it to the rules array, and
            // keep a reference so we can modify it when we need to in the future.
            $this->rule($this->dimensions = new Dimensions($this));
        }
        return $this->dimensions;
    }

    /**
     * @return $this
     */
    public function image(): FileNode
    {
        $this->rule('image');
        return $this;
    }

    /**
     * @return MIMEType
     */
    public function mimeType(): MIMEType
    {
        if (!isset($this->mimeType)) {
            // Create a new MIMEType rule object, push it to the rules array, and
            // keep a reference so we can modify it when we need to in the future.
            $this->rule($this->mimeType = new MIMEType($this));
        }
        return $this->mimeType;
    }
}