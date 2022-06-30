<?php

declare(strict_types=1);

namespace humhub\modules\mail\widgets;

use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\forms\UserFilter;
use humhub\modules\mail\models\Message;
use humhub\modules\search\helpers\TransliterateHelper;
use humhub\widgets\JsWidget;
use Yii;
use yii\data\Pagination;
use yii\db\ActiveQuery;

class ChatUserList extends JsWidget
{
    public $jsWidget = 'mail.UserList';

    public ActiveQuery $query;

    /**
     * @var string title of the box (not html encoded!)
     */
    public string $title = 'Users';

    public ?int $pageSize = null;

    public ?int $conversationId = null;

    public UserFilter $filter;

    public ?Message $message = null;

    public function init(): void
    {
        if ($this->pageSize === null) {
            $this->pageSize = Yii::$app->getModule('user')->userListPaginationSize;
        }

        parent::init();
    }
    public function run(): string
    {
        $this->applyFilter();
        $countQuery = clone $this->query;
        $pagination = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $this->pageSize]);
        $this->query->offset($pagination->offset)->limit($pagination->limit);

        $this->getView()->registerJsConfig('mail.UserList', [
            'removeParticipantUrl' => Url::toRemoveParticipant(),
            'userListUrl' => Url::toUserList($this->message->id)
        ]);

        return $this->render("chatUserList", [
            'title' => $this->title,
            'users' => $this->query->all(),
            'pagination' => $pagination,
            'filter' => $this->filter,
            'conversationId' => $this->conversationId,
            'message' => $this->message
        ]);
    }

    private function applyFilter(): void
    {
        if (empty($this->filter->filter)) {
            return;
        }

        $this->query
            ->leftJoin('profile', 'profile.user_id = user.id')
            ->andWhere(['like', 'profile.firstname', $this->filter->filter])
            ->orWhere(['like', 'profile.lastname', $this->filter->filter]);

        if (preg_match('/[a-z]/i', $this->filter->filter)) {
            $filterRu = TransliterateHelper::switch2Ru($this->filter->filter);
            $filterTransliteratedRu = TransliterateHelper::transliterate2Ru($this->filter->filter);
            $this->query
                ->orWhere(['like', 'profile.firstname', $filterRu])
                ->orWhere(['like', 'profile.lastname', $filterRu])
                ->orWhere(['like', 'profile.firstname', $filterTransliteratedRu])
                ->orWhere(['like', 'profile.lastname', $filterTransliteratedRu]);
        } else {
            $filterEn = TransliterateHelper::switch2En($this->filter->filter);
            $filterTransliteratedEn = TransliterateHelper::transliterate2En($this->filter->filter);
            $this->query
                ->orWhere(['like', 'profile.firstname', $filterEn])
                ->orWhere(['like', 'profile.lastname', $filterEn])
                ->orWhere(['like', 'profile.firstname', $filterTransliteratedEn])
                ->orWhere(['like', 'profile.lastname', $filterTransliteratedEn]);
        }
    }
}
