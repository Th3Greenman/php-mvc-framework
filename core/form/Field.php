<?php


namespace app\core\form;


use app\core\abstractModel;

class Field
{
    /** @var string  */
    public const TYPE_TEXT = 'text';
    /** @var string */
    public const TYPE_PASSWORD = 'password';
    /** @var string  */
    public const TYPE_NUMBER = 'number';
    /** @var string  */
    public string $type;
    /** @var abstractModel  */
    public abstractModel $model;
    /** @var string  */
    public string $attribute;

    public function __construct(abstractModel $model, $attribute)
    {
        $this->type = self::TYPE_TEXT;
        $this->model = $model;
        $this->attribute = $attribute;
    }

    public function __toString(): string
    {
        return sprintf('
            <div class="form-group">
                <label>%s</label>
                <input type="%s" name="%s" value="%s" class="form-control %s">
                <div class="invalid-feedback">
                    %s
                </div>
            </div>
        ',
            $this->attribute,
            $this->type,
            $this->attribute,
            $this->model->{$this->attribute},
            $this->model->hasError($this->attribute) ? 'is-invalid' : '',
            $this->model->getFirstError($this->attribute),
        );
    }

    /**
     * @return $this
     */
    public function passwordField()
    {
        $this->type = self::TYPE_PASSWORD;

        return $this;
    }
}
