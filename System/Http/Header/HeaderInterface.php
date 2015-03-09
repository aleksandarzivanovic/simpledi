<?php

namespace System\Http\Header;

interface HeaderInterface
{
    /**
     * @param string $header
     * @param string $value
     * @return HeaderInterface|$this
     */
    public function setHeader($header, $value);

    /**
     * @param string $header
     * @return string|array|null
     */
    public function getHeader($header);

    /**
     * @return array
     */
    public function getHeaders();
}
