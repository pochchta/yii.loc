<?php


namespace app\widgets\csc;


use yii\bootstrap\Widget;
use yii\helpers\Inflector;

class ViewRender extends Widget
{
    public function run()
    {
        $view = $this->getView();
        WidgetAsset::register($view);
        $viewName = Inflector::camel2id($this->clientOptions['columns']['params']['widget_name'], '-');
        return $this->render($viewName, ['columns' => $this->formatColumns()]);
    }

    protected function formatColumns()
    {
        $columns = $this->clientOptions['columns'];
        return $columns;
    }

}