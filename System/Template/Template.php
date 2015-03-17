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
    private $variables = ['p' => [], 'v' => [], 'm' => []];

    /** @var array */
    private $values = [];

    /** @var string|null */
    private $cached = null;

    /** @var string */
    private $viewCachePath = 'Cache/views';

    /** @var string */
    private $child;

    /**
     * @param string $file
     * @param string|null $child
     */
    public function load($file, $child = null)
    {
        $this->child = $child;

        if ($this->child) {
            $this->getCache($this->child);
        } else {
            $this->getCache($file);
        }

        if ($this->cached) {
            return;
        }

        if (false == is_file($file)) {
            throw new \RuntimeException("Template {$file} is not file");
        }

        $this->file = $file;
        $this->source = file_get_contents($this->file);

        $this->parseAreas();
        $this->parseVariables();
        $this->getParent();
    }

    /**
     * @param array $values
     * @return bool
     */
    public function render(array $values = [])
    {
        if ($this->cached && is_file($this->cached)) {
            extract($values);
            include $this->cached;

            return true;
        }

        if ($this->parent instanceof TemplateInterface) {
            $this->parent->setAreas($this->areas);
            $this->parent->setVariables($this->variables);

            if ($this->child) {
                $child = $this->child;
            } else {
                $child = $this->file;
            }


            return $this->parent->render($values, $child);
        }

        $this->setRenderVariables($values);
        $this->parseForMethodsVariables();
        $this->preRender();

        $this->updateCache();
        $this->render($values);

        return true;
    }

    private function updateCache()
    {
        if ($this->child) {
            $file = $this->child;
        } else {
            $file = $this->file;
        }

        $cache = $this->getCachePath($file);

        $fileHandler = fopen($cache, 'a');
        fwrite($fileHandler, $this->source);
        fclose($fileHandler);

        if ($fileHandler) {
            $this->cached = $cache;
        }
    }

    private function replaceTemplateToPhpVariables()
    {
        $pattern = '/\{\{ {0,}+([A-Z._-]+) {0,}+\}\}/msi';
        preg_match_all($pattern, $this->source, $matches);

        $replaces = $this->replaceVariablesWithPhp($matches);
        $this->source = str_replace(array_keys($replaces), $replaces, $this->source);
    }

    /**
     * @param array $areas
     */
    public function setAreas(array $areas)
    {
        foreach ($areas as $area => $content) {
            $this->setArea($area, $content);
        }
    }

    /**
     * @param array $variables
     */
    public function setVariables(array $variables)
    {
        $this->variables = array_merge_recursive($this->variables, $variables);
    }

    /**
     * @param string $area
     * @param string $value
     */
    public function setArea($area, $value)
    {
        $this->areas[$area] = $value;
    }

    /**
     * @param string $area
     *
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

    private function parseVariables()
    {
        $pattern = '/\{\{([ A-Z0-9._]+)\}\}/mis';

        foreach ($this->areas as $area => $content) {
            $tmpMatches = [];
            preg_match_all($pattern, $content, $tmpMatches);

            if (false == isset($this->variables['p'][$area]) || false == is_array($this->variables['p'][$area])) {
                $this->variables['p'][$area] = [];
            }

            if (false == isset($this->variables['v'][$area]) || false == is_array($this->variables['v'][$area])) {
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

            if (false == empty($this->variables['m'][$area]['replaces'])) {
                $this->source = str_replace(array_keys($this->variables['m'][$area]['replaces']), $this->variables['m'][$area]['replaces'], $this->source);
            }

            $this->source = preg_replace($pattern, $content, $this->source);
        }

        $this->replaceTemplateToPhpVariables();
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

    private function parseForMethodsVariables()
    {
        foreach (array_keys($this->areas) as $area) {
            $this->parseAreaForMethodsVariables($area);
        }
    }

    /**
     * @param string $area
     */
    private function parseAreaForMethodsVariables($area)
    {
        if (empty($this->variables['m'][$area]) || false == is_array($this->variables['m'][$area])) {
            return;
        }

        foreach ($this->variables['m'][$area] as $index => $method) {
            if (false == isset($this->values[$method['array']]) || false == is_array($this->values[$method['array']])) {
                throw new \RuntimeException("Variable {$method['array']} is not defined or is not array");
            }

            $pattern = "/\{\{ {0,}+{$method['variable']}.{0,1}([A-Z0-9._]+) {0,}+\}\}/mis";
            preg_match_all($pattern, $method['body'], $matches);

            $replace = $method['body'];

            foreach ($this->values[$method['array']] as $idx => $v) {
                foreach ($matches[1] as $index => $match) {
                    $replace = str_replace($matches[0][$index], $this->getArrayValue($v, $match), $replace);
                }

                if ($idx + 1 < count($this->values[$method['array']])) {
                    $replace .= $method['body'];
                }

                $this->variables['m'][$area]['replaces'][$method['id']] = $replace;
            }
        }
    }

    /**
     * @param $array
     * @param $key
     *
     * @return mixed
     */
    private function getArrayValue($array, $key)
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

    /**
     * @param array $variables
     *
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

        $methods = [];

        foreach ($this->areas as $area => $content) {
            $methods[$area] = $this->parseForMethods($content);
        }

        $this->replaceForMethodBody($methods);
    }

    /**
     * @param array $methods
     */
    private function replaceForMethodBody(array $methods)
    {
        foreach ($methods as $area => $method) {
            if (empty($method)) {
                continue;
            }

            $this->variables['m'][$area] = $method;

            foreach ($this->variables['m'][$area] as $m) {
                $this->areas[$area] = str_replace($m['area'], $m['snippet'], $this->areas[$area]);
            }
        }
    }

    private function parseForMethods($content)
    {
        $pattern = '/\{\% {0,}+for ([A-Z0-9_.:]+) in ([A-Z0-9_.:]+) {0,}+\%}(.*?)\{\% {0,}+endfor {0,}+\%\}/mis';
        preg_match_all($pattern, $content, $matches);

        if (empty($matches[0])) {
            return false;
        }

        $snippet = file_get_contents('System/Template/Snippets/foreach.code');

        $data = [];

        foreach ($matches[0] as $index => $body) {
            $currentSnippet = str_replace(
                ['{array}', '{variable}'],
                ['$'.$matches[2][$index], '$'.$matches[1][$index]],
                $snippet
            );
            $d = [
                'area' => $body,
                'variable' => $matches[1][$index],
                'array' => $matches[2][$index],
                'body' => $matches[3][$index],
                'id' => uniqid('for_', true),
                'snippet' => $currentSnippet,
            ];

            $this->compileTemplate($d);

            $data[] = $d;
        }

        return $data;
    }

    private function compileTemplate(&$method)
    {
        $body = $method['body'];
        $pattern = '/\{\{ {0,}+([A-Z._-]+) {0,}+\}\}/msi';
        preg_match_all($pattern, $body, $matches);

        if (empty($matches[1])) {
            return;
        }

        $replaces = $this->replaceVariablesWithPhp($matches);

        $method['snippet'] = str_replace('{body}', $this->replaceVariables($replaces, $method['body']), $method['snippet']);
    }

    private function replaceVariablesWithPhp(array $matches)
    {
        $replaces = [];

        foreach ($matches[1] as $index => $match) {
            $extracted = explode('.', $match);
            $v = $extracted[0];
            unset($extracted[0]);

            if (empty($extracted)) {
                $variable = '<?php echo $'.$v.';?>';
            } else {
                $variable = '<?php echo $'.$v.'["'.implode('"]["', $extracted).'"];?>';
            }

            $replaces[$matches[0][$index]] = $variable;
        }

        return $replaces;
    }

    private function replaceVariables(array $replaces, $body)
    {
        $body = str_replace(
            array_keys($replaces),
            $replaces,
            $body
        );

        return $body;
    }

    /**
     * @param array $areas
     *
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
        $pattern = '/\{\%parent \"([A-Za-z._\\/]+)\" {0,}+\%\}/i';
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

        if ($this->child) {
            $child = $this->child;
        } else {
            $child = $this->file;
        }

        $template->load($parent[1][0], $child);
        $this->parent = $template;
    }

    /**
     * @param string $file
     */
    private function getCache($file)
    {
        $cacheFile = $this->getCachePath($file);

        if (is_file($cacheFile)) {
            $this->cached = $cacheFile;
        }
    }

    /**
     * @param string $file
     * @return string
     */
    private function getCachePath($file)
    {
        $file = str_replace('/', '_', $file);
        $cacheFile = $this->viewCachePath . '/' . trim($file, '/') . '.php';

        return $cacheFile;
    }
}
