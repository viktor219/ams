<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class ChangepasswordForm extends Model
{
    public $password;
	public $password_confirmation;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
		//minimum character + alpha + numeric --> strong
        return [
            // username and password are both required
            [['password', 'password_confirmation'], 'required'],
            // password is validated by validatePassword()
            ['password_confirmation', 'validatePassword'],
        ];
    }
    
    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || ($this->password!==$this->password_confirmation)) {
                $this->addError($attribute, 'Password confirmation failed.');
            }
        }
    }
    

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findOne(Yii::$app->user->id);
        }

        return $this->_user;
    }
}
