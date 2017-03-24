<?php

namespace Test\Models;

use Neutrino\Model;
use Phalcon\Db\Column;
use Phalcon\Mvc\Model\MetaData;
use Test\TestCase\TestCase;

class ModelTest extends TestCase
{
    /**
     * @var Model
     */
    private $mockModel;

    public function setUp()
    {
        parent::setUp();

        $this->setStaticValueProperty(Model::class, 'columnsMapClass', []);
        $this->setStaticValueProperty(Model::class, 'metaDatasClass', []);
        $this->mockModel = null;
    }

    /**
     * @return Model
     */
    public function getMockModel()
    {
        if ($this->mockModel == null)
            /** @var Model $model */
            $this->mockModel = $this->getMockForAbstractClass(Model::class);

        return $this->mockModel;
    }

    public function assertColumnBind($name, $expectedBind, $numeric, $class = Model::class)
    {
        $meta = $this->getStaticValueProperty(Model::class, 'metaDatasClass')[$class];

        $bind = $meta[MetaData::MODELS_DATA_TYPES_BIND][$name];

        $this->assertEquals($expectedBind, $bind);

        if ($numeric) {
            $this->assertArrayHasKey(MetaData::MODELS_DATA_TYPES_NUMERIC, $meta);
            $this->assertArrayHasKey($name, $meta[MetaData::MODELS_DATA_TYPES_NUMERIC]);
        } elseif (isset($meta[MetaData::MODELS_DATA_TYPES_NUMERIC])) {
            $this->assertArrayNotHasKey($name, $meta[MetaData::MODELS_DATA_TYPES_NUMERIC]);
        }
    }

    public function assertColumnAdded($name, $type, $expectedBind, $numeric, $class = Model::class)
    {

        $meta = $this->getStaticValueProperty(Model::class, 'metaDatasClass')[$class];

        $this->assertColumnBind($name, $expectedBind, $numeric, $class);

        $this->assertArrayHasKey(MetaData::MODELS_ATTRIBUTES, $meta);
        $this->assertTrue(in_array($name, $meta[MetaData::MODELS_ATTRIBUTES]));

        $this->assertArrayHasKey(MetaData::MODELS_DATA_TYPES, $meta);
        $this->assertArrayHasKey($name, $meta[MetaData::MODELS_DATA_TYPES]);
        $this->assertEquals($type, $meta[MetaData::MODELS_DATA_TYPES][$name]);

        $columns = $this->getStaticValueProperty(Model::class, 'columnsMapClass')[$class];

        $this->assertArrayHasKey($name, $columns);
        $this->assertEquals($name, $columns[$name]);
    }

    public function dataDescribeColumnType()
    {
        return [
            [Column::TYPE_BIGINTEGER, Column::BIND_PARAM_INT, true],
            [Column::TYPE_INTEGER, Column::BIND_PARAM_INT, true],
            [Column::TYPE_TIMESTAMP, Column::BIND_PARAM_INT, true],
            [Column::TYPE_DECIMAL, Column::BIND_PARAM_DECIMAL, true],
            [Column::TYPE_FLOAT, Column::BIND_PARAM_DECIMAL, true],
            [Column::TYPE_DOUBLE, Column::BIND_PARAM_DECIMAL, true],
            [Column::TYPE_JSON, Column::BIND_PARAM_STR],
            [Column::TYPE_TEXT, Column::BIND_PARAM_STR],
            [Column::TYPE_CHAR, Column::BIND_PARAM_STR],
            [Column::TYPE_VARCHAR, Column::BIND_PARAM_STR],
            [Column::TYPE_DATE, Column::BIND_PARAM_STR],
            [Column::TYPE_DATETIME, Column::BIND_PARAM_STR],
            [Column::TYPE_BLOB, Column::BIND_PARAM_BLOB],
            [Column::TYPE_JSONB, Column::BIND_PARAM_BLOB],
            [Column::TYPE_MEDIUMBLOB, Column::BIND_PARAM_BLOB],
            [Column::TYPE_TINYBLOB, Column::BIND_PARAM_BLOB],
            [Column::TYPE_LONGBLOB, Column::BIND_PARAM_BLOB],
            [Column::TYPE_BOOLEAN, Column::BIND_PARAM_BOOL],
            [null, Column::BIND_PARAM_NULL],
            ['abc', Column::BIND_SKIP],
        ];
    }

    /**
     * @dataProvider dataDescribeColumnType
     */
    public function testDescribeColumnType($type, $expectedBind, $numeric = false)
    {
        $this->invokeStaticMethod(Model::class, 'describeColumnType', ['test', $type]);

        $this->assertColumnBind('test', $expectedBind, $numeric);
    }

    public function dataAddColumn()
    {
        return [
            ['test', Column::TYPE_BIGINTEGER, 'test', Column::BIND_PARAM_INT, true],
            ['test', Column::TYPE_INTEGER, 'test', Column::BIND_PARAM_INT, true],
            ['test', Column::TYPE_TIMESTAMP, 'test', Column::BIND_PARAM_INT, true],
            ['test', Column::TYPE_DECIMAL, 'test', Column::BIND_PARAM_DECIMAL, true],
            ['test', Column::TYPE_FLOAT, 'test', Column::BIND_PARAM_DECIMAL, true],
            ['test', Column::TYPE_DOUBLE, 'test', Column::BIND_PARAM_DECIMAL, true],
            ['test', Column::TYPE_JSON, 'test', Column::BIND_PARAM_STR],
            ['test', Column::TYPE_TEXT, 'test', Column::BIND_PARAM_STR],
            ['test', Column::TYPE_CHAR, 'test', Column::BIND_PARAM_STR],
            ['test', Column::TYPE_VARCHAR, 'test', Column::BIND_PARAM_STR],
            ['test', Column::TYPE_DATE, 'test', Column::BIND_PARAM_STR],
            ['test', Column::TYPE_DATETIME, 'test', Column::BIND_PARAM_STR],
            ['test', Column::TYPE_BLOB, 'test', Column::BIND_PARAM_BLOB],
            ['test', Column::TYPE_JSONB, 'test', Column::BIND_PARAM_BLOB],
            ['test', Column::TYPE_MEDIUMBLOB, 'test', Column::BIND_PARAM_BLOB],
            ['test', Column::TYPE_TINYBLOB, 'test', Column::BIND_PARAM_BLOB],
            ['test', Column::TYPE_LONGBLOB, 'test', Column::BIND_PARAM_BLOB],
            ['test', Column::TYPE_BOOLEAN, 'test', Column::BIND_PARAM_BOOL],
            ['test', null, 'test', Column::BIND_PARAM_NULL],
            ['test', 'abc', 'test', Column::BIND_SKIP],
        ];
    }

    /**
     * @dataProvider dataAddColumn
     */
    public function testAddColumn($name, $type, $map, $expectedBind, $numeric = false)
    {
        $this->invokeStaticMethod(Model::class, 'addColumn', [$name, $type, $map]);

        $this->assertColumnAdded($name, $type, $expectedBind, $numeric);
    }

    public function dataColumn()
    {
        return [
            [
                'test', Column::TYPE_BIGINTEGER,
                ['nullable' => false, 'default' => null, 'autoInsert' => false, 'autoUpdate' => false],
                Column::BIND_PARAM_INT, true
            ],
            [
                'test', Column::TYPE_INTEGER,
                ['nullable' => true, 'default' => null, 'autoInsert' => false, 'autoUpdate' => false],
                Column::BIND_PARAM_INT, true
            ],
            [
                'test', Column::TYPE_TIMESTAMP,
                ['nullable' => false, 'default' => 1, 'autoInsert' => false, 'autoUpdate' => false],
                Column::BIND_PARAM_INT, true
            ],
            [
                'test', Column::TYPE_DECIMAL,
                ['nullable' => true, 'default' => 1, 'autoInsert' => false, 'autoUpdate' => false],
                Column::BIND_PARAM_DECIMAL, true
            ],
            [
                'test', Column::TYPE_FLOAT,
                ['nullable' => false, 'default' => null, 'autoInsert' => true, 'autoUpdate' => false],
                Column::BIND_PARAM_DECIMAL, true
            ],
            [
                'test', Column::TYPE_DOUBLE,
                ['nullable' => true, 'default' => null, 'autoInsert' => true, 'autoUpdate' => false],
                Column::BIND_PARAM_DECIMAL, true
            ],
            [
                'test', Column::TYPE_JSON,
                ['nullable' => false, 'default' => 1, 'autoInsert' => true, 'autoUpdate' => false],
                Column::BIND_PARAM_STR
            ],
            [
                'test', Column::TYPE_TEXT,
                ['nullable' => true, 'default' => 1, 'autoInsert' => true, 'autoUpdate' => false],
                Column::BIND_PARAM_STR
            ],
            [
                'test', Column::TYPE_CHAR,
                ['nullable' => false, 'default' => null, 'autoInsert' => false, 'autoUpdate' => true],
                Column::BIND_PARAM_STR
            ],
            [
                'test', Column::TYPE_VARCHAR,
                ['nullable' => true, 'default' => null, 'autoInsert' => false, 'autoUpdate' => true],
                Column::BIND_PARAM_STR
            ],
            [
                'test', Column::TYPE_DATE,
                ['nullable' => false, 'default' => 1, 'autoInsert' => false, 'autoUpdate' => true],
                Column::BIND_PARAM_STR
            ],
            [
                'test', Column::TYPE_DATETIME,
                ['nullable' => true, 'default' => 1, 'autoInsert' => false, 'autoUpdate' => true],
                Column::BIND_PARAM_STR
            ],
            [
                'test', Column::TYPE_BLOB,
                ['nullable' => false, 'default' => null, 'autoInsert' => true, 'autoUpdate' => true],
                Column::BIND_PARAM_BLOB
            ],
            [
                'test', Column::TYPE_JSONB,
                ['nullable' => true, 'default' => null, 'autoInsert' => true, 'autoUpdate' => true],
                Column::BIND_PARAM_BLOB
            ],
            [
                'test', Column::TYPE_MEDIUMBLOB,
                ['nullable' => false, 'default' => 1, 'autoInsert' => true, 'autoUpdate' => true],
                Column::BIND_PARAM_BLOB
            ],
            [
                'test', Column::TYPE_TINYBLOB,
                ['nullable' => true, 'default' => 1, 'autoInsert' => true, 'autoUpdate' => true],
                Column::BIND_PARAM_BLOB
            ],
            [
                'test', Column::TYPE_LONGBLOB,
                [],
                Column::BIND_PARAM_BLOB
            ],
            [
                'test', Column::TYPE_BOOLEAN,
                [],
                Column::BIND_PARAM_BOOL
            ],
            [
                'test', null,
                [],
                Column::BIND_PARAM_NULL
            ],
            [
                'test', 'abc',
                [],
                Column::BIND_SKIP
            ],
        ];
    }

    /**
     * @dataProvider dataColumn
     */
    public function testColumn($name, $type, $options, $expectedBind, $numeric = false)
    {
        $model = $this->getMockModel();

        $this->invokeMethod($model, 'column', [$name, $type, $options]);

        $meta = $this->getStaticValueProperty(Model::class, 'metaDatasClass')[get_class($model)];

        $this->assertEquals($meta, $this->invokeMethod($model, 'metaData', []));

        $this->assertColumnAdded($name, $type, $expectedBind, $numeric, get_class($model));

        if (arr_fetch($options, 'nullable')) {
            $this->assertArrayHasKey(MetaData::MODELS_EMPTY_STRING_VALUES, $meta);
            $this->assertEquals([$name => true], $meta[MetaData::MODELS_EMPTY_STRING_VALUES]);
        } else {
            $this->assertArrayHasKey(MetaData::MODELS_NOT_NULL, $meta);
            $this->assertEquals([$name], $meta[MetaData::MODELS_NOT_NULL]);
        }

        if (!is_null(arr_fetch($options, 'default'))) {
            $this->assertArrayHasKey(MetaData::MODELS_DEFAULT_VALUES, $meta);
            $this->assertEquals([$name => arr_fetch($options, 'default')], $meta[MetaData::MODELS_DEFAULT_VALUES]);
        } else {
            $this->assertEmpty($meta[MetaData::MODELS_DEFAULT_VALUES]);
        }

        if (arr_fetch($options, 'autoInsert')) {
            $this->assertArrayHasKey(MetaData::MODELS_AUTOMATIC_DEFAULT_INSERT, $meta);
            $this->assertEquals([$name => true], $meta[MetaData::MODELS_AUTOMATIC_DEFAULT_INSERT]);
        }

        if (arr_fetch($options, 'autoUpdate')) {
            $this->assertArrayHasKey(MetaData::MODELS_AUTOMATIC_DEFAULT_UPDATE, $meta);
            $this->assertEquals([$name => true], $meta[MetaData::MODELS_AUTOMATIC_DEFAULT_UPDATE]);
        }

        $columns = $this->getStaticValueProperty(Model::class, 'columnsMapClass')[get_class($model)];

        $this->assertEquals([$name => $name], $columns);
        $this->assertEquals($columns, $this->invokeMethod($model, 'columnMap', []));
    }

    public function testFullColumns()
    {
        $columns = $this->dataColumn();

        /** @var Model $model */
        $model = $this->getMockModel();

        $names = [];
        $attrs = [];
        $types = [];
        $options = [];
        $binds = [];
        foreach ($columns as $column) {
            $this->invokeMethod($model, 'column', $column);

            $name = array_shift($column);
            $attrs[] = $name;
            $names[$name] = $name;
            $types[$name] = array_shift($column);
            $options[$name] = array_shift($column);
            $binds[$name] = array_shift($column);
        }

        $columns = $this->getStaticValueProperty(Model::class, 'columnsMapClass')[get_class($model)];
        $this->assertEquals($names, $columns);

        $meta = $this->getStaticValueProperty(Model::class, 'metaDatasClass')[get_class($model)];
        $this->assertEquals($attrs, $meta[MetaData::MODELS_ATTRIBUTES]);
        $this->assertEquals($binds, $meta[MetaData::MODELS_DATA_TYPES_BIND]);
    }

    public function dataPrimary()
    {
        return [
            ['primary', Column::TYPE_BIGINTEGER, ['identity' => true, 'autoIncrement' => true]],
            ['primary', Column::TYPE_BIGINTEGER, ['identity' => false, 'autoIncrement' => true]],
            ['primary', Column::TYPE_BIGINTEGER, ['identity' => true, 'autoIncrement' => false]],
            ['primary', Column::TYPE_BIGINTEGER, ['identity' => false, 'autoIncrement' => false]],
        ];
    }

    /**
     * @dataProvider dataPrimary
     */
    public function testPrimary($name, $type, $options)
    {
        /** @var Model $model */
        $model = $this->getMockModel();

        $this->invokeMethod($model, 'primary', [$name, $type, $options], Model::class);

        $this->assertColumnAdded($name, $type, Column::BIND_PARAM_INT, true);

        $meta = $this->getStaticValueProperty(Model::class, 'metaDatasClass')[Model::class];

        $this->assertEquals([$name], $meta[MetaData::MODELS_PRIMARY_KEY]);
        $this->assertEquals([$name], $meta[MetaData::MODELS_NOT_NULL]);

        if ($options['identity']) {
            $this->assertEquals($name, $meta[MetaData::MODELS_IDENTITY_COLUMN]);
        } else {
            $this->assertArrayNotHasKey(MetaData::MODELS_IDENTITY_COLUMN, $meta);
        }
        if ($options['autoIncrement']) {
            $this->assertEquals([$name => true], $meta[MetaData::MODELS_AUTOMATIC_DEFAULT_INSERT]);
        } else {
            $this->assertArrayNotHasKey(MetaData::MODELS_AUTOMATIC_DEFAULT_INSERT, $meta);
        }
    }

    public function testMultipePrimary()
    {
        /** @var Model $model */
        $model = $this->getMockModel();

        $this->invokeMethod($model, 'primary', ['p_1', Column::TYPE_BIGINTEGER, ['multiple' => true]], Model::class);
        $this->invokeMethod($model, 'primary', ['p_2', Column::TYPE_VARCHAR, ['multiple' => true]], Model::class);

        $this->assertColumnAdded('p_1', Column::TYPE_BIGINTEGER, Column::BIND_PARAM_INT, true);
        $this->assertColumnAdded('p_2', Column::TYPE_VARCHAR, Column::BIND_PARAM_STR, false);

        $meta = $this->getStaticValueProperty(Model::class, 'metaDatasClass')[Model::class];

        $this->assertEquals(['p_1', 'p_2'], $meta[MetaData::MODELS_PRIMARY_KEY]);
        $this->assertEquals(['p_1', 'p_2'], $meta[MetaData::MODELS_NOT_NULL]);
    }

    public function dataTimestampable()
    {
        return [
            ['t_1', Column::TYPE_DATETIME, ['insert' => true], 'beforeCreate', DATE_ATOM],
            ['t_1', Column::TYPE_DATETIME, ['update' => true], 'beforeUpdate', DATE_ATOM],
            ['t_1', Column::TYPE_DATE, ['insert' => true, 'type' => Column::TYPE_DATE, 'format' => 'Y-m-d'], 'beforeCreate', 'Y-m-d'],
            ['t_1', Column::TYPE_DATE, ['update' => true, 'type' => Column::TYPE_DATE, 'format' => 'Y-m-d'], 'beforeUpdate', 'Y-m-d'],
        ];
    }

    /**
     * @dataProvider dataTimestampable
     */
    public function testTimestampable($field, $type, $options, $event, $format)
    {
        /** @var Model $model */
        $model = $this->getMockModel();

        $this->invokeMethod($model, 'timestampable', [$field, $options]);

        $this->assertColumnAdded($field, $type, Column::BIND_PARAM_STR, false, get_class($model));

        $modelManager = $model->getModelsManager();
        $behaviors = $this->getValueProperty($modelManager, '_behaviors');

        $class = strtolower(get_class($model));

        $this->assertArrayHasKey($class, $behaviors);
        $this->assertEquals([
            'field'  => $field,
            'format' => $format
        ], $this->invokeMethod($behaviors[$class][0], 'getOptions', [$event]));
    }

    public function dataFailedTimestampable()
    {
        return [
            [[]],
            [['nullable' => 'true']],
            [['default' => 'true']],
            [['autoInsert' => 'true']],
            [['autoUpdate' => 'true']],
        ];
    }

    /**
     * @dataProvider dataFailedTimestampable
     * @expectedException \RuntimeException
     */
    public function testFailedTimestampable($options)
    {
        /** @var Model $model */
        $model = $this->getMockModel();

        $this->invokeMethod($model, 'timestampable', ['test', $options]);
    }

    public function dataSoftDeletable()
    {
        return [
            ['t_1', Column::TYPE_BOOLEAN, [], Column::BIND_PARAM_BOOL, true],
            ['t_1', Column::TYPE_CHAR, ['type' => Column::TYPE_CHAR, 'value' => 'D'], Column::BIND_PARAM_STR, 'D'],
        ];
    }

    /**
     * @dataProvider dataSoftDeletable
     */
    public function testSoftDeletable($field, $type, $options, $bind, $value)
    {
        /** @var Model $model */
        $model = $this->getMockModel();

        $this->invokeMethod($model, 'softDeletable', [$field, $options]);

        $this->assertColumnAdded('t_1', $type, $bind, false, get_class($model));

        $modelManager = $model->getModelsManager();
        $behaviors = $this->getValueProperty($modelManager, '_behaviors');

        $class = strtolower(get_class($model));

        $this->assertArrayHasKey($class, $behaviors);
        $this->assertEquals([
            'field' => $field,
            'value' => $value
        ], $this->invokeMethod($behaviors[$class][0], 'getOptions', []));
    }

    public function dataFailedSoftDeletable()
    {
        return [
            [['default' => 'true']],
            [['autoInsert' => 'true']],
            [['autoUpdate' => 'true']],
        ];
    }

    /**
     * @dataProvider dataFailedSoftDeletable
     * @expectedException \RuntimeException
     */
    public function testFailedSoftDeletable($options)
    {
        /** @var Model $model */
        $model = $this->getMockModel();

        $this->invokeMethod($model, 'softDeletable', ['test', $options]);
    }

    public function testTimestamps()
    {
        /** @var Model $model */
        $model = $this->getMockModel();

        $this->invokeMethod($model, 'timestamps', ['test']);

        $this->assertColumnAdded('created_at', Column::TYPE_DATETIME, Column::BIND_PARAM_STR, false, get_class($model));
        $this->assertColumnAdded('updated_at', Column::TYPE_DATETIME, Column::BIND_PARAM_STR, false, get_class($model));


        $modelManager = $model->getModelsManager();
        $behaviors = $this->getValueProperty($modelManager, '_behaviors');

        $class = strtolower(get_class($model));

        $this->assertArrayHasKey($class, $behaviors);
        $this->assertEquals([
            'field'  => 'created_at',
            'format' => DATE_ATOM
        ], $this->invokeMethod($behaviors[$class][0], 'getOptions', ['beforeCreate']));
        $this->assertEquals([
            'field'  => 'updated_at',
            'format' => DATE_ATOM
        ], $this->invokeMethod($behaviors[$class][1], 'getOptions', ['beforeUpdate']));
    }

    public function testSoftDelete()
    {
        /** @var Model $model */
        $model = $this->getMockModel();

        $this->invokeMethod($model, 'softDelete', []);

        $this->assertColumnAdded('deleted', Column::TYPE_BOOLEAN, Column::BIND_PARAM_BOOL, false, get_class($model));

        $modelManager = $model->getModelsManager();
        $behaviors = $this->getValueProperty($modelManager, '_behaviors');

        $class = strtolower(get_class($model));

        $this->assertArrayHasKey($class, $behaviors);
        $this->assertEquals([
            'field' => 'deleted',
            'value' => true
        ], $this->invokeMethod($behaviors[$class][0], 'getOptions', []));
    }
}
