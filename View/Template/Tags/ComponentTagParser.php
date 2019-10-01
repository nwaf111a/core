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

use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

use Arikaim\Core\View\Template\Tags\ComponentNode;

/**
 * Component tag parser
 */
class ComponentTagParser extends AbstractTokenParser
{
    /**
     * Parse tag 'component'
     *
     * @param Token $token
     * @return ComponentNode
     */
    public function parse(Token $token)
    {
        $line = $token->getLine();
        $stream = $this->parser->getStream();
        // tag params
        $component_name = $stream->expect(Token::STRING_TYPE)->getValue();   
        $params = $stream->getCurrent()->getValue(); 
        $params = (is_array($params) == false) ? [] : $params;
    
        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this,'decideTagEnd'],true);
        $stream->expect(Token::BLOCK_END_TYPE);
        return new ComponentNode($body,['name' => $component_name, 'params' => $params],$line,$this->getTag());
    }

    /**
    * 
    * Return true when the expected end tag is reached.
    *
    * @param Token $token
    * @return bool
    */
    public function decideTagEnd(Token $token)
    {
        return $token->test('endcomponent');
    }

    /**
    * Tag name
    *
    * @return string
    */
    public function getTag()
    {
        return 'component';
    }
}