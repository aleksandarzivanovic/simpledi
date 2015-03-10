<?php

namespace System\Http\Response;

use System\Http\Header\HeaderInterface;

class Response implements ResponseInterface
{
    /** @var HeaderInterface */
    private $header;

    /** @var string */
    private $templateFile;

    /** @var array */
    private $templateData = [];

    /**
     * @param HeaderInterface $header
     */
    public function __construct(HeaderInterface $header)
    {
        $this->header = $header;
    }

    /**
     * @param $template
     * @param array $data
     * @return ResponseInterface|$this
     */
    public function render($template, array $data)
    {
        $this->templateFile = $template;
        $this->templateData = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getTemplate()
    {
        return [$this->templateFile, $this->templateData];
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
