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
        $deleted = Status::NOT_DELETED;
        $testQueries[0] = "SELECT `id` FROM `word` WHERE (`parent_id`=1) AND (`deleted`=$deleted)";
        $testQueries[1] = "SELECT `id` FROM `word` WHERE (`parent_id` IN (SELECT `id` FROM `word` WHERE (`parent_id`=1) AND (`deleted`=$deleted))) AND (`deleted`=$deleted)";
        $testQueries[2] = "SELECT `id` FROM `word` WHERE (`parent_id` IN (SELECT `id` FROM `word` WHERE (`parent_id` IN (SELECT `id` FROM `word` WHERE (`parent_id`=1) AND (`deleted`=$deleted))) AND (`deleted`=$deleted))) AND (`deleted`=$deleted)";

        $queries = Word::getQueriesToGetChildren(1, 1);
        expect(count($queries))->equals(1);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        $queries = Word::getQueriesToGetChildren(1, 2);
        expect(count($queries))->equals(2);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        $queries = Word::getQueriesToGetChildren(1, 3);
        expect(count($queries))->equals(3);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        // удаленные - int
        $deleted = Status::DELETED;
        $testQueries[0] = "SELECT `id` FROM `word` WHERE (`parent_id`=1) AND (`deleted`=$deleted)";
        $testQueries[1] = "SELECT `id` FROM `word` WHERE (`parent_id` IN (SELECT `id` FROM `word` WHERE (`parent_id`=1) AND (`deleted`=$deleted))) AND (`deleted`=$deleted)";

        $queries = Word::getQueriesToGetChildren(1, 1, Status::DELETED);
        expect(count($queries))->equals(1);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        $queries = Word::getQueriesToGetChildren(1, 2, Status::DELETED);
        expect(count($queries))->equals(2);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        // все - int
        $testQueries[0] = "SELECT `id` FROM `word` WHERE `parent_id`=1";
        $testQueries[1] = "SELECT `id` FROM `word` WHERE `parent_id` IN (SELECT `id` FROM `word` WHERE `parent_id`=1)";

        $queries = Word::getQueriesToGetChildren(1, 1, Status::ALL);
        expect(count($queries))->equals(1);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        $queries = Word::getQueriesToGetChildren(1, 2, Status::ALL);
        expect(count($queries))->equals(2);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        // все по названию - string
        $testQueries[0] = "SELECT `id` FROM `word` WHERE `parent_id`='text'";

        $queries = Word::getQueriesToGetChildren('text', 1, Status::ALL);
        expect(count($queries))->equals(1);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        // все по названию - array
        $name = 'test';
        $testQueries[0] = "SELECT `id` FROM `word` WHERE `name`='$name'";
        $testQueries[1] = "SELECT `id` FROM `word` WHERE `parent_id` IN (SELECT `id` FROM `word` WHERE `name`='$name')";

        $queries = Word::getQueriesToGetChildren(['name' => $name], 1, Status::ALL);
        expect(count($queries))->equals(1);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        $queries = Word::getQueriesToGetChildren(['name' => $name], 2, Status::ALL);
        expect(count($queries))->equals(2);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }
    }
}