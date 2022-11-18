<?php


namespace app\widgets\sort;


use yii\bootstrap\Widget;

class ViewRender extends Widget
{
    public function run()
    {
        $view = $this->getView();
        $options = json_encode($this->clientOptions['params']);
        $view->registerJs("$('#save_grid_column_sort').on('click', $options, saveGridColumnSort);");
        return $this->render('widget', ['columns' => $this->formatColumns()]);
    }

    protected function formatColumns()
    {
        $columns = $this->clientOptions['columns'];
        return $columns;
    }

}