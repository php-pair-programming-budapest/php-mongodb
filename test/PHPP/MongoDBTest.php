<?php

namespace PHPPP;

use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Command;


class MongoDBTest extends \PHPUnit_Framework_TestCase {
	
	private $manager;

	protected function setUp() {
		$this->manager = new Manager('mongodb://localhost:27017');
		
	}

	public function testItWorks() {
		$this->assertNotNull($this->manager);
	}

	public function testInsertAndRead() {
		$bulk = new BulkWrite();
        $bulk->insert(["test" => 1]);
        $this->manager->executeBulkWrite("db.coll", $bulk);

		$query = new Query([], [
	            'projection' => ['_id' => 0]
	        ]);
        $cursor = $this->manager->executeQuery("db.coll", $query);
        $result = [];
        foreach ($cursor as $cur) {
        	$result[] = $cur;

        }
        $this->assertTrue(count($result) == 1);
	}

	protected function tearDown(){
		$bulk=new BulkWrite();
		$bulk->delete([]);
		$this->manager->executeBulkWrite('db.coll',$bulk);
	}

		public function testUpdate() {
		$bulk = new BulkWrite();
        $bulk->insert(['id' => 1, 'val' => 10]);
        $bulk->insert(['id' => 2, 'val' => 20]);
        $this->manager->executeBulkWrite("db.coll", $bulk);

        $update = new BulkWrite();
        $update->update(['id' => 2], ['$set' => ['val' => 30]]);
        $this->manager->executeBulkWrite("db.coll", $update);

        $this->assertEquals(10, $this->findById(1)->val);
        $this->assertEquals(30, $this->findById(2)->val);
	}

	private function findById($id) {
		$query = new Query(['id' => $id], []);
        $cursor = $this->manager->executeQuery("db.coll", $query);
        $result = [];
        foreach ($cursor as $cur) {
        	return $cur;
        }
	}

}
