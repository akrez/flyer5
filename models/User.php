<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 * @property string $status
 * @property string $token
 * @property string $passwordHash
 * @property string $resetToken
 * @property string $resetAt
 * @property string $email
 * @property string $mobile
 */
class User extends ActiveRecord implements IdentityInterface
{

    const TIMEOUT_RESET = 120;

    public $password;
    public $_user;

    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [0 => ['email',], 1 => 'required', 'on' => 'signup',],
            [0 => ['email',], 1 => 'unique', 'on' => 'signup',],
            [0 => ['email',], 1 => 'email', 'on' => 'signup',],
            [0 => ['password',], 1 => 'required', 'on' => 'signup',],
            [0 => ['password',], 1 => 'minLenValidation', 'params' => ['min' => 6,], 'on' => 'signup',],
            [0 => ['email',], 1 => 'required', 'on' => 'signin',],
            [0 => ['email',], 1 => 'email', 'on' => 'signin',],
            [0 => ['password',], 1 => 'required', 'on' => 'signin',],
            [0 => ['password',], 1 => 'passwordValidation', 'on' => 'signin',],
            [0 => ['password',], 1 => 'minLenValidation', 'params' => ['min' => 6,], 'on' => 'signin',],
            [0 => ['email',], 1 => 'required', 'on' => 'resetPasswordRequest',],
            [0 => ['email',], 1 => 'findValidUserByEmailValidation', 'on' => 'resetPasswordRequest',],
            [0 => ['email',], 1 => 'email', 'on' => 'resetPasswordRequest',],
            [0 => ['email',], 1 => 'required', 'on' => 'resetPassword',],
            [0 => ['email',], 1 => 'findValidUserByEmailResetTokenValidation', 'on' => 'resetPassword',],
            [0 => ['email',], 1 => 'email', 'on' => 'resetPassword',],
            [0 => ['password',], 1 => 'required', 'on' => 'resetPassword',],
            [0 => ['password',], 1 => 'minLenValidation', 'params' => ['min' => 6,], 'on' => 'resetPassword',],
            [0 => ['resetToken',], 1 => 'required', 'on' => 'resetPassword',],
        ];
    }

    /////

    public static function findIdentity($id)
    {
        return static::find()->where(['id' => $id])->andWhere(['status' => [Status::STATUS_ACTIVE, Status::STATUS_DISABLE]])->one();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()->where(['token' => $token])->andWhere(['status' => [Status::STATUS_UNVERIFIED, Status::STATUS_ACTIVE, Status::STATUS_DISABLE]])->one();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->token;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /////

    public function passwordValidation($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = User::findValidUserByEmail($this->email);
            if ($user && $user->validatePassword($this->password)) {
                return $this->_user = $user;
            }
            $this->addError($attribute, Yii::t('yii', '{attribute} is invalid.', ['attribute' => $this->getAttributeLabel($attribute)]));
        }
        return $this->_user = null;
    }

    public function findValidUserByEmailValidation($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = User::findValidUserByEmail($this->email);
            if ($user) {
                return $this->_user = $user;
            }
            $this->addError($attribute, Yii::t('yii', '{attribute} is invalid.', ['attribute' => $this->getAttributeLabel($attribute)]));
        }
        return $this->_user = null;
    }

    public function findValidUserByEmailResetTokenValidation($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = User::findValidUserByEmailResetToken($this->email, $this->resetToken);
            if ($user) {
                return $this->_user = $user;
            }
            $this->addError($attribute, Yii::t('yii', '{attribute} is invalid.', ['attribute' => $this->getAttributeLabel($attribute)]));
        }
        return $this->_user = null;
    }

    public function minLenValidation($attribute, $params, $validator)
    {
        $min = $params['min'];
        if (strlen($this->$attribute) < $min) {
            $this->addError($attribute, Yii::t('yii', '{attribute} must be no less than {min}.', ['min' => $min, 'attribute' => $this->getAttributeLabel($attribute)]));
        }
    }

    public function maxLenValidation($attribute, $params, $validator)
    {
        $max = $params['max'];
        if ($max < strlen($this->$attribute)) {
            $this->addError($attribute, Yii::t('yii', '{attribute} must be no greater than {max}.', ['max' => $max, 'attribute' => $this->getAttributeLabel($attribute)]));
        }
    }

    public function setPasswordHash($password)
    {
        $this->passwordHash = Yii::$app->security->generatePasswordHash($password);
    }

    public function setAuthKey()
    {
        return $this->token = Yii::$app->security->generateRandomString();
    }

    public function setResetToken()
    {
        if (empty($this->resetToken) || time() - self::TIMEOUT_RESET > $this->resetAt) {
            $this->resetToken = self::generateResetToken();
        }
        $this->resetAt = time();
    }

    public static function findValidUserByEmail($email)
    {
        return self::find()->where(['status' => [Status::STATUS_UNVERIFIED, Status::STATUS_ACTIVE, Status::STATUS_DISABLE]])->andWhere(['email' => $email])->one();
    }

    public static function findValidUserByEmailResetToken($email, $resetToken)
    {
        return self::find()->where(['status' => [Status::STATUS_UNVERIFIED, Status::STATUS_ACTIVE, Status::STATUS_DISABLE]])->andWhere(['email' => $email])->andWhere(['resetToken' => $resetToken])->andWhere(['>', 'resetAt', time() - self::TIMEOUT_RESET])->one();
    }

    public function generateResetToken()
    {
        do {
            $rand = rand(10000, 99999);
            $model = self::find()->where(['resetToken' => $rand])->one();
        } while ($model != null);
        return $rand;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->passwordHash);
    }

    public function getUser()
    {
        return $this->_user;
    }

}
