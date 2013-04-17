<?php
/**
 * Copyright 2009 Ivan Ribas
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 *
 * Add this line to your config/routes.php
 *
 *   Router::parseExtensions('xml'); 
 *
 *
 * @author    Ivan Ribas
 * @copyright Copyright 2009 Ivan Ribas
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */
App::uses('Sitemap.SitemapAppController', 'Controller');
App::uses('Xml', 'Utility');
App::uses('CakeTime', 'Utility');

class SitemapController extends SitemapAppController {

	/**
	 * Add all the models you want to include in the sitemap in the $uses var.
	 */
	 
	public $uses = array('Cheese','CheeseProducer','Term','Place');
	public $components = array('RequestHandler');
	public $helpers = array('Time');
	public $siteMap = array();

	/**
	 * Here you can set changefreq and priority for your homepage
	 */
	 
	public $sitemap_siteSettings = array(
										'changefreq' => 'daily',
										'priority' => '1.0'
										);
	
	/**
	 * Settings for your models.
	 *
	 * - changefreq
	 * - priority
	 * - field:
	 *          the model field used to build the url, defaults to 'id'
	 * - action:
	 *          the model action used to build the url, defaults to 'view'
	 * - controller
	 *          is the controller name used to build the url, defaults to 
	 *          the pluralized model name.
	 *          ej. Model: post => controller: posts
	 * - plugin
	 *          plugin name used to build the url, defaults to null
	 */
 
	 /**
		* var $sitemap_modelSettings = array(
		*                         'User' => array('changefreq' => 'weekly',
		*                                         'priority' => '0.8'
		*                           ),
		*                         'Post' => array('field' => 'slug',
		*                                         'controller' => 'posts',
		*                                         'changefreq' => 'monthly',
		*                                         'priority' => '0.9'
		*                           ));
		*/                     
 
	public $sitemap_modelSettings = array(
		'Cheese' => array('field' => 'name',
						 'changefreq' => 'daily',
						 'priority' => '0.9'
						),
		'CheeseProducer' => array('field' => 'name',
						 'changefreq' => 'daily',
						 'priority' => '0.9'
						),
		'Term' => array('field' => 'word',
						 'changefreq' => 'monthly',
						 'priority' => '0.7'
						),
		'Place' => array('field' => 'name',
						 'changefreq' => 'daily',
						 'priority' => '0.8'
						)
	);
	
	/**
	 * Initialize the plugin in beforeFilter()
	 */
	 
	public function beforeFilter() {
		Configure::write('debug',2);
		$this->__initSiteMap();
		parent::beforeFilter();
	}  

	/**
	 * Initialize the plugin
	 * gets models in $uses and sets some default values.
	 */
		 
	public function __initSiteMap() {
		foreach($this->sitemap_modelSettings as $model => $value):
			$aModelName = array();
			$modelName = (strpos($model,'.'))? substr($model,strpos($model,'.')+1) : $model;

			$aModelName['model'] = $modelName;      
			$aModelName['field'] = 'id';
			$aModelName['action'] = 'view';
			$aModelName['controller'] = strtolower(Inflector::pluralize($modelName));
			$aModelName['plugin'] = null;
			$aModelName['items'] = null;

			array_push($this->siteMap, array_merge($aModelName, $value)); 
		endforeach;
	}
	
	/**
	 * Sets the variables to be rendered in the view
	 */
	 
	public function index() {
		$siteMap = array();
		foreach($this->siteMap as $model):
			$fields = array($model['field']);
			
			if($this->$model['model']->hasField('modified')) {
				array_push($fields, 'modified');
			}
			elseif($this->$model['model']->hasField('updated')) {
				array_push($fields, 'updated');
			}
			$this->$model['model']->recursive = -1;
			$model['items'] = $this->$model['model']->find('all', array('fields'=>$fields));
			array_push($siteMap, $model);
		endforeach;
		$this->set('items', $siteMap);
		$this->set('site', $this->sitemap_siteSettings);
	}
}
?>