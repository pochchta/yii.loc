<?php
namespace models;

use app\models\Status;
use app\models\Word;

class WordTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testGetQueryByIdToGetChildren()
    {
        // неудаленные элементы словаря - int
        $testQueries = $this->getTestQueries("`id`=1");

        $queries = Word::getQueriesToGetChildren(1, 1);
        expect(count($queries))->equals(2);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        $queries = Word::getQueriesToGetChildren(1, 2);
        expect(count($queries))->equals(3);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        $queries = Word::getQueriesToGetChildren(1, 3);
        expect(count($queries))->equals(4);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        // удаленные - int
        $testQueries = $this->getTestQueries("`id`=1", Status::DELETED);

        $queries = Word::getQueriesToGetChildren(1, 1, Status::DELETED);
        expect(count($queries))->equals(2);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        $queries = Word::getQueriesToGetChildren(1, 2, Status::DELETED);
        expect(count($queries))->equals(3);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        // все - int
        $testQueries = $this->getTestQueries("`id`=1", Status::ALL);

        $queries = Word::getQueriesToGetChildren(1, 1, Status::ALL);
        expect(count($queries))->equals(2);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        $queries = Word::getQueriesToGetChildren(1, 2, Status::ALL);
        expect(count($queries))->equals(3);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        // все по строке - string
        $str = '1';
        $testQueries = $this->getTestQueries("`id`='$str'", Status::ALL);

        $queries = Word::getQueriesToGetChildren($str, 1, Status::ALL);
        expect(count($queries))->equals(2);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        // все по произвольному полю - array
        $name = 'test';
        $testQueries = $this->getTestQueries("`name`='$name'", Status::ALL);

        $queries = Word::getQueriesToGetChildren(['name' => $name], 1, Status::ALL);
        expect(count($queries))->equals(2);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        $queries = Word::getQueriesToGetChildren(['name' => $name], 2, Status::ALL);
        expect(count($queries))->equals(3);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }
    }

    public function getTestQueries($condition, $deleted = Status::NOT_DELETED)
    {
        $testQueries = [];

        if ($deleted === Status::NOT_DELETED || $deleted === Status::DELETED) {
            $testQueries[0] = "SELECT `id` FROM `word` WHERE ($condition) AND (`deleted`=$deleted)";
            $testQueries[1] = "SELECT `id` FROM `word` WHERE (`parent_id` IN ($testQueries[0])) AND (`deleted`=$deleted)";
            $testQueries[2] = "SELECT `id` FROM `word` WHERE (`parent_id` IN ($testQueries[1])) AND (`deleted`=$deleted)";
            $testQueries[3] = "SELECT `id` FROM `word` WHERE (`parent_id` IN ($testQueries[2])) AND (`deleted`=$deleted)";
        } else {
            $testQueries[0] = "SELECT `id` FROM `word` WHERE $condition";
            $testQueries[1] = "SELECT `id` FROM `word` WHERE `parent_id` IN ($testQueries[0])";
            $testQueries[2] = "SELECT `id` FROM `word` WHERE `parent_id` IN ($testQueries[1])";
            $testQueries[3] = "SELECT `id` FROM `word` WHERE `parent_id` IN ($testQueries[2])";
        }

        return $testQueries;
    }
}