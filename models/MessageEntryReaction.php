<?php
declare(strict_types=1);

namespace humhub\modules\mail\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use yii\db\ActiveQuery;

/**
 * @property integer $id
 * @property integer $message_entry_id
 * @property integer $user_id
 * @property string $type
 * @property string $created_at
 * @property integer $message_id
 */
class MessageEntryReaction extends ActiveRecord
{
    const HEART = 'heart';
    const FLAME = 'flame';
    const THUMB_UP = 'thumb_up';
    const CRYING = 'crying';

    public static function tableName(): string
    {
        return 'message_entry_reaction';
    }

    public static function getReactionTypes(): array
    {
        return [self::HEART, self::FLAME, self::THUMB_UP, self::CRYING];
    }

    public static function create(int $userId, int $messageEntryId, string $type): MessageEntryReaction
    {
        $messageEntry = MessageEntry::findOne(['id' => $messageEntryId]);

        return new MessageEntryReaction([
            'user_id' => $userId,
            'message_entry_id' => $messageEntryId,
            'type' => $type,
            'message_id' => $messageEntry->message_id,
        ]);
    }

    public static function canAddReaction(int $userId, int $messageEntryId): bool
    {
        $messageEntry = MessageEntry::findOne(['id' => $messageEntryId]);
        if (!$messageEntry) {
            return false;
        }

        return UserMessage::find()->where(['user_id' => $userId, 'message_id' => $messageEntry->message_id])->exists();
    }

    public static function hasReaction(int $userId, int $messageEntryId, string $type): bool
    {
        return MessageEntryReaction::find()
            ->where(['user_id' => $userId, 'message_entry_id' => $messageEntryId, 'type' => $type])
            ->exists();
    }

    public function rules(): array
    {
        return [
            [['message_entry_id', 'user_id'], 'integer'],
            [['type'], 'string', 'max' => 20],
            ['type', 'in', 'range' => self::getReactionTypes()],
            [['message_entry_id', 'user_id', 'type', 'message_id'], 'required'],
            [['created_at'], 'safe'],
        ];
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['user_id' => 'id']);
    }

    public function getMessage(): ActiveQuery
    {
        return $this->hasOne(Message::class, ['id' => 'message_id']);
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        $saveResult = parent::save($runValidation, $attributeNames);
        if (!$saveResult) {
            return false;
        }

        /** @var Message $message */
        $message = $this->getMessage()->one();
        if ($message) {
            $message->seen($this->user_id);
            // force update update_at & updated_by fields
            $message->save();
        }


        return true;
    }

    public function afterDelete()
    {
        parent::afterDelete();

        /** @var Message $message */
        $message = $this->getMessage()->one();
        $message->updateAttributes(['updated_at' => $message->getRealUpdatedAt()]);
    }
}
