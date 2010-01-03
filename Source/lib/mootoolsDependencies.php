<?php

define('METHOD','.');
define('VARIABLE',3);
define('STRING',4);

if(!defined('PHP_EOL')){
	define('PHP_EOL',"\n");
}

$deps = array();

// Core 
foreach(explode(PHP_EOL,'chk
clear
defined
arguments
empty
lambda
extend
merge
each
pick
random
splat
time
try
type') as $dollarVar){
	$deps[VARIABLE]['$'.$dollarVar] = 'Core';
}

// Browser
$deps[VARIABLE]['Browser'] = 'Browser';

// Array
foreach(explode(PHP_EOL,'each
every
filter
clean
indexOf
map
some
associate
link
contains
extend
getLast
getRandom
include
combine
erase
empty
flatten
hexToRgb
rgbToHex') as $method){
	$deps[METHOD][$method] = 'Array';
}

$deps[VARIABLE]['$A'] = 'Array';

// Function
foreach(explode(PHP_EOL,'create
pass
attempt
bind
bindWithEvent
delay
periodical
run') as $method){
	$deps[METHOD][$method] = 'Function';
}

// Number
foreach(explode(PHP_EOL,'limit
round
times
toFloat
toInt') as $method){
	$deps[METHOD][$method] = 'Number';
}

// String
foreach(explode(PHP_EOL,'test
contains
trim
clean
camelCase
hyphenate
capitalize
escapeRegExp
toInt
toFloat
hexToRgb
rgbToHex
stripScripts
substitute') as $method){
	$deps[METHOD][$method] = 'String';
}

// Hash
foreach(explode(PHP_EOL,'constructor
each
has
keyOf
hasValue
extend
combine
erase
get
set
empty
include
map
filter
every
some
getClean
getKeys
getValues
getLength
toQueryString') as $method){
	$deps[METHOD][$method] = 'Hash';
}

$deps[VARIABLE]['$H'] = 'Hash';
$deps[VARIABLE]['Hash'] = 'Hash';

// Events
$deps[VARIABLE]['Event'] = 'Event';

foreach(explode(PHP_EOL,'stop
stopPropagation
preventDefault
key
Keys') as $method){
	$deps[METHOD][$method] = 'Event';
}

// Class
$deps[VARIABLE]['Class'] = 'Class';
$deps[METHOD]['implement'] = 'Class';

// Class.Extras
$deps[VARIABLE]['Chain'] = 'Class.Extras';

foreach(explode(PHP_EOL,'chain
callChain
clearChain') as $method){
	$deps[METHOD][$method] = 'Class.Extras';
}

$deps[VARIABLE]['Events'] = 'Class.Extras';

foreach(explode(PHP_EOL,'addEvent
addEvents
fireEvent
removeEvent
removeEvents') as $method){
	// This will intefere with Element.Event.. just looking for the Events 
	// keyword will be enough
	//$deps[METHOD][$method] = 'Class.Extras';
}

$deps[VARIABLE]['Options'] = 'Class.Extras';
$deps[METHOD]['setOptions'] = 'Class.Extras';

// Element
$deps[VARIABLE]['$'] = 'Element';
$deps[VARIABLE]['$$'] = 'Element';
$deps[VARIABLE]['Element'] = 'Element';

foreach(explode(PHP_EOL,'constructor
getElement
getElements
getElementById
set
get
erase
match
inject
grab
adopt
wraps
appendText
dispose
clone
replaces
hasClass
addClass
removeClass
toggleClass
getPrevious
getAllPrevious
getNext
getAllNext
getFirst
getLast
getParent
getParents
getChildren
hasChild
empty
destroy
toQueryString
getSelected
getProperty
getProperties
setProperty
setProperties
removeProperty
removeProperties
store
retrieve
eliminate') as $method){
	$deps[METHOD][$method] = 'Element';
}

$deps[VARIABLE]['IFrame'] = 'Element';
$deps[VARIABLE]['Elements'] = 'Element';
$deps[METHOD]['filter'] = 'Element';

// Element.Event

foreach(explode(PHP_EOL,'addEvent
removeEvent
addEvents
removeEvents
fireEvent
cloneEvents') as $method){
	$deps[METHOD][$method] = 'Element.Event';
}

// Element.Style

foreach(explode(PHP_EOL,'setStyle
getStyle
setStyles
getStyles') as $method){
	$deps[METHOD][$method] = 'Element.Style';
}

// Element.Dimensions

foreach(explode(PHP_EOL,'scrollTo
getSize
getScrollSize
getScroll
getPosition
setPosition
getCoordinates
getOffsetParent') as $method){
	$deps[METHOD][$method] = 'Element.Dimensions';
}

// Selectors

foreach(explode(PHP_EOL,'getElements
getElement') as $method){
	$deps[METHOD][$method] = 'Selectors';
}

// DomReady
$deps[STRING]["'domready'"] = 'DomReady';
$deps[STRING]['"domready"'] = 'DomReady';

// JSON

$deps[VARIABLE]['JSON'] = 'JSON';

// Cookie

$deps[VARIABLE]['Cookie'] = 'Cookie';

// Swiff

$deps[VARIABLE]['Swiff'] = 'Swiff';

// Fx

$deps[VARIABLE]['Fx'] = 'Fx';

// Fx.Tween

$deps[METHOD]['Tween'] = 'Fx.Tween';
foreach(explode(PHP_EOL,'tween
fade
highlight') as $method){
	$deps[METHOD][$method] = 'Fx.Tween';
}

// Fx.Morph

$deps[METHOD]['Morph'] = 'Fx.Morph';
$deps[METHOD]['morph'] = 'Fx.Morph';

// Fx.Transistions

$deps[METHOD]['Transitions'] = 'Fx.Transitions';

// Request

$deps[VARIABLE]['Request'] = 'Request';
$deps[METHOD]['send'] = 'Request';

// Request.HTML

$deps[METHOD]['HTML'] = 'Request.HTML';
$deps[METHOD]['load'] = 'Request.HTML';

// Request.JSON

$deps[METHOD]['JSON'] = 'Request.JSON';

return $deps;