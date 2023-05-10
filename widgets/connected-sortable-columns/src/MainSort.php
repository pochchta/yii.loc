<?php


namespace app\widgets\csc;


use Yii;

abstract class MainSort
{
    protected $params;
    protected $namesFromRep;
    protected $columnsForWidget;

    /**
     * @param array $params
     */
    public function loadParams(array $params = [])
    {
        foreach (['name', 'class', 'role', 'write_url', 'read_url', 'token'] as $name) {
            if (! isset($params[$name])) {
                $params[$name] = '';
            }
        }
        if ($params['name'] === '') {
            $params['name'] = $this->getShortClassName($params['class']);
        }
        if (! isset($params['required'])) {
            $params['required'] = [];
        }

        $params['widget_name'] = basename(get_class($this));

        $this->params = $params;
    }

    public function runWidget()
    {
        $this->process();
        return ViewRender::widget([
            'clientOptions' => [
                'columns' => $this->columnsForWidget,
            ]
        ]);
    }

    public function getColumnsForWidget()
    {
        return $this->columnsForWidget;
    }

    abstract protected function process();

    protected function takeColumnsFromRep()
    {
        $model = Model::findOne([
            'role' => $this->params['role'],
            'name' => $this->params['name'],
            'widget_name' => $this->params['widget_name'],
        ]);
        $this->namesFromRep = [];
        if ($model) {
            $this->namesFromRep = json_decode($model->col);
        }
    }

    protected function findLabel($key)
    {
        if (class_exists($this->params['class'])) {
            $label = (new $this->params['class'])->getAttributeLabel($key);
        }
        return $label ?? $key;
    }

    protected function getShortClassName($name)
    {
        $pos = strrpos($name, '\\');
        if ($pos !== false) {
            $name = substr($name, $pos + 1);
        }
        return $name;
    }

    public static function getListProfileView()
    {
        $keys = array_keys(Yii::$app->authManager->getRoles());
        $roles = array_combine($keys, $keys);
        return array_merge(
            ['default' => 'По умолчанию'],
            $roles
        );
    }
}