<?php

use \app\widgets\sort\GridColumnSort;
use Codeception\Test\Unit;
use Codeception\Util\Fixtures;

class GridColumnSortTest extends Unit
{
    private $classForLabel = '\app\models\Device';

    /**
     * @var UnitTester
     */
    protected $tester;

    protected function _before()
    {
        if(Fixtures::exists('grid_column_sort')){
            foreach (Fixtures::get('grid_column_sort') as $item) {
                $this->tester->haveInDatabase('grid_column_sort', $item);
            }
        }
    }

    protected function _after()
    {
    }

    public function testExtractColumnName()
    {
        $gcs = new GridColumnSort([],[
            'class' => $this->classForLabel,
            'name' => '',
        ]);

        $method = self::getMethod('extractColumnName');
        expect($method->invoke($gcs, 'id'))->equals((new $this->classForLabel())->getAttributeLabel('id'));
        expect($method->invoke($gcs, 'id:test'))->equals((new $this->classForLabel())->getAttributeLabel('id'));
        expect($method->invoke($gcs, 'id:test:test'))->equals((new $this->classForLabel())->getAttributeLabel('id'));
        expect($method->invoke($gcs, ['attribute' => 'kind_id']))->equals((new $this->classForLabel())->getAttributeLabel('kind_id'));
        expect($method->invoke($gcs, ['class' => 'yii\grid\SerialColumn']))->equals('SerialColumn');
        expect($method->invoke($gcs, ['class' => 'SerialColumn']))->equals('SerialColumn');
        expect($method->invoke($gcs, []))->equals('noname');

    }

    public function testGrid0Col0()
    {
        $gcs = new GridColumnSort();
        expect($gcs->getGridViewData()['columns'])->equals([]);
        expect($gcs->getColumnsForWidget()['enabled'])->equals([]);
        expect($gcs->getColumnsForWidget()['disabled'])->equals([]);
    }

    public function testGrid0Col1()
    {
        $gcs = new GridColumnSort(
            ['columns' => []],
            [
                'name' => 'testName',
                'role' => 'id'
            ]
        );
        expect($gcs->getGridViewData()['columns'])->equals([]);
        expect($gcs->getColumnsForWidget()['enabled'])->equals([]);
        expect($gcs->getColumnsForWidget()['disabled'])->equals([]);
    }

    public function testGrid1Col0()
    {
        $gcs = new GridColumnSort(
            ['columns' => [
                'id',
            ]]
        );
        expect($gcs->getGridViewData()['columns'])->equals(['id']);
        expect($gcs->getColumnsForWidget()['enabled'])->equals([]);
        expect($gcs->getColumnsForWidget()['disabled'])->equals(['id']);
    }

    public function testGrid1Col1()
    {
        $gcs = new GridColumnSort(
            ['columns' => [
                'id',
            ]],
            [
                'name' => 'testName',
                'role' => 'id'
            ]
        );
        expect($gcs->getGridViewData()['columns'])->equals(['id']);
        expect($gcs->getColumnsForWidget()['enabled'])->equals(['id']);
        expect($gcs->getColumnsForWidget()['disabled'])->equals([]);
    }

    public function testGrid2Col1()
    {
        $gcs = new GridColumnSort(
            ['columns' => [
                'id',
                'name'
            ]],
            [
                'name' => 'testName',
                'role' => 'id',
            ]
        );
        expect($gcs->getGridViewData()['columns'])->equals(['id']);
        expect($gcs->getColumnsForWidget()['enabled'])->equals(['id']);
        expect($gcs->getColumnsForWidget()['disabled'])->equals(['name']);
    }

    public function testGrid1Col1Req1()
    {
        $gcs = new GridColumnSort(
            ['columns' => [
                'id',
                'testCol' => []
            ]],
            [
                'name' => 'testName',
                'role' => 'id',
                'required' => ['testCol']
            ]
        );
        expect($gcs->getGridViewData()['columns'])->equals(['id', []]);
        expect($gcs->getColumnsForWidget()['enabled'])->equals(['id', 'testCol']);
        expect($gcs->getColumnsForWidget()['disabled'])->equals([]);
    }

    public function testGrid1SameColAndReq()
    {
        $gcs = new GridColumnSort(
            ['columns' => [
                'id',
                'testCol' => []
            ]],
            [
                'name' => 'testName',
                'role' => 'id',
                'required' => ['id']
            ]
        );
        expect($gcs->getGridViewData()['columns'])->equals(['id']);
        expect($gcs->getColumnsForWidget()['enabled'])->equals(['id']);
        expect($gcs->getColumnsForWidget()['disabled'])->equals(['testCol']);
    }

    protected static function getMethod($name) {
        $class = new ReflectionClass('\app\widgets\sort\GridColumnSort');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

}