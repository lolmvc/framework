<?php
namespace Lolmvc\Model;

/**
 * Abstract database class that all models will inherit from
 *
 * @abstract
 * @author mitzip <mitzip@lolmvc.com>
 * @package MVC\Model
 */
abstract class Model {

	/**
	 * Mongo server object
	 *
	 * @var Mongo
	 * @access protected
	 */
	protected $m;

	/**
	 * Selected database
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $db;

     /**
      * List of the collections available in the database
      *
      * @var array
      * @access private
      */
	 private $collections;

	/**
	 * Constructor
	 *
	 * @access protected
	 * @return void
	 */
	function __construct() {
		/*$this->m = new \Mongo(MONGO_SERVER);
		$this->db = $this->m->selectDB(MONGO_DATABASE);

		  // get the collections
		  $size = strlen(strval($this->db)) + 1;
		  $this->collections = $this->db->listCollections();
		  foreach ($this->collections as &$collection)
              $collection = substr($collection, $size);
         */
	}

     /**
      * Returns the array of collections in the database
      *
      * @access public
      * @return void
      */
	 function getCollections() {
		  return $this->collections;
	 }

     /**
      * Get magic method that allows access of collections without having to
      * know of their existance beforehand.
      *
      * @param mixed $name
      * @access public
      * @return void
      */
	 public function __get($name) {
		 if(in_array($name, $this->collections)) {
			 return $this->db->$name;
		 }
	}
}

