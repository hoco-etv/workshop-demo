<?php

namespace app\models;

class User extends \yii\base\BaseObject implements \yii\web\IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;
    public $isAdmin;
    public $email;
    public $first_name, $last_name;
    private static $users = [
        0 => [
            'id' => 0,
            'first_name' => 'Admin',
            'last_name' => '',
            'username' => 'admin',
            'password' => '21232f297a57a5a743894a0e4a801fc3',
            'authKey' => 'adminKey',
            'accessToken' => 'fefef5e1e794444265c93384a0d04c83',
            'isAdmin' => True,
            'email' => '',
        ],
        1 => [
            'id' => 1,
            'first_name' => 'Bram',
            'last_name' => 'den Ouden',
            'username' => 'Bram',
            'password' => 'PoahMooieSite!',
            'authKey' => 'bramKey',
            'accessToken' => 'cd6ca9570c069539e72c37c6598c524f',
            'isAdmin' => True,
            'email' => 'b.l.denouden@student.tudelft.nl',
        ],
        2 => [
            'id' => 2,
            'first_name' => 'Werner',
            'last_name' => 'van Dijk',
            'username' => 'Werner',
            'password' => 'WernerLogJeInDan?',
            'authKey' => 'wernerKey',
            'accessToken' => '02e979375104f29963837c210952ad8a',
            'isAdmin' => True,
            'email' => 'contact@wernervandijk.nl',
        ],
        3 => [
            'id' => 3,
            'first_name' => 'Max',
            'last_name' => 'Deutman',
            'username' => 'Max',
            'password' => '#IkBetaalWel!',
            'authKey' => 'maxKey',
            'accessToken' => '0c6548a0ee693b84dbf8173d8f5571c3',
            'isAdmin' => True,
            'email' => 'thesaurier-etv@tudelft.nl',
        ],
        4 => [
            'id' => 4,
            'first_name' => 'Bestuur',
            'last_name' => '',
            'username' => 'Bestuur',
            'password' => 'IsDeKoffieAlKlaar?',
            'authKey' => 'bestuurKey',
            'accessToken' => '7fa6c6e457ce30228f9b7db2635e74d9',
            'isAdmin' => True,
            'email' => 'bestuur-etv@tudelft.nl',
        ],
        5 => [
            'id' => 5,
            'first_name' => 'Tom',
            'last_name' => 'Salden',
            'username' => 'Tom',
            'password' => '#TomTheTankEngine',
            'authKey' => 'tomKey',
            'accessToken' => 'ebf6812271466f0a6337fcccd55f405e',
            'isAdmin' => True,
            'email' => 't.v.a.salden@student.tudelft.nl',
        ],
        6 => [
            'id' => 6,
            'first_name' => 'Klushok',
            'last_name' => '',
            'username' => 'klushok',
            'password' => 'ThisPasswordMustNotBeUsedAtAnyTime!!!',
            'authKey' => 'klushokKey',
            'accessToken' => '4cdab1f29feb0d29c68f861974084ebf',
            'isAdmin' => True,
            'email' => 'klushok-etv@tudelft.nl',
        ],
    ];
    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }
    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }
        return null;
    }
    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        foreach (self::$users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }
        return null;
    }
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }
    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }
    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
    }

    /**
     * Check if user is admin
     * 
     * @return bool if user is admin
     */
    public function isAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * Finds all users and returns their id, full name and email
     * 
     * @return mixed array of users
     */

    public static function findUserIdEmail()
    {
        $output = [];
        foreach (self::$users as $user) {
            $safeData = [
                'id' => $user['id'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email']
            ];
            array_push($output, $safeData);
        }
        return $output;
    }
}
