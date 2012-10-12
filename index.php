<?php
/*
	MIKI by Xavi Esteve

	Miki is the smallest wiki system ever, just one file.
	 - One single PHP file (and an htaccess)
	 - No database needed
	 - CSS and JS files generated automatically
	 - Basic Markdown-like language optimized for mobile device keyboards
	 - No images
	 - Super fast, just one server request
	 
	============= Setup ==============
	1. Place index.php and .htaccess in an empty folder
	2. Open index.php and customize it
	3. Open htaccess and write your folder path
	4. Bookmark /miki/$apppass and always use that URL when accessing for the first time
 	
	=========== Formatting ===========
	. Heading 1 .
	.. Heading 2 ..
	... Heading 3 ...
	- List item
	**bold**
	''italics''
	__small__
	    blockquote
	[internal-link]
	[http://google.com external link]
	http://external.com
	
*/

session_start();
class Miki {
	
	/****************************************************************************************
	 * Basic config
	 */
	private $appname = 'Miki';
	private $appversion = '1.15';
	private $apppass = 'JHGFkJYfbDYGbingNjhGBJFjfbgJBFJfgjbHFjHFjfkuwtyknGDFcFDSwxEWzaZWeqwQSdvFgbIplIOiMjmbhVGFFuHGGTRDeEDFyTctdgfDVtEScescTrdvFBfuYGNJgy538gjHBGfBHgfbHgBFjHGnkJmhJkjpkplPlPoPlpKpLPlpKlJKNKnKnhjHBjhGsaWAqaWSZxCXVCbvBVnBnmnmnmNKJKjJOIuUgYFteRsRdFYgUhuHkJ';
	private $extension = 'txt';
	private $maxlength = 10000; // file max characters
	private $csscachetime = 86400; // 3600 = 1 hour, 86400 = 1 day, 604800 = 1 week, 18144000 = 1 month
	
	private $url = 'http://example.com/miki'; // no trailing slash
	private $apppath = '/home/xavi/miki'; // no trailing slash
	
	
	
	
	
	
	
	
	/****************************************************************************************
	 * You are done, no more config needed :)
	 */
	 
	private $page;
	private $breadcrumb = array();

	private $css = '/* Miki CSS file */
html {background: #FBFBFB;}
body {font-family:Georgia, serif;margin: 0 auto 20px;max-width:960px;padding: 0 7%;}
.bc {color: #CCC;margin: 0;padding: 1px 20px;margin: 0 -20px;opacity:0.6;transition: opacity 0.5s ease;-webkit-transition: opacity 0.5s ease;height: 1.45em;}
	.bc:hover {opacity:1;background:#fff;}
.bc a {color:#666;font-size:66%;font-weight:100;text-decoration:none;}
	.bc .start {color:#c00;margin-top: 0;font-family: sans-serif;font-weight: bold;}
	.bc .start:hover {color:#c00;}
#box-view {font-size: 1em;line-height: 1.66em;}
	#box-view a {color:#C00;}
	.title {color: #C00;font-size: 4em;line-height: 1.33em;margin: .3em 0;text-shadow: 0 3px 4px #CCC;}
	h2,h3,h4,h5,h6 {margin: 2em 0 0.1em;padding-bottom: .1em;}
	h2 {border-bottom: 1px solid #eee;box-shadow: 0 1px 3px #EEE;color: #555;padding-bottom:.33em}
	h3 {color: #c00;text-transform: uppercase;font-family:\'Trebuchet MS\',sans-serif;font-size:1em;}
	h4 {color:#000;}
	h5 {font-family:\'Trebuchet MS\',sans-serif;text-transform:uppercase;font-size: .7em;letter-spacing: .2em;}
	h5,h6 {color:#666;}
	#box-view a {color:#c00;text-decoration:none;}
		#box-view a:hover {background:#fffacb;}
#box-edit {border-top:1px dashed #eee;height:2em;overflow:hidden;padding:5px 0;margin-top:50px;}
	#box-edit .edit {color: #666;cursor:pointer;padding:1em;opacity:0.3;font-size: 66%;height:2em;margin: 0 0 10px 0;}
		#box-edit .edit:hover {opacity:0.9}
	#box-edit textarea {border:none;height:500px;font-family:Georgia,sans-serif;font-size:1em;line-height:1.66em;padding:.66em;width:90%;}
	#box-edit input {width: 99%;}
	pre {background: #EEE;color: #008200;overflow-x: scroll;padding: .33em .66em;line-height: 1.33em;border-radius: .5em;tab-size:2;border: 1px solid #DDD;}
	blockquote {font-style:italic;font-size:.95em;color:#666;padding:.66em 1em;margin:0 .66em;}
	#box-edit input[type=submit] {border: none;background: #C00;border-radius: 1em;padding: .66em;width: 92.3%;opacity: 0.3;font-size: 1em;font-family: Georgia,serif;color: white;font-weight: 900;cursor:pointer;}
	.allfiles {list-style:none;padding:0;}
		.allfiles li {display: inline;}
			.allfiles a {padding: 0 1em 1em 0;text-decoration: none;font-size: 80%;color: #666;}

/* Responsive Design */
@media screen and (max-width:520px) { /* Mobile */
	body {font-size:80%;padding: 0 3px;}
	#box-edit textarea {height:150px;}
}
';
	private $js = '/* Miki JS file */
';
	private $template = array(
		"page" => '<!DOCTYPE html>
<html>
	<head>
		<title>[title]</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/> 
		<link rel="stylesheet" type="text/css" media="screen" href="[url]/index.php?special=css&v=[version]" />
		<link rel="apple-touch-icon" href="[url]/miki-logo.png" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
		<link rel="shortcut icon" href="[url]/miki-logo.png">
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
		<ul class="allfiles">[allfiles]</ul>
		<!-- <script type="text/javascript" src="[url]/index.php?special=js&v=[version]"></script> -->
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

		// Password protected page
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
		$files = array();
		$allfiles = scandir($this->apppath.'/');
		foreach ($allfiles as $allfile) {
			if ( substr($allfile, -(strlen($this->extension)+1)) == ".".$this->extension ) {
				array_push($files, $allfile);
			}
		}
		$files = $this->arraytoli($files);
		return $files;
	}
	// Converts the array into a <li> list
	private function arraytoli($array) {
		$output = '';
		foreach ($array as $item) {
			$name = substr($item, 0, -4);
			$output .= '<li><a href="'.$this->url.'/'.$name.'">'.$name.'</li>';
		}
		return $output;
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
			"allfiles" => $this->listfiles(),
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
		$text = preg_replace('#\.\.\.\.\.\. ?(.*?) ?\.\.\.\.\.\.#s', '<h6>$1</h6>', $text);
		$text = preg_replace('#\.\.\.\.\. ?(.*?) ?\.\.\.\.\.#s', '<h5>$1</h5>', $text);
		$text = preg_replace('#\.\.\.\. ?(.*?) ?\.\.\.\.#s', '<h4>$1</h4>', $text);
		$text = preg_replace('#\.\.\. ?(.*?) ?\.\.\.#s', '<h3>$1</h3>', $text);
		$text = preg_replace('#\.\. ?(.*?) ?\.\.#s', '<h2>$1</h2>', $text);
		// Links
		$text = preg_replace('#\[http:\/\/([^\s]*) (.*?)\]#s', '<a href="http://$1" title="$1">$2</a> ', $text); // external
		$text = preg_replace('#[^"]http:\/\/([^\s]*)#s', ' <a href="http://$1" title="$1">$1</a> ', $text); // raw link
		$text = preg_replace('#\[([^\s]*)\]#s', '<a href="'.$this->url.'/$1" title="$1">$1</a>', $text); // internal
		// Format
		$text = preg_replace('#\*\*(.*?)\*\*#s', '<strong>$1</strong>', $text);
		$text = preg_replace('#\'\'(.*?)\'\'#s', '<em>$1</em>', $text);
		$text = preg_replace('#__(.*?)__#s', '<small>$1</small>', $text);
		// Blockquote
		$text = preg_replace('#\n    (.*?)\r#', '<blockquote>$1</blockquote>', $text);
		// List
		$text = preg_replace('#- (.*?)\r#s', '<li>$1</li>', $text);
		// Code
		$text = preg_replace('#{{(.*?)}}#s', '<pre>$1</pre>', $text);

		// Paragraphs for everything else
		$text = preg_replace('#\n([^<].*[^>])\r#', '<p>$1</p>', $text);

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
		if (sizeof($_SESSION['breadcrumb'])>10) {
			$_SESSION['breadcrumb'] = array_slice($_SESSION['breadcrumb'], 9);
		}
	}


	/******************************************************************************************
	 * Update the breadcrumb
	 */
	private function get_breadcrumb() {
		$output = '';
		// Don't show last one (current)
		$breadcrumb = $_SESSION['breadcrumb'];
		$output = '<a class="start" href="'.$this->url.'/welcome" title="Home">miki</a> &nbsp; &nbsp; ';
		foreach ($breadcrumb as $crumb) {
			$output = $output.'<a href="'.$this->url.'/'.$crumb.'" title="'.$crumb.'">'.$crumb.'</a> &nbsp; &nbsp; ';
		}
		
		return $output;
	}


	
}
$m = new Miki;
