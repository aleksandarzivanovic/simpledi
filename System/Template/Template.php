<?php

namespace System\Template;

use System\Di\Di;

class Template implements TemplateInterface
{
    /** @var string */
    private $file;

    /** @var string */
    private $source;

    /** @var TemplateInterface|null */
    private $parent;

    /** @var array */
    private $areas = [];

    /** @var array */
    private $variables = ['p' => [], 'v' => []];

    /** @var array */
    private $values = [];

    /**
     * @param string $file
     */
    public function load($file)
    {
        $file = "Web/views/{$file}";

        if (false == is_file($file)) {
            throw new \RuntimeException("Template {$file} is not file");
        }

        $this->file = $file;
        $this->source = file_get_contents($this->file);

        $this->getParent();
        $this->parseAreas();
        $this->parseVariables();
    }

    /**
     * @param array $values
     */
    public function render(array $values = [])
    {
        if ($this->parent instanceof TemplateInterface) {
            $this->parent->setAreas($this->areas);
            $this->parent->setVariables($this->variables);

            return $this->parent->render($values);
        }

        $this->setRenderVariables($values);
        $this->replaceRenderVariables();
        $this->replaceAreas();

        $this->preRender();
        echo $this->source;
    }

    /**
     * @param array $areas
     */
    public function setAreas(array $areas)
    {
        $this->areas = $areas;
    }

    /**
     * @param array $variables
     */
    public function setVariables(array $variables)
    {
        $this->variables = $variables;
    }

    /**
     * @param string $area
     * @param string $value
     */
    public function setArea($area, $value)
    {
        if (empty($this->areas[$area])) {
            throw new \RuntimeException("Area {$area} not found.");
        }

        $this->areas[$area] = $value;
    }

    /**
     * @param string $area
     * @return null|array
     */
    public function getArea($area)
    {
        return empty($this->areas[$area]) ? null : $this->areas[$area];
    }

    /**
     * @return array
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param $array
     * @param $key
     * @return mixed
     */
    private function getArrayValue(&$array, $key)
    {
        $index = explode('.', $key, 2);

        if (false == isset($array[$index[0]])) {
            throw new \RuntimeException("Key {$index[0]} doesn't exist.");
        }

        if (false == empty($index[1])) {
            return $this->getArrayValue($array[$index[0]], $index[1]);
        }

        return $array[$index[0]];
    }

    private function parseVariables()
    {
        $pattern = '/\{\{([ A-Z0-9._]+)\}\}/mis';

        foreach ($this->areas as $area => $content) {
            $tmpMatches = [];
            preg_match_all($pattern, $content, $tmpMatches);

            if (false == isset($this->variables['p'][$area])) {
                $this->variables['p'][$area] = [];
            }

            if (false == isset($this->variables['v'][$area])) {
                $this->variables['v'][$area] = [];
            }

            $this->variables['p'][$area] = array_merge($this->variables['p'][$area], $this->trimVariables($tmpMatches[0]));
            $this->variables['v'][$area] = array_merge($this->variables['v'][$area], $this->trimVariables($tmpMatches[1]));
        }
    }

    private function preRender()
    {
        foreach ($this->areas as $area => $content) {
            $pattern = '/\{\%area '.$area.' {0,}+\%\}(.*?)\{\% {0,}+endarea {0,}+\%\}/msi';
            $this->source = preg_replace($pattern, $content, $this->source);
        }
    }

    /**
     * @param array $variables
     */
    private function setRenderVariables(array $variables)
    {
        foreach ($variables as $variable => $value) {
            $this->values[$variable] = $value;
        }
    }

    private function replaceRenderVariables()
    {
        foreach ($this->variables['v'] as $area => $variables) {
            foreach ($variables as $index => $variable) {
                $this->variables['v'][$area][$index] = $this->getArrayValue($this->values, $variable);
            }
        }
    }

    private function replaceAreas()
    {
        foreach ($this->areas as $area => $content) {
            $this->areas[$area] = str_replace($this->variables['p'][$area], $this->variables['v'][$area], $content);
        }
    }

    /**
     * @param array $variables
     * @return array
     */
    private function trimVariables(array $variables)
    {
        foreach ($variables as $index => $variable) {
            $variables[$index] = trim($variable);
        }

        return $variables;
    }

    private function parseAreas()
    {
        $pattern = '/\{\%area ([A-Z0-9_.:]+) {0,}+\%}(.*?)\{\% {0,}+endarea {0,}+\%\}/mis';
        $areas = [];

        preg_match_all($pattern, $this->source, $areas);

        if (false == empty($areas[0])) {
            $this->areas = $this->mapAreas($areas);
        }
    }

    /**
     * @param array $areas
     * @return array
     */
    private function mapAreas(array $areas)
    {
        $mapped = [];

        foreach ($areas[1] as $key => $area) {
            $mapped[$area] = $areas[2][$key];
        }

        return $mapped;
    }

    private function getParent()
    {
        $pattern = '/\{\%parent \"([A-Za-z._]+)\" {0,}+\%\}/i';
        $parent = [];

        preg_match_all($pattern, $this->source, $parent);

        if (empty($parent[0]) || empty($parent[1])) {
            return;
        }

        $this->source = str_replace($parent[0][0], '', $this->source);

        if (count($parent[1]) > 1) {
            throw new \RuntimeException("Template can have only 1 parent. Template: {$this->file}");
        }

        $template = Di::getInstance()->get('system.template', false);
        $template->load($parent[1][0]);
        $this->parent = $template;
    }
}
