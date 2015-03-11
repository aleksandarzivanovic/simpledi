<?php

namespace System\Template;

use System\Di\Di;

/**
 * {%parent "parent.file.html"  %}
 * {%area content%}
 *  <div>object.attribute</div>
 *  <div>array.key</div>
 * {%endarea%}
 * 
 */
class Template implements TemplateInterface {

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

    public function load($file)
    {
        if (false == is_file($file)) {
            throw new \RuntimeException("Template {$file} is not file");
        }

        $this->file = $file;
        $this->source = file_get_contents($this->file);

        $this->getParent();
        $this->parseAreas();
        $this->parseVariables();
    }

    public function render(array $values = [])
    {
        $this->setRenderVariables($values);
        $this->replaceRenderVariables();
        
        if ($this->parent instanceof TemplateInterface) {
            $this->parent->setVariables($this->variables);
            return $this->parent->render($values);
        }
        
        echo $this->source;
    }

    private function setRenderVariables(array $variables)
    {
        foreach ($variables as $variable => $value) {
            $this->values[$variable] = $value;
        }
    }

    private function replaceRenderVariables()
    {
        foreach($this->variables['v'] as $area => $variables) {
            foreach ($variables as $index => $variable) {
                $this->variables['v'][$area][$index] = $this->getArrayValue($this->values, $variable);
            }
            
            $this->source = str_replace($this->variables['p'][$area], $this->variables['v'][$area], $this->source);
        }
    }
    
    private function replaceAreas()
    {
        foreach($this->areas as $area => $content) {
//            preg_replace($content, $replacement, $subject)
        }
    }
    
    public function getArrayValue(&$array, $key, $value = null) {
        $index = explode('.', $key, 2);
        
        if (false == isset($array[$index[0]])){
            throw new \RuntimeException("Key {$index[0]} doesn't exist.");
        }

        if(false == empty($index[1])) {
            return $this->getArrayValue($array[$index[0]], $index[1], $value);   
        }
        
        return $array[$index[0]];
    }
    
    public function setVariables(array $variables)
    {
        $this->variables = $variables;
    }

        public function setArea($area, $value)
    {
        if (empty($this->areas[$area])) {
            throw new \RuntimeException("Area {$area} not found.");
        }
        
        $this->areas[$area] = $value;
    }
    
    public function getArea($area)
    {
        return empty($this->areas[$area]) ? null : $this->areas[$area];
    }
    
    public function getAreas()
    {
        return $this->areas;
    }
    
    public function getVariables()
    {
        return $this->variables;
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
    
    private function trimVariables(array $variables)
    {
        foreach ($variables as $index => $variable) {
            $variables[$index] = trim($variable);
        }
        
        return $variables;
    }
    
    private function parseAreas()
    {
        $pattern = '/\{\%area ([A-Z0-9_.:]+)\%}(.*?)\{\%endarea\%\}/mis';
        $areas = [];

        preg_match_all($pattern, $this->source, $areas);
                
        $this->areas = $this->mapAreas($areas);
    }

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
        $pattern = '/\{\%parent \"([A-Za-z._]+)\" +\%\}/i';
        $parent = [];
        
        preg_match_all($pattern, $this->source, $parent);

        if (empty($parent[0]) || empty($parent[1])) {
            return;
        }
        
        $this->source = str_replace($parent[0][0], '', $this->source);

        if (count($parent[1]) > 1) {
            throw new \RuntimeException("Template can have only 1  parent. Template: {$this->file}");
        }

        $template = Di::getInstance()->get('system.template', false);
        $this->parent = $template->load($parent[1][0]);
    }

}
