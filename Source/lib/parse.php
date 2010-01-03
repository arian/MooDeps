<?php

/**
 * We use jsminplus http://crisp.tweakblogs.net/blog/1856/jsmin+-version-13.html
 * to parse the JS
*/ 
include_once 'jsminplus.php';

/**
 * 
 * With this class we can complete the the dependencies...
 * for example if the script needs Request.JSON it also needs Request and JSON
 * 
 */
class MooDependencies {
	
	/**
	 * Just an array with the dependencies of each component
	 * for example Request.JSON => [Request,JSON]
	 * 
	 * @var array
	 */
	protected $_deps;
	
	/**
	 * The found components
	 * 
	 * @var array
	 */
	protected $_comps;
	
	/**
	 * Set the dependencies list
	 * @param array $deps the same structure as scripts.json (json_encode with acoc = true)
	 */
	public function setDependencies($deps){
		$components = array();
		foreach($deps as $group){
			foreach($group as $component => $options){
				$components[$component] = $options['deps'];
			}
		}
		$this->_deps = $components;
	}
	
	/**
	 * A flat array with the dependencies of each component
	 * for example {Request.JSON => [Request,JSON],Element.Event => [...]}
	 * @return array
	 */
	public function getDependencies(){
		return $this->_deps;
	}
	
	/**
	 * Add a component.. this method will fild all 
	 * the dependencies of this component
	 * @param string $comp
	 */
	public function addComponent($comp){
		if(isset($this->_deps[$comp])){
			$this->_comps[$comp] = $comp;
			foreach($this->_deps[$comp] as $dep){			
				if($comp != $dep){
					$this->addComponent($dep);
				}
			}
		}	
	}
	
	/**
	 * This method will return an array of all the components
	 * @return array
	 */
	public function getComponents(){
		return $this->_comps;
	}	
	
}

/**
 * 
 * With this class we can loop trough all the js parts 
 * (strings, methods, variables, if,else,function etc. keywords etc.
 * This is a simplyfied version of jsminplus. 
 */
class JSDeps {
	
	protected $_tests = array();
	protected $_deps = array();
	
	private $parser;
	private $reserved = array(
		'break', 'case', 'catch', 'continue', 'default', 'delete', 'do',
		'else', 'finally', 'for', 'function', 'if', 'in', 'instanceof',
		'new', 'return', 'switch', 'this', 'throw', 'try', 'typeof', 'var',
		'void', 'while', 'with',
		// Words reserved for future use
		'abstract', 'boolean', 'byte', 'char', 'class', 'const', 'debugger',
		'double', 'enum', 'export', 'extends', 'final', 'float', 'goto',
		'implements', 'import', 'int', 'interface', 'long', 'native',
		'package', 'private', 'protected', 'public', 'short', 'static',
		'super', 'synchronized', 'throws', 'transient', 'volatile',
		// These are not reserved, but should be taken into account
		// in isValidIdentifier (See jslint source code)
		'arguments', 'eval', 'true', 'false', 'Infinity', 'NaN', 'null', 'undefined'
	);

	public function __construct(){
		$this->parser = new JSParser();
	}

	protected function parse($js){
		try{
			$n = $this->parser->parse($js, '', 1);
			return $this->parseTree($n);
		}catch(Exception $e){
			echo $e->getMessage() . "\n";
		}
	}
	
	/**
	 * An multi dimensional array.. the type of js particle and the string
	 * @param array $tests
	 */
	public function setTests($tests){
		$this->_tests = $tests;
	}

	/**
	 * Get the mootools dependencies of this javascript
	 * @param string $js The javascript string
	 * @return array
	 */
	public function getDependencies($js){
		$this->parse($js);
		return $this->_deps;
	}
	

	private function parseTree($n, $noBlockGrouping = false){
		
		if(isset($this->_tests[$n->type][$n->value])){
			$dep = $this->_tests[$n->type][$n->value];
			$this->_deps[$dep] = $dep;
		}
			
		switch ($n->type)
		{
			case KEYWORD_FUNCTION:
				// function ... (){}
				$this->parseTree($n->body, true);
			break;

			case JS_SCRIPT:
				// we do nothing with funDecls or varDecls
				$noBlockGrouping = true;
			// FALL THROUGH

			case JS_BLOCK:
				$childs = $n->treeNodes;
				for ($c = 0, $i = 0, $j = count($childs); $i < $j; $i++)
				{
					$t = $this->parseTree($childs[$i]);
				}

			break;

			case KEYWORD_IF:
				$this->parseTree($n->condition);
				$this->parseTree($n->thenPart);
				if($n->elsePart){
					$this->parseTree($n->elsePart);
				}
			break;

			case KEYWORD_SWITCH:
				$this->parseTree($n->discriminant);
				$cases = $n->cases;
				for ($i = 0, $j = count($cases); $i < $j; $i++)
				{
					$case = $cases[$i];
					if ($case->type == KEYWORD_CASE)
						$this->parseTree($case->caseLabel);

					$this->parseTree($case->statements, true);
				}
			break;

			case KEYWORD_FOR:
				if($n->setupp){
					$this->parseTree($n->setup);
				}
				if($n->condition){
					$this->parseTree($n->condition);
				}
				if($n->update){
					$this->parseTree($n->update);
				}
				$this->parseTree($n->body);
			break;

			case KEYWORD_WHILE:
				$this->parseTree($n->condition);
				$this->parseTree($n->body);
			break;

			case JS_FOR_IN:
				if($n->varDecl){
					$this->parseTree($n->varDecl);
				}
				if($n->iterator){
					 $this->parseTree($n->iterator);
				}
				if($n->object){
					$this->parseTree($n->object);
				}
				$this->parseTree($n->body);
			break;

			case KEYWORD_DO:
				$this->parseTree($n->body, true);
				$this->parseTree($n->condition);
			break;

			case KEYWORD_BREAK:
			case KEYWORD_CONTINUE:
				
			break;

			case KEYWORD_TRY:
				$this->parseTree($n->tryBlock, true);
				$catchClauses = $n->catchClauses;
				for ($i = 0, $j = count($catchClauses); $i < $j; $i++)
				{
					$t = $catchClauses[$i];
					if($t->guard){
						$this->parseTree($t->guard);
					}
					$this->parseTree($t->block, true);
				}
				if ($n->finallyBlock)
					$this->parseTree($n->finallyBlock, true);
			break;

			case KEYWORD_THROW:
				$this->parseTree($n->exception);
			break;

			case KEYWORD_RETURN:
				if ($n->value)
				{
					$t = $this->parseTree($n->value);
				}
			break;

			case KEYWORD_WITH:
				$this->parseTree($n->object);
				$this->parseTree($n->body);
			break;

			case KEYWORD_VAR:
			case KEYWORD_CONST:
				$childs = $n->treeNodes;
				for ($i = 0, $j = count($childs); $i < $j; $i++)
				{
					$t = $childs[$i];
					$u = $t->initializer;
					if ($u)
						$this->parseTree($u);
				}
			break;

			case KEYWORD_DEBUGGER:
				throw new Exception('NOT IMPLEMENTED: DEBUGGER');
			break;

			case TOKEN_CONDCOMMENT_START:
			case TOKEN_CONDCOMMENT_END:
				$childs = $n->treeNodes;
				for ($i = 0, $j = count($childs); $i < $j; $i++)
					$this->parseTree($childs[$i]);
			break;

			case OP_SEMICOLON:
				if ($expression = $n->expression)
					$this->parseTree($expression);
			break;

			case JS_LABEL:
				$this->parseTree($n->statement);
			break;

			case OP_COMMA:
				$childs = $n->treeNodes;
				for ($i = 0, $j = count($childs); $i < $j; $i++)
					$this->parseTree($childs[$i]);
			break;

			case OP_ASSIGN:
				$this->parseTree($n->treeNodes[0]);
				$this->parseTree($n->treeNodes[1]);
			break;

			case OP_HOOK:
				$this->parseTree($n->treeNodes[0]);
				$this->parseTree($n->treeNodes[1]);
				$this->parseTree($n->treeNodes[2]);
			break;

			case OP_OR: case OP_AND:
			case OP_BITWISE_OR: case OP_BITWISE_XOR: case OP_BITWISE_AND:
			case OP_EQ: case OP_NE: case OP_STRICT_EQ: case OP_STRICT_NE:
			case OP_LT: case OP_LE: case OP_GE: case OP_GT:
			case OP_LSH: case OP_RSH: case OP_URSH:
			case OP_MUL: case OP_DIV: case OP_MOD:
				$this->parseTree($n->treeNodes[0]);
				$this->parseTree($n->treeNodes[1]);
			break;

			case OP_PLUS:
			case OP_MINUS:
				$left = $this->parseTree($n->treeNodes[0]);
				$right = $this->parseTree($n->treeNodes[1]);
			break;

			case KEYWORD_IN:
				$this->parseTree($n->treeNodes[0]);
				$this->parseTree($n->treeNodes[1]);
			break;

			case KEYWORD_INSTANCEOF:
				$this->parseTree($n->treeNodes[0]);
				$this->parseTree($n->treeNodes[1]);
			break;

			case KEYWORD_DELETE:
				$this->parseTree($n->treeNodes[0]);
			break;

			case KEYWORD_VOID:
				$this->parseTree($n->treeNodes[0]);
			break;

			case KEYWORD_TYPEOF:
				$this->parseTree($n->treeNodes[0]);
			break;

			case OP_NOT:
			case OP_BITWISE_NOT:
			case OP_UNARY_PLUS:
			case OP_UNARY_MINUS:
				$this->parseTree($n->treeNodes[0]);
			break;

			case OP_INCREMENT:
			case OP_DECREMENT:
				if ($n->postfix)
					$this->parseTree($n->treeNodes[0]);
				else
					$this->parseTree($n->treeNodes[0]);
			break;

			case OP_DOT:
				$this->parseTree($n->treeNodes[0]);
				$this->parseTree($n->treeNodes[1]);
			break;

			case JS_INDEX:
				$this->parseTree($n->treeNodes[0]);
				// See if we can replace named index with a dot saving 3 bytes
				if (	$n->treeNodes[0]->type == TOKEN_IDENTIFIER &&
					$n->treeNodes[1]->type == TOKEN_STRING &&
					$this->isValidIdentifier(substr($n->treeNodes[1]->value, 1, -1))
				){
					//
				}else{
					$this->parseTree($n->treeNodes[1]);
				}
			break;

			case JS_LIST:
				$childs = $n->treeNodes;
				for ($i = 0, $j = count($childs); $i < $j; $i++)
					$this->parseTree($childs[$i]);
			break;

			case JS_CALL:
				$this->parseTree($n->treeNodes[0]);
				$this->parseTree($n->treeNodes[1]);
			break;

			case KEYWORD_NEW:
			case JS_NEW_WITH_ARGS:
				$this->parseTree($n->treeNodes[0]);
				if($n->type == JS_NEW_WITH_ARGS){
					$this->parseTree($n->treeNodes[1]);
				}
			break;

			case JS_ARRAY_INIT:
				$childs = $n->treeNodes;
				for ($i = 0, $j = count($childs); $i < $j; $i++)
				{
					$this->parseTree($childs[$i]);
				}
			break;

			case JS_OBJECT_INIT:
				$childs = $n->treeNodes;
				for ($i = 0, $j = count($childs); $i < $j; $i++)
				{
					$t = $childs[$i];
					if ($t->type == JS_PROPERTY_INIT)
					{
						$this->parseTree($t->treeNodes[1]);
					}
					else
					{
						$this->parseTree($t->body, true);
					}
				}
			break;

			case KEYWORD_NULL: case KEYWORD_THIS: case KEYWORD_TRUE: case KEYWORD_FALSE:
			case TOKEN_IDENTIFIER: case TOKEN_NUMBER: case TOKEN_STRING: case TOKEN_REGEXP:
				//
			break;

			case JS_GROUP:
				$this->parseTree($n->treeNodes[0]);
			break;

			default:
				throw new Exception('UNKNOWN TOKEN TYPE: ' . $n->type);
		}
	}

	private function isValidIdentifier($string)
	{
		return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $string) && !in_array($string, $this->reserved);
	}
}
