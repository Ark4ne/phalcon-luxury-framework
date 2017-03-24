<?php

namespace Test\Repositories;

use Neutrino\Constants\Services;
use Neutrino\Model;
use Neutrino\Repositories\Repository;
use Phalcon\Db\Column;
use Test\TestCase\TestCase;

class RepositoryTest extends TestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage StubWrongRepository must have a $modelClass.
     */
    public function testWrongContructor()
    {
        new StubWrongRepository;
    }

    public function testContructor()
    {
        $this->assertInstanceOf(StubRepository::class, new StubRepository);
    }

    public function testCount()
    {
        $this->mockCount(1);

        $repository = new StubRepository;

        $this->assertEquals(1, $repository->count());
    }

    public function dataAll()
    {
        return [
            [[]],
            [[['id' => 1, 'name' => 't1'], ['id' => 2, 'name' => 't2'], ['id' => 3, 'name' => 't3']]]
        ];
    }

    /**
     * @dataProvider dataAll
     */
    public function testAll($data)
    {
        $this->mockDb(count($data), $data);

        $repository = new StubRepository;

        $result = $repository->all();

        $this->assertInstanceOf(\Phalcon\Mvc\Model\ResultsetInterface::class, $result);
        $this->assertEquals($data, $result->toArray());

        foreach ($result as $item) {
            $this->assertInstanceOf(StubModelTest::class, $item);
        }
    }

    public function testFirst()
    {
        $this->mockDb(count([['id' => 1, 'name' => 't1']]), [['id' => 1, 'name' => 't1']]);

        $repository = new StubRepository;

        $result = $repository->first();

        $this->assertInstanceOf(StubModelTest::class, $result);

        $this->assertEquals(['id' => 1, 'name' => 't1'], $result->toArray());
    }

    /**
     * @dataProvider dataAll
     */
    public function testFind($data)
    {
        $this->mockDb(count($data), $data);

        $repository = new StubRepository;

        $result = $repository->find();

        $this->assertInstanceOf(\Phalcon\Mvc\Model\ResultsetInterface::class, $result);
        $this->assertEquals($data, $result->toArray());

        foreach ($result as $key => $item) {
            $this->assertInstanceOf(StubModelTest::class, $item);
            $this->assertEquals($data[$key], $item->toArray());
        }
    }

    public function testSave()
    {
        $this->mockDb(0, null);
        $repository = new StubRepository;

        $model = new StubModelTest;

        $this->assertTrue($repository->save($model));
        $this->assertTrue($repository->save([$model, $model]));
    }

    public function testUpdate()
    {
        $this->mockDb(0, null);
        $repository = new StubRepository;

        $model = new StubModelTest;

        $this->assertTrue($repository->update($model));
        $this->assertTrue($repository->update([$model, $model]));
    }

    public function testDelete()
    {
        $this->mockDb(0, null);
        $repository = new StubRepository;

        $model = new StubModelTest;

        $this->assertTrue($repository->delete($model));
        $this->assertTrue($repository->delete([$model, $model]));
    }


    private function mockCount($number)
    {
        $this->mockDb(1, [['rowcount' => $number]]);
    }

    private function mockDb($numRows, $result)
    {
        $con = $this->mockService(Services::DB, \Phalcon\Db\Adapter\Pdo\Mysql::class, true);
        $dialect = $this->createMock(\Phalcon\Db\Dialect\Mysql::class);
        $results = $this->createMock(\Phalcon\Db\Result\Pdo::class);

        $results->expects($this->any())
            ->method('numRows')
            ->will($this->returnValue($numRows));

        $results->expects($this->any())
            ->method('fetchall')
            ->will($this->returnValue($result));

        $dialect->expects($this->any())
            ->method('select')
            ->will($this->returnValue(null));

        $con->expects($this->any())
            ->method('getDialect')
            ->will($this->returnValue($dialect));

        $con->expects($this->any())
            ->method('query')
            ->will($this->returnValue($results));

        $con->expects($this->any())
            ->method('execute');

        $con->expects($this->any())
            ->method('tableExists')
            ->will($this->returnValue(true));
    }
}

class StubWrongRepository extends Repository
{
    protected $modelClass = '';
}

class StubModelTest extends Model
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    public function initialize()
    {
        parent::initialize();

        $this->setSource("test");

        $this->primary('id', Column::TYPE_INTEGER);

        $this->column('name', Column::TYPE_VARCHAR);
    }
}

class StubRepository extends Repository
{
    protected $modelClass = StubModelTest::class;
}
