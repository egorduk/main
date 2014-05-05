<?php 

class JqueryGrid_JqueryGrid
{
	
	protected $_config = array();

	/**
	 * jQuery selector. Special for Grid
	 *
	 * @var unknown_type
	*/
	protected $_gridSelector = '#list';

	/**
	 * An associative array of files to include into HTML
	 *
	 * @var assoc array
	 */
	protected $_files = array(
			'base'=> '/main/public/js/jqGrid/js/grid.base.js'
	);

	/**
	 * Path to Grid themes
	 *
	 * @var string
	*/
	protected $_themesPath = '/main/public/design/css/jqgrid/themes/';

	/**
	 * Current Grid theme name
	 *
	 * @var string
	 */
	protected $_theme = 'basic';

	/**
	 * jQuery
	 *
	 * @var string
	 */
	protected $_jqueryFile = '/main/public/js/jQuery.js';

	/**
	 * Class constructor
	 *
	 * @param array $config
	 * @param array $files
	 * @return Fooup_jQuery_Grid
	 */
	public function __construct(array $config = null, array $files = null) {

		if (!is_null($config)) $this->setConfig($config);
		if (!is_null($files)) $this->setFiles($files);
		return $this;
	}

	/**
	 * Sets the jQuery file
	 *
	 * @param string $file
	 * @return bool
	 */
	public function setjQueryFile($file) {
		$file = trim($file);
		if (empty($file)) return false;
		$this->_jqueryFile = $file;
		return true;
	}

	/**
	 * Retrieve jQuery file
	 *
	 * @return string
	 */
	public function getjQueryFile() {
		return $this->_jqueryFile;
	}

	/**
	 * Sets the class configuration
	 *
	 * @param array $config
	 * @return Fooup_jQuery_Grid
	 */
	public function setConfig(array $config) {
		$this->_config = $config;
		return $this;
	}

	/**
	 * Gets the class configuration
	 *
	 * @return array
	 */
	public function getConfig() {
		return $this->_config;
	}

	/**
	 * Sets file value for concrete array key
	 *
	 * @param string $key
	 * @param string $file
	 * @return Fooup_jQuery_Grid
	 */
	public function setFile($key, $file) {
		$this->_files[$key] = $file;
		return $this;
	}

	/**
	 * Gets the file by a key
	 *
	 * @param string $key
	 * @return string
	 */
	public function getFile(string $key) {
		return $this->_files[$key];
	}

	/**
	 * Set class files configuration
	 *
	 * @param array $files
	 * @return Fooup_jQuery_Grid
	 */
	public function setFiles(array $files) {
		if (!empty($files)) {
			foreach ($files as $key=>$file) {
				$this->setFile($key, $file);
			}
		};
		return $this;
	}

	/**
	 * Gets class files configuration
	 *
	 * @return unknown
	 */
	public function getFiles() {
		return $this->_files;
	}

	/**
	 * Gets themes path
	 *
	 * @return string
	 */
	public function getThemesPath() {
		return $this->_themesPath;
	}

	/**
	 * Sets the theme path
	 *
	 * @param string $path
	 * @return Fooup_jQuery_Grid
	 */
	public function setThemesPath(string $path) {
		if ($path !== '') {
			if ($path[strlen($path)-1] !== '/') $path.='/';
			$this->_themesPath = $path;
		}
		return $this;
	}

	/**
	 * Sets the current theme name
	 *
	 * @param string $theme
	 * @return Fooup_jQuery_Grid
	 */
	public function setTheme($theme = '') {
		if ($theme !== '') $this->_theme = $theme;
		return $this;
	}

	/**
	 * Gets the current theme name
	 *
	 * @return string
	 */
	public function getTheme() {
		return $this->_theme;
	}

	/**
	 * Sets the grid selector class or id
	 *
	 * @param string $selector
	 * @return Fooup_jQuery_Grid
	 */
	public function setGridSelector($selector) {
		if ($selector !== '') $this->_gridSelector = $selector;
		return $this;
	}

	/**
	 * Gets the grid selector
	 *
	 * @return string
	 */
	public function getGridSelector() {
		return $this->_gridSelector;
	}
	/**
	 * Populates an jqGrid scripts and css
	 *
	 * @param Zend_View $view
	 * @return Fooup_jQuery_Grid
	 */
	public function populate(Zend_View_Interface $view) 
	{
		$view->headScript()->appendFile($this->getjQueryFile());
		
		foreach ($this->getFiles() as $key=>$file) 
		{
			$view->headScript()->appendFile($file);
		}
		
		$view->headLink()->appendStylesheet($this->getThemesPath().$this->getTheme().'/grid.css');
		$view->headLink()->appendStylesheet($this->getThemesPath().'jqModal.css');
		$config = $this->getConfig();
		$json = Zend_Json::encode($config);
		$view->headScript()->appendScript('jQuery(document).ready(function(){jQuery("'.$this->getGridSelector().'").jqGrid('.$json.');})');
		
		return $this;
	}
}