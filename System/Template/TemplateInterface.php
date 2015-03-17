<?php

namespace System\Template;

interface TemplateInterface
{
    /**
     * @param array $values
     *
     * @return bool
     */
    public function render(array $values = []);

    /**
     * @param string $file
     */
    public function load($file);

    /**
     * @param array $areas
     */
    public function setAreas(array $areas);

    /**
     * @param array $variables
     */
    public function setVariables(array $variables);

    /**
     * @param string $area
     * @param string $value
     */
    public function setArea($area, $value);

    /**
     * @param string $area
     *
     * @return null|array
     */
    public function getArea($area);

    /**
     * @return array
     */
    public function getAreas();

    /**
     * @return array
     */
    public function getVariables();
}
