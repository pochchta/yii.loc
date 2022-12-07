<?php


namespace app\widgets\sort;


use yii\bootstrap\Widget;

class ViewRender extends Widget
{
    public function run()
    {
        $options = json_encode($this->clientOptions['columns']['params']);
        $view = $this->getView();
        WidgetAsset::register($view);
        $view->registerJs("$('#save_grid_column_sort').on('click', $options, gcs.save);");
        $view->registerJs("$('#load_grid_column_sort').on('change', $options, gcs.load);");
        return $this->render('widget', ['columns' => $this->formatColumns()]);
    }

    protected function formatColumns()
    {
        $columns = $this->clientOptions['columns'];
        return $columns;
    }

}