<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2017-2019 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license.html
 * 
*/
namespace Arikaim\Core\View\Template\Tags;

use Twig\Compiler;
use Twig\Node\Node;
use Twig\Node\NodeOutputInterface;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\SetNode;

use \Arikaim\Core\View\Html\HtmlComponent;

/**
 * Component tag node
 */
class ComponentNode extends Node implements NodeOutputInterface
{
    /**
     * Constructor
     *
     * @param Node $body
     * @param array $params
     * @param integer $line
     * @param string $tag
     */
    public function __construct(Node $body, $params = [], $line = 0, $tag = 'component')
    {
        parent::__construct(['body' => $body],$params,$line,$tag);
    }

    /**
     * Compile node
     *
     * @param Compiler $compiler
     * @return void
     */
    public function compile(Compiler $compiler)
    {
        $compiler->addDebugInfo($this);
        $component_name = $this->getAttribute('name');
        $params = $this->getAttribute('params');
        $exported_params = var_export($params, true);

        $count = count($this->getNode('body'));
        $compiler->write("\$params = $exported_params;")->raw(PHP_EOL);
        $compiler->write("\$context = array_merge(\$context,\$params);")->raw(PHP_EOL);
        $compiler->write('ob_start();')->raw(PHP_EOL);
        $compiler->subcompile($this->getNode('body'),true);
        $compiler->write("\$context['content'] = ob_get_clean();")->raw(PHP_EOL);
     
        for ($i = 0; ($i < $count); $i++) {
            $item = $this->getNode('body')->getNode($i);         
            if ($item instanceof SetNode) {
                $compiler->subcompile($item,true);
            }          
        }
        $compiler->raw("echo \\Arikaim\\Core\\View\\Html\\HtmlComponent::loadComponent('$component_name',\$context);" . PHP_EOL);
    }
}