<?php

namespace DoctrineExtensions\Query\Mysql;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * "FROM_BASE64" "(" "$fieldIdentifierExpression" ")"
 *
 * @link https://dev.mysql.com/doc/refman/en/string-functions.html#function_from-base64
 *
 * @example SELECT FROM_BASE64(foo) FROM dual;
 */
class FromBase64 extends FunctionNode
{
    public $field = null;

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->field = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'FROM_BASE64(' . $this->field->dispatch($sqlWalker) . ')';
    }
}
