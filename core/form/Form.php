<?php


namespace app\core\form;


use app\core\abstractModel;

class Form
{
    public static function begin($action, $method): Form
    {
        echo sprintf('<form action="%s" method="%s">', $action, $method);
        return new Form();
    }

    public static function end()
    {
        echo '</form>';
    }

    /**
     * @param abstractModel $model
     * @param $attribute
     * @return Field
     */
    public function field(abstractModel $model, $attribute): Field
    {
        return new Field($model, $attribute);
    }
}
