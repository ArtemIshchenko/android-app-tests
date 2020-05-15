<?php

namespace backend\models\db\adm;

use librariesHelpers\helpers\Type\Type_Cast;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Adm model
 *
 * @property integer $id
 * @property string $nickname
 * @property string $email
 * @property string $password
 * @property string $auth_key
 * @property string $rules
 * @property boolean $is_root
 * @property string $ip
 * @property integer $last_active_at
 * @property boolean $is_deleted
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 */
class Adm extends ActiveRecord implements IdentityInterface
{

    const STATUS_DELETED = 0;
    const STATUS_BANNED = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{adm}}';
    }

    public static function upLastActive()
    {
        if (!\Yii::$app->user->isGuest) {
            $user = self::findOne(\Yii::$app->user->id);
            if (!is_null($user) && !empty($user)) {
                $user->last_active_at = time();
                $user->save(false);
            }
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // nickname rules
            ['nickname', 'required', 'on' => ['update', 'add']],
            ['nickname', 'match', 'pattern' => '/^[-a-zA-Z0-9]+$/'],
            ['nickname', 'string', 'min' => 3, 'max' => 50],
            ['nickname', 'unique', 'on' => ['update', 'add']],
            ['nickname', 'trim', 'on' => ['update', 'add']],

	        ['password', 'required', 'on' => ['add']],
            ['password', 'string', 'min' => 6, 'on' => ['add', 'update']],
            ['rules', 'required', 'on' => ['update', 'add']],
            ['rules', 'string', 'min' => '1', 'on' => ['update', 'add']],
            ['is_root', 'integer', 'min' => 0, 'max' => 1, 'on' => ['update', 'add']],

            ['is_deleted', 'default', 'value' => self::STATUS_DELETED],
        ];
    }

    public function attributeLabels()
    {
        return array(
            'id' => '#',
            'nickname' => 'Никнейм',
            'password' => 'Пароль',
            'is_root' => 'Супер пользователь',
            'is_banned' => 'Активен',
            'last_active_at' => 'Дата последней авторизации'
        );
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'is_banned' => self::STATUS_BANNED, 'is_deleted' => self::STATUS_DELETED]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['nickname' => $username, 'is_banned' => self::STATUS_BANNED, 'is_deleted' => self::STATUS_DELETED]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'is_banned' => self::STATUS_BANNED,
            'is_deleted' => self::STATUS_DELETED,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int)end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getModel()
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Возвращает серриализарованый массив с правами админа
     * @param type $rules
     * @return type
     */
    public static function serializeRules($rules)
    {
        $newRules = array();
        foreach ($rules as $key => $val) {
            $newRules[mb_strtoupper($key)] = end($val);
        }
        return serialize($newRules);
    }

    /**
     * Разсериализирует права
     * @param $rules
     * @return mixed
     */
    public static function unserializeRules($rules)
    {
        if (!is_array($rules)) {
            return unserialize($rules);
        }
        return $rules;
    }

    /**
     * @description Поиск по выбраным полям
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $status = isset($params['status']) ? Type_Cast::toUInt($params['status']) : 0;
        switch ($status) {
            case 1://Удаленые админы
                $query = self::find()->where(['is_deleted' => 1])->orderBy(['id' => SORT_ASC]);
                break;
            default: //Активные
                $query = self::find()->where(['is_deleted' => 0])->orderBy(['id' => SORT_ASC]);
                break;
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        if (!($this->load($params))) {
            return $dataProvider;
        }
        return $dataProvider;
    }

}