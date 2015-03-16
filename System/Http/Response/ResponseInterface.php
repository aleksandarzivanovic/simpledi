<?php

namespace System\Http\Response;

use System\Http\Header\HeaderInterface;

/**
 * @author coa
 */
interface ResponseInterface extends HeaderInterface
{
    /**
     * @param $template
     * @param array $data
     *
     * @return ResponseInterface
     */
    public function render($template, array $data);

    /**
     * @return array
     */
    public function getTemplate();
}
