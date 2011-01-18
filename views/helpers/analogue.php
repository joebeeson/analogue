<?php

	/**
	 * AnalogueHelper
	 * Renames, or "maps", helpers prior to rendering so that you can easily
	 * replace helpers with your extended, or updated version.
	 * @author Joe Beeson <jbeeson@gmail.com>
	 */
	class AnalogueHelper extends AppHelper {

		/**
		 * Mappings
		 * @var array
		 * @access private
		 */
		private $mappings = array();
		
		/**
		 * View
		 * @var View
		 * @access protected
		 */
		protected $View;
		
		/**
		 * Executed prior to rendering
		 * @return null
		 * @access public
		 */
		public function beforeRender() {
			foreach ($this->mappings as $mapping) {
				extract($mapping);
				if (isset($helper) and isset($rename)) {
					$this->mapHelper($helper, $rename);
				}
			}

			parent::beforeRender();
		}
		
		/**
		 * Construction method.
		 * @param array $mappings
		 * @return null
		 * @access public
		 */
		public function __construct($mappings = array()) {
			parent::__construct();

			// Grab our View object for use later...
			$this->View = ClassRegistry::getObject('view');
			
			// Merge our mappings together...
			$this->mappings = am(
				$this->mappings,
				$mappings
			);
			
			// Add ourself to the ClassRegistry in case anyone wants to use us
			ClassRegistry::addObject('Analogue', $this);
			
		}
		
		/**
		 * Performs the mapping of a helper object to a new name. We return 
		 * boolean to indicate success.
		 * @param string $helper
		 * @param string $rename
		 * @return boolean
		 * @access public
		 */
		public function mapHelper($helper, $rename) {
			
			list($plugin, $name) = pluginSplit($helper, true);

			// Only continue if we have a valid, loaded helper
			if (!$this->_loadHelper($name, $plugin)) {
				trigger_error(
					sprintf(__('Analogue is unable to load helper %s', true), $helper),
					E_USER_ERROR
				);
				return false;
			}

			// Tell the View that it's loaded and ready it for usage...
			$this->View->loaded[$rename] = $this->View->loaded[$name];
			$this->View->{$rename} = $this->View->loaded[$name];
			return true;
		}
		
		/**
		 * Convenience method for checking if a helper is already loaded in our
		 * View object. Returns boolean to indicate.
		 * @param string $name
		 * @return boolean
		 * @access protected
		 */
		protected function _isHelperLoaded($name) {
			return isset($this->View->loaded[$name]);
		}
		
		/**
		 * Loads the requested $helper if it is not already. Returns a boolean
		 * to indicate success.
		 * @param string $name
		 * @param mixed $plugin string with dot at the end or null
		 * @return boolean
		 * @access protected
		 */
		protected function _loadHelper($name, $plugin = null) {
			if ($this->_isHelperLoaded($name)) {
				return true;
			}
			$this->View->loaded = am(
				$this->View->loaded,
					$this->View->_loadHelpers(
					$this->View->loaded,
					array($plugin . $name)
				)
			);
			return $this->_isHelperLoaded($name);
		}
		
	}