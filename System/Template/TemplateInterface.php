<?php

namespace System\Template;

interface TemplateInterface
{
    public function setAreas(array $areas);
    public function setVariables(array $variables);
    public function setArea($area, $value);
    public function getArea($area);
    public function getAreas();
    public function getVariables();
}
