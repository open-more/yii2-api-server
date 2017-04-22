<?php
/**
 * Created by PhpStorm.
 * User: symo
 * Date: 17/4/17
 * Time: 下午5:14
 */

namespace common\traits\model;
use common\exceptions\data\ValidateFailedException;

/**
 * Class ModelTrait
 * @package common\traits\model
 * @property $firstErrorMessage
 * @inheritdoc Model
 */
trait ModelTrait
{
    /**
     * @return string
     */
    public function getFirstErrorMessage()
    {
        return current($this->firstErrors);
    }

    /**
     * @throws ValidateFailedException
     */
    public function validateOrFail()
    {
        if(!$this->validate()){
            throw new ValidateFailedException($this->getFirstErrorMessage());
        }
    }
}