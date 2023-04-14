<?php
namespace models;

use app\models\Status;
use app\models\Word;
use Codeception\Test\Unit;

class WordTest extends Unit
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
        // по строке - id
        $str = '1';

        $testQueries = $this->getTestQueries("`id`='$str'");

        $queries = Word::getQueriesToGetChildren('id=' . $str);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        // по массиву - ['name' => 'что-то']
        $name = 'test';

        $testQueries = $this->getTestQueries("`name`='$name'");

        $queries = Word::getQueriesToGetChildren(['name' => $name]);
        foreach ($queries as $key => $query) {
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }
    }

    public function testGetQueryByIdToGetChildrenIfDepthIsAbsolute()
    {
        // по строке - id
        $str = '1';

            // level 1
        $level = 1;
        $testQueries = $this->getTestQueriesIfDepthIsAbsolute("`id`='$str'", $level);

        $queries = Word::getQueriesToGetChildrenIfDepthIsAbsolute('id=' . $str, $level);
        foreach ($queries as $key => $query) {
            if ($key === 0) {
                expect($query)->equals($testQueries[$key]);
                continue;
            }
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        // по массиву - ['name' => 'что-то']
        $name = 'test';

            // level 1
        $level = 1;
        $testQueries = $this->getTestQueriesIfDepthIsAbsolute("`name`='$name'", $level);

        $queries = Word::getQueriesToGetChildrenIfDepthIsAbsolute(['name' => $name], $level);
        foreach ($queries as $key => $query) {
            if ($key === 0) {
                expect($query)->equals($testQueries[$key]);
                continue;
            }
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

            // level 2
        $level = 2;
        $testQueries = $this->getTestQueriesIfDepthIsAbsolute("`name`='$name'", $level);

        $queries = Word::getQueriesToGetChildrenIfDepthIsAbsolute(['name' => $name], $level);
        foreach ($queries as $key => $query) {
            if ($key === 0) {
                expect($query)->equals($testQueries[$key]);
                continue;
            }
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }
    }

    public function testGetQueryByIdToGetChildrenIfParentIsVirtual()
    {
        // по строке - один id
        $str = Word::FIELD_WORD['name'];
        $testQueries = $this->getTestQueriesIfDepthIfParentIsVirtual("`id`='$str'");

        $queries = Word::getQueriesToGetChildrenIfParentIsVirtual('id=' . $str);
        foreach ($queries as $key => $query) {
            if ($key === 0) {
                expect($query)->equals($testQueries[$key]);
                continue;
            }
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        // по массиву - два id
        $ids = [];
        $ids[] = Word::FIELD_WORD['name'];
        $ids[] = Word::FIELD_WORD['state'];

        $testQueries = $this->getTestQueriesIfDepthIfParentIsVirtual("`id`='$ids[0], $ids[1]'");

        $queries = Word::getQueriesToGetChildrenIfParentIsVirtual(['id' => $ids]);
        foreach ($queries as $key => $query) {
            if ($key === 0) {
                expect($query)->equals($testQueries[$key]);
                continue;
            }
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        // по массиву - [name => 'нечто']
        $name = Word::LABEL_FIELD_WORD[Word::FIELD_WORD['name']];
        $testQueries = $this->getTestQueriesIfDepthIfParentIsVirtual("`name`='$name'");

        $queries = Word::getQueriesToGetChildrenIfParentIsVirtual(['name' => $name]);
        foreach ($queries as $key => $query) {
            if ($key === 0) {
                expect($query)->equals($testQueries[$key]);
                continue;
            }
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }

        // по массиву - ['like', 'name', 'нечто']
        $name = Word::LABEL_FIELD_WORD[Word::FIELD_WORD['name']];
        $condition = ['like', 'name', $name];
        $testQueries = $this->getTestQueriesIfDepthIfParentIsVirtual($condition);

        $queries = Word::getQueriesToGetChildrenIfParentIsVirtual($condition);
        foreach ($queries as $key => $query) {
            if ($key === 0) {
                expect($query)->equals($testQueries[$key]);
                continue;
            }
            expect($query->createCommand()->getRawSql())->equals($testQueries[$key]);
        }
    }

    public function testGetNumbersBySimilarLabel()
    {
        $id = Word::FIELD_WORD['name'];
        $label = Word::LABEL_FIELD_WORD[$id];
        expect(Word::getNumbersBySimilarLabel($label))->equals([$id]);
        expect(Word::getNumbersBySimilarLabel($label . '%'))->equals([$id]);
        expect(Word::getNumbersBySimilarLabel('%' . $label))->equals([$id]);
        expect(Word::getNumbersBySimilarLabel('%' . $label . '%'))->equals([$id]);

        expect(Word::getNumbersBySimilarLabel(mb_substr($label, 1)))->equals([]);
        expect(Word::getNumbersBySimilarLabel(mb_substr($label, 0, mb_strlen($label) - 1)))->equals([]);
        expect(Word::getNumbersBySimilarLabel(mb_substr($label, 1, mb_strlen($label) - 1)))->equals([]);

        expect(Word::getNumbersBySimilarLabel('%' . mb_substr($label, 1)))->equals([$id]);
        expect(Word::getNumbersBySimilarLabel(mb_substr($label, 0, mb_strlen($label) - 1) . '%'))->equals([$id]);
        expect(Word::getNumbersBySimilarLabel('%' . mb_substr($label, 1, mb_strlen($label) - 1) . '%'))->equals([$id]);

        expect(Word::getNumbersBySimilarLabel(''))->equals([]);
        expect(Word::getNumbersBySimilarLabel(0))->equals([]);
        expect(Word::getNumbersBySimilarLabel(false))->equals([]);
        expect(Word::getNumbersBySimilarLabel(null))->equals([]);
        expect(Word::getNumbersBySimilarLabel('%'))->equals(array_keys(Word::LABEL_FIELD_WORD));
        expect(Word::getNumbersBySimilarLabel('%%'))->equals(array_keys(Word::LABEL_FIELD_WORD));
        expect(Word::getNumbersBySimilarLabel('%%%'))->equals(array_keys(Word::LABEL_FIELD_WORD));
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

    public function getTestQueriesIfDepthIsAbsolute($condition, $level = 1)
    {
        $testQueries = [];
        $deleted = Status::NOT_DELETED;
        $conditions = ['', '', '', ''];
        $conditions[$level] = ' (' . $condition . ') AND';

        $testQueries[0] = implode(', ', array_keys(Word::LABEL_FIELD_WORD));
        $testQueries[1] = "SELECT `id` FROM `word` WHERE{$conditions[1]} (`parent_id` IN ($testQueries[0])) AND (`deleted`=$deleted)";
        $testQueries[2] = "SELECT `id` FROM `word` WHERE{$conditions[2]} (`parent_id` IN ($testQueries[1])) AND (`deleted`=$deleted)";
        $testQueries[3] = "SELECT `id` FROM `word` WHERE{$conditions[3]} (`parent_id` IN ($testQueries[2])) AND (`deleted`=$deleted)";

        $testQueries[0] = explode(', ', $testQueries[0]);

        return $testQueries;
    }

    public function getTestQueriesIfDepthIfParentIsVirtual($condition)
    {
        if (! is_array($condition)) {
            $condition = str_replace(['`', '\'', '"'], '', $condition);
            list($key, $value) = explode('=', $condition);
            $condition = [$key => $value];
        }

        $testQueries = [];
        $deleted = Status::NOT_DELETED;

        if (isset($condition['id'])) {
            $numbers = explode(', ', $condition['id']);
        } elseif (isset($condition['name'])) {
            $numbers = Word::getNumbersBySimilarLabel($condition['name']);
        } elseif ($condition[0] === 'like' && $condition[1] === 'name') {
            $numbers = Word::getNumbersBySimilarLabel($condition[2]);
        } else {
            return $testQueries;
        }

        $testQueries[0] = implode(', ',$numbers);
        $subQuery1 = "={$testQueries[0]}";
        if (mb_strpos($testQueries[0], ',') !== false) {
            $subQuery1 = " IN ($testQueries[0])";
        }
        $testQueries[1] = "SELECT `id` FROM `word` WHERE (`parent_id`{$subQuery1}) AND (`deleted`=$deleted)";
        $testQueries[2] = "SELECT `id` FROM `word` WHERE (`parent_id` IN ($testQueries[1])) AND (`deleted`=$deleted)";
        $testQueries[3] = "SELECT `id` FROM `word` WHERE (`parent_id` IN ($testQueries[2])) AND (`deleted`=$deleted)";

        $testQueries[0] = explode(', ',$testQueries[0]);

        return $testQueries;
    }
}