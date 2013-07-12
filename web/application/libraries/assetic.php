<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

spl_autoload_register(function($className) {
	if(strpos($className, 'Assetic') === 0)
		require APPPATH .'third_party/' . str_replace('\\', '/', $className.'.php');
});

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\StringAsset;
use Assetic\Asset\HttpAsset;
use Assetic\Asset\GlobAsset;
use Assetic\Filter\LessFilter;
use Assetic\Filter\Yui;
use Assetic\AssetWriter;
use Assetic\AssetManager;

class CI_Assetic {
	var $CI;
	var $config 	= array();
	var $js;
	var $css;
	var $writer;

	function __construct() {
		$this->CI =& get_instance();
		// Loads the assetic config (assetic.php under ./system/application/config/)
		$this->CI->load->config('assetic');
		$tmp_config =& get_config();

		if (count($tmp_config['assetic']) > 0) {
			$this->config = $tmp_config['assetic'];
			unset ($tmp_config);
		} else
			$this->_error('assetic configuration error');

		$this->CI->load->helper('url');

		$this->collections['js'] = array();
		$this->collections['css'] = array();

		foreach($this->config['js']['autoload'] as $filename)
			$this->addJs($filename);

		foreach($this->config['css']['autoload'] as $filename)
			$this->addCss($filename);
	}

	protected function addAsset($asset, $type, $group) {
		if($group == null)
			$group = $this->config[$type]['default-group'];

		$group .= '.'.$type;
		if(!isset($this->collections[$type][$group]))
			$this->collections[$type][$group] = new AssetCollection();
		$this->collections[$type][$group]->add($asset);
	}

	public function addJs($filename, $group = null) {
		if(parse_url($filename, PHP_URL_SCHEME) === null && strpos($filename, '//:') !== 0)
			$asset = new FileAsset($filename);
		else
			$asset = new HttpAsset($filename);

		$this->addAsset($asset, 'js', $group);
	}

	public function addJsDir($path, $group = null) {
		$this->addAsset( new GlobAsset($path) , 'js', $group);
	}

	public function addScript($script, $group = null) {
		$this->addAsset( new StringAsset($script) , 'js', $group);
	}

	public function getJs() {
		return $this->collections['js']->dump();
	}

	public function writeJsScripts() {
		$scripts = array();
		foreach ($this->collections['js'] as $ac)
			$this->recursiveAssets($ac, $scripts);

		foreach($scripts as $script)
			if(true === $script['url'])
				echo '<script src="'.$script['content'].'"></script>'."\n";
			else
				echo '<script>'.$script['content'].'</script>'."\n";
	}

	public function writeStaticJsScripts() {
		if(!isset($this->writer) || $this->writer !== null)
			$this->writer = new AssetWriter($this->config['static']['dir']);

		$urls = array();

		foreach ($this->collections['js'] as $filename => $ac) {
			if(!file_exists($this->config['static']['dir'].$filename)) {
				$ac->setTargetPath($filename);
				$this->writer->writeAsset($ac);
			}
			$urls[] = base_url($this->config['static']['dir'].$filename);
		}

		foreach($urls as $url)
			echo '<script src="'.$url.'"></script>'."\n";
	}


	public function addCss($filename, $group = null) {
		if(parse_url($filename, PHP_URL_SCHEME) === null && strpos($filename, '//:') !== 0)
			$asset = new FileAsset($filename);
		else
			$asset = new HttpAsset($filename);

		$this->addAsset($asset, 'css', $group);
	}

	public function addCssDir($path, $group = null) {
		$this->addAsset( new GlobAsset($path) , 'css', $group);
	}

	public function addStyle($script, $group = null) {
		$this->addAsset( new StringAsset($script) , 'css', $group);
	}

	public function getCss() {
		return $this->js->dump();
	}

	public function writeCssLinks() {
		$styles = array();
		foreach ($this->collections['css'] as $ac)
			$this->recursiveAssets($ac, $styles);

		foreach($styles as $style)
			if(true === $style['url'])
				echo '<link rel="stylesheet" type="text/css" href="'.$style['content'].'" />'."\n";
			else
				echo '<style>'.$style['content'].'"</style>'."\n";
	}

	public function writeStaticCssLinks() {
		if(!isset($this->writer) || $this->writer !== null)
			$this->writer = new AssetWriter($this->config['static']['dir']);

		$urls = array();

		foreach ($this->collections['css'] as $filename => $ac) {
			if(!file_exists($this->config['static']['dir'].$filename)) {
				$ac->setTargetPath($filename);
				$this->writer->writeAsset($ac);
			}
			$urls[] = base_url($this->config['static']['dir'].$filename);	
			
		}

		foreach($urls as $url)
			echo '<link rel="stylesheet" type="text/css" href="'.$url.'" />'."\n";
	}


	private function recursiveAssets(AssetCollection $ac, &$tag) {
		foreach($ac->all() as $el) {
			if($el instanceof AssetCollection)
				$this->recursiveAssets($el, $tag);
			elseif($el instanceof StringAsset)
				$tag[] = array('url' => false, 'content' => $el->dump());
			else {
				$filename = $el->getSourceRoot().'/'.$el->getSourcePath();
				if(parse_url($filename, PHP_URL_SCHEME) === null && strpos($filename, '//:') !== 0)
					$filename = base_url($filename);
				$tag[$filename] = array('url' => true, 'content' => $filename);
			}
		}
	}
}