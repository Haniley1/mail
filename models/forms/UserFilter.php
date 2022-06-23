<?php

declare(strict_types=1);

namespace humhub\modules\mail\models\forms;

use Yii;
use yii\base\Model;

class UserFilter extends Model
{
    public ?string $filter = null;

    public function rules(): array
    {
        return [
            ['filter', 'string', 'max' => 50],
            ['filter', 'trim'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'filter' => Yii::t('MailModule.base', 'Search'),
        ];
    }
}
