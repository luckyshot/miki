<?php
/*
	MIKI by Xavi Esteve

	Miki is a mini-wiki system that consists of only one core file.
	 - One single PHP file (and an htaccess)
	 - No database needed
	 - Basic Markdown language
 	
	=========== Formatting ===========
	. Heading 1 .
	.. Heading 2 ..
	... Heading 3 ...
	- List item
	**bold**
	''italics''
	[internal link]
	http://externallink.com
	<strong>Any HTML</html>
	
	
*/

session_start();
class Miki {
	
	/****************************************************************************************
	 * Basic config
	 */
	private $appname = 'Miki';
	private $appversion = '1.01';
	private $apppass = 'JHGFkJYfbDYGbingNjhGBJFjfbgJBFJfgjbHFjHFjfkuwtyknGDFcFDSwxEWzaZWeqwQSdvFgbIplIOiMjmbhVGFFuHGGTRDeEDFyTctdgfDVtEScescTrdvFBfuYGNJgy538gjHBGfBHgfbHgBFjHGnkJmhJkjpkplPlPoPlpKpLPlpKlJKNKnKnhjHBjhGsaWAqaWSZxCXVCbvBVnBnmnmnmNKJKjJOIuUgYFteRsRdFYgUhuHkJ';
	private $extension = 'txt';
	private $maxlength = 10000; // file max characters
	private $csscachetime = 86400; // 3600 = 1 hour, 86400 = 1 day, 604800 = 1 week, 18144000 = 1 month
	
	private $url = 'http://example.com/miki'; // no trailing slash
	private $apppath = '/home/xavi/miki'; // no trailing slash
	
	
	
	
	
	
	
	/****************************************************************************************
	 * You are done, no more config needed
	 */
	 
	private $page;
	private $breadcrumb = array();
	
	private $css = '
html {background: #FBFBFB;}
body {font-family:sans-serif;margin: 20px auto;max-width:1000px;padding: 0 20px;box-shadow: 0 0 10px #ccc;background: white;}
	.bc {color: #CCC;margin: 0;background: #FBFBFB;padding: 1px 20px;border-bottom: 1px solid #EEE;margin: 0 -20px;}
	.bc a {color:#666;font-size:66%;font-weight:100;text-decoration:none;}
	#box-view {font-size: 14px;}
		.title {color: #C00;margin: .3em 0;}
		h2,h3,h4,h5,h6 {margin-bottom:0;}
		h2 {border-bottom: 1px solid #eee;color: #666;padding-bottom:.1em}
		h3 {color: #C00;}
		h4,h5,h6 {color:#666;}
		#box-view a {color:#0085d5;text-decoration:none;}
		#box-view a:hover {border-bottom:1px dotted #3385d5;}
	#box-edit {border-top:1px dashed #eee;height:15px;overflow:hidden;padding:5px 0;margin-top:50px;}
		#box-edit .edit {color: #666;cursor:pointer;font-size: 66%;margin: 0 0 10px 0;}
		#box-edit textarea {font-family:monospace;height:500px;width: 99%;}
		#box-edit input {width: 99%;}
		pre {background: #EEE;color: #363;font-size: 90%;overflow-x: scroll;padding: 5px;}
		
/* Responsive Design */
@media screen and (max-width:520px) { /* Mobile */
	body {padding: 0 3px;}
	#box-edit textarea {height:150px;width: 95%;}
}
';
private $js = '
var a=document.getElementsByTagName("a");
for(var i=0;i<a.length;i++) {
	a[i].onclick=function() {
		window.location=this.getAttribute("href");
		return false;
	}
}
';
	private $template = array(
		"page" => '<!DOCTYPE html>
<html>
	<head>
		<title>[title]</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/> 
		<link rel="stylesheet" type="text/css" media="screen" href="[url]/index.php?special=css&v=[version]" />
		<link rel="apple-touch-icon" href="miki-logo.png" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
		<link rel="shortcut icon" href="miki-logo.png">
	<head>
	<body>
		<p class="bc">[breadcrumb]</p>
		<div id="box-view">
			<h1 class="title">[filename]</h1>
			[content]
		</div>
		<div id="box-edit" onclick="document.getElementById(\'box-edit\').style.height=\'auto\';document.getElementById(\'text\').focus();window.location.hash=\'text\';">
			<p class="edit">Edit</p>
			<form action="[url]/index.php?p=[filename]" method="post">
				<textarea id="text" name="text">[contentraw]</textarea>
				<input type="submit" value="Save" />
			</form>
		</div>
		<script type="text/javascript" src="[url]/index.php?special=js&v=[version]"></script>
	</body>
</html>',
	);
	
	
	
	/******************************************************************************************
	 * Magic starts here
	 */
	public function __construct() {
		// Get page
		if (!isset($_GET['p'])) {
			$_GET['p'] = 'welcome';
		// Password protected site?
		}else if (isset($_GET['p']) AND $_GET['p']==$this->apppass) {
			$_SESSION['pass'] = $_GET['p'];
			$_GET['p'] = 'welcome';
		}

		if ($this->apppass AND $_SESSION['pass'] != $this->apppass) {
			die('<h1 style="font-size: 1000%;font-family: sans-serif;margin-top: 15%;text-transform: lowercase;letter-spacing: -.05em;text-align: center;color: #C00;text-shadow: 0 2px 10px #CCC;">'.$this->appname.'</h1>');
		}
		
		// CSS file?
		if (isset($_GET['special']) && $_GET['special'] == 'css') {
			header("Expires: ".gmdate("D, d M Y H:i:s", time() + $this->csscachetime)." GMT");
			header("Pragma: cache");
			header("Cache-Control: max-age=".$this->csscachetime);
			header('Content-type: text/css');
			echo $this->css;
			die();
		}
		// JavaScript file?
		if (isset($_GET['special']) && $_GET['special'] == 'js') {
			header("Expires: ".gmdate("D, d M Y H:i:s", time() + $this->csscachetime)." GMT");
			header("Pragma: cache");
			header("Cache-Control: max-age=".$this->csscachetime);
			header('Content-type: application/x-javascript');
			echo $this->js;
			die();
		}
		
		$this->page = preg_replace("[^a-z0-9\-]", "", strtolower($_GET['p']));
		
		// Editing page?
		if (isset($_POST['text']) AND $this->page) {
			$this->savefile($this->page, $_POST['text']);
			$this->gotofile($this->page);
		}
		
		// Load page
		if (!$this->page) {
			if (!$this->is_file_created('welcome')) {
				$this->createfile('welcome', "This is the ''Welcome'' file, *Edit* this file to start.");
			}
			$this->gotofile('welcome');
		}else{
			$this->update_breadcrumb($this->page);
			echo $this->view($this->page);
		}
	}
	
	
	
	/******************************************************************************************
	 * Returns an array of the files
	 */
	private function listfiles() {
		// @@@
	}
	
	
	
	/******************************************************************************************
	 * Generate the View
	 */
	private function view($filename) {
		$output = $this->template['page'];
		$array = array(
			"title" => $filename." - ".$this->appname,
			"filename" => $filename,
			"content" => $this->markdown_to_html($this->loadfile($filename)),
			"contentraw" => $this->loadfile($filename),
			"appname" => $this->appname,
			"url" => $this->url,
			"version" => $this->appversion,
			"breadcrumb" => $this->get_breadcrumb(),
		);
		foreach ($array as $key => $value) {
			$output = str_replace("[".$key."]", $value, $output);
		}
		return $output;
	}



	/******************************************************************************************
	 * Create the file
	 */
	private function savefile($filename, $text = '') {
		if ($text) {
			$filename = strtolower(preg_replace("[^A-Za-z0-9\-]", "", $filename).".".$this->extension);
			$filehandle = fopen($this->apppath."/".$filename, 'w') or die("Error: Can't create or save files");
			$fwrite = fwrite($filehandle, substr($text, 0, $this->maxlength));
			fclose($filehandle);
			if ($fwrite === false) {
				return $fwrite;
			}else{
				return true;
			}
		}else{
			// no text, delete file
			if (file_exists(preg_replace("[^A-Za-z0-9\-]", "", $filename).".".$this->extension)) {
				unlink($this->apppath."/".preg_replace("[^A-Za-z0-9\-]", "", $filename).".".$this->extension);
			}
		}
	}
	
	
	
	/******************************************************************************************
	 * Load contents of a file
	 */
	private function loadfile($filename) {
		if (file_exists(preg_replace("[^A-Za-z0-9\-]", "", $filename).".".$this->extension)) {
			return file_get_contents(preg_replace("[^A-Za-z0-9\-]", "", $filename).".".$this->extension);
		}else{
			return '';
		}
	}
	
	
	/******************************************************************************************
	 * Redirects to the specified file
	 */
	private function gotofile($filename) {
		header("Location: ".$this->url."/".strtolower($filename)."/");
	}
	
	
	
	/******************************************************************************************
	 * Format markdown to HTML
	 */
	private function markdown_to_html($text) {
		$text = htmlentities($text);
		// Headings
		$text = preg_replace('#\.\.\.\.\.\.\s(.*?) \.\.\.\.\.\.#s', '<h6>$1</h6>', $text);
		$text = preg_replace('#\.\.\.\.\. (.*?) \.\.\.\.\.#s', '<h5>$1</h5>', $text);
		$text = preg_replace('#\.\.\.\. (.*?) \.\.\.\.#s', '<h4>$1</h4>', $text);
		$text = preg_replace('#\.\.\. (.*?) \.\.\.#s', '<h3>$1</h3>', $text);
		$text = preg_replace('#\.\. (.*?) \.\.#s', '<h2>$1</h2>', $text);
		// Links
		$text = preg_replace('#http:\/\/([A-Z0-9a-z\.\-\/]*)#s', '<a href="http://$1" title="$1">http://$1</a> ', $text);
		$text = preg_replace('#\[([A-Za-z0-9\-]*?)\]#s', '<a href="'.$this->url.'/$1" title="$1">$1</a>', $text);
		// Format
		$text = preg_replace('#\*\*(.*?)\*\*#s', '<strong>$1</strong>', $text);
		$text = preg_replace('#\'\'(.*?)\'\'#s', '<em>$1</em>', $text);
		// List
		$text = preg_replace('#- (.*?)\r#s', '<li>$1</li>', $text);
		// Code
		$text = preg_replace('#{{(.*?)}}#s', '<pre>$1</pre>', $text);
		// Line breaks
		$text = preg_replace('#\r\n\r\n#s', '<br />', $text);

		return $text;
	}
	
	
	
	/******************************************************************************************
	 * Check if file has been created
	 */
	private function is_file_created($filename) {
		if (file_exists(strtolower($filename).'.'.$this->extension)) {
			return true;
		}else{
			return false;
		}
	}
	
	
	
	
	
	/******************************************************************************************
	 * Update the breadcrumb
	 */
	private function update_breadcrumb($filename) {
		if (!isset($_SESSION['breadcrumb'])) {$_SESSION['breadcrumb'] = array();}
		
		if (!in_array($filename, $_SESSION['breadcrumb'])) {
			$_SESSION['breadcrumb'] = array_merge(array($filename), $_SESSION['breadcrumb']);
		}
		if (sizeof($_SESSION['breadcrumb'])>4) {
			$_SESSION['breadcrumb'] = array_slice($_SESSION['breadcrumb'], 0, 4);
		}
	}

	/******************************************************************************************
	 * Update the breadcrumb
	 */
	private function get_breadcrumb() {
		$output = '';
		// Don't show last one (current)
		$breadcrumb = $_SESSION['breadcrumb'];
		array_pop($breadcrumb);
		foreach ($breadcrumb as $crumb) {
			$output = '<a href="'.$this->url.'/'.$crumb.'">'.$crumb.'</a> &nbsp; '.$output;
		}
		$output = '<a href="'.$this->url.'/welcome">&#8962;</a> &nbsp; '.$output;
		return $output;
	}


	
}
$m = new Miki;
