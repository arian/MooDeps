<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />

<title>MooDependencies Checker</title>

<link  href="style.css" rel="stylesheet" type="text/css" media="screen" />

<script src="js/mootools-1.2.3-core.js" type="text/javascript"></script>
<script src="js/iFrameFormRequest.js" type="text/javascript"></script>
<script src="js/site.js" type="text/javascript"></script>

</head>
<body>

<h1>MooTools Core Dependency Checker</h1>
<h2>Find the Moo Components of your Script</h2>


<div id="help">
	<h3>Please help!</h3>
	Please help me improve this dependency checker.
	Go to <a href="http://code.google.com/p/awfmootools/source/browse/trunk/mooDeps/">Google Code</a> and
	improve the code, fill issues and spread the word!
</div>

<p>
	This page will give you the right components you need to
	download.
</p>

<form action="deps.php" method="post" enctype="multipart/form-data">
	
	<strong>Only .js files</strong> For example: <a href="http://www.mootools.net/assets/scripts/mootools.net.js">http://www.mootools.net/assets/scripts/mootools.net.js</a>
	<div id="inputFields">
		
	</div>
	
	<p>
		<a href="#" id="addInputField">
			<img src="images/add.png" alt="Add a new Row" />
			Add a new row
		</a>		
	</p>
	
	<input type="submit" value="Check your Dependencies" />

	<p id="loading">
		<img src="images/loading.gif" alt="Loading" />
	</p>

	
</form>

<p class="download">
	<a href="http://www.mootools.net/core" target="_blank">
		<img src="images/download.png" alt="Download" />
		Download your version of MooTools
	</a>
</p>

<p class="download">
	<a href="#" id="genLink">
		<img src="images/link.png" alt="Link" />
		Generate link
	</a>
	<input type="text" id="linkText" size="80" />
</p>


<div id="list">
	<ul></ul>
</div>

<h3>Created By</h3>
<p>
	<a href="http://www.aryweb.nl">Aryweb Webdevelopment</a>
	&mdash; MooTools helped me out a lot. On this way I want to do something back
</p>

</body>
</html>