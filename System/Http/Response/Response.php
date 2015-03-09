<?php

namespace System\Http\Response;

use System\Http\Header\HeaderInterface;

class Response implements ResponseInterface
{
    /** @var HeaderInterface */
    private $header;

    /**
     * @param HeaderInterface $header
     */
    public function __construct(HeaderInterface $header)
    {
        $this->header = $header;
    }

    /**
     * @param string $header
     * @param string $value
     * @return HeaderInterface|$this
     */
    public function setHeader($header, $value)
    {
        $this->header->setHeader($header, $value);
    }

    /**
     * @param string $header
     * @return string|array|null
     */
    public function getHeader($header)
    {
        return $this->header->getHeader($header);
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->header->getHeaders();
    }

    public function updateHeaders()
    {
        $this->header->updateHeaders();

        return $this;
    }
}
