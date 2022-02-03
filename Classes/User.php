<?php

/**
 * Автор: Ермаченя Александр
 *
 * Дата реализации: 31.01.2022 13:11
 *
 * Дата изменения: 03.02.2022 13:00
 *
 * Утилита для работы с базой данных
*/

namespace Classes;

use PDO;

/**
 * User
 * Основой класс для создания удаления и вывода информации пользователе.
 * Методы каласса передаются в класс UserCoiiection.
 */
class User
{
    protected $id;
    protected $name;
    protected $surname;
    protected $dateOfBirth;
    protected $gender;
    protected $cityOfBirth;
    public $db;

    /**
     * Используется для создания пользователя или вывода информации
     * о пользователе по Id и сохранение их в переменные.
     *
     * @param array ...$arrUserInfo Массив значений. Может содержать 5 или 1 значение.
     */
    public function __construct(...$arrUserInfo)
    {
        $config = array(
            'host'     => 'localhost',
            'name'     => 'users',
            'user'     => 'root',
            'password' => '',
        );
        $this->db = new PDO(
            'mysql:host=' . $config['host'] . ';dbname=' . $config['name'] . ';' . 'charset=utf8;',
            $config['user'],
            $config['password']
        );
        if (count($arrUserInfo) === 1) {
            [$this->id] = $arrUserInfo;
            $this->currentUser($this->id);
        }
        if (count($arrUserInfo) === 5) {
            list($name, $surname, $dateOfBirth, $gender, $cityOfBirth) = $arrUserInfo;
            $this->addUser($name, $surname, $dateOfBirth, $gender, $cityOfBirth);
        }
    }

    public function addUser($name, $surname, $dateOfBirth, $gender, $cityOfBirth)
    {
        $name = preg_replace('/([[:alpha:]]*)/', '$1', $name);
        $surname = preg_replace('/([[:alpha:]]*)/', '$1', $surname);

        $query = $this->db->prepare("INSERT INTO `users`"
               . "( `name`, `surname`, `date_of_birth`, `gender`, `city_of_birth`)"
               . "VALUES ( :name, :surname, :date_of_birth, :gender, :city_of_birth)");
        $query->execute(array(
            'name'          => $name,
            'surname'       => $surname,
            'date_of_birth' => $dateOfBirth,
            'gender'        => $gender,
            'city_of_birth' => $cityOfBirth,
        ));
        return $this->db->lastInsertId();
    }

    public function deleteUser($id)
    {
        $query = $this->db->prepare("DELETE FROM `users` WHERE id = :id");
        $query->execute(array(
            'id' => $id,
        ));
    }

    public function currentUser($id)
    {
        $query = $this->db->prepare("SELECT * FROM `users` WHERE id = :id");
        $query->execute(array(
            'id' => $id,
        ));
        list($this->id, $this->name, $this->surname,$this->dateOfBirth,
            $this->gender, $this->cityOfBirth) = $query->fetch();
    }

    public function userObject()
    {
        $gender = self:: gender($this->gender);
        $dateOfBirth = self:: userAge($this->dateOfBirth);
        return (object) array(
            'id'            => $this->id,
            'name'          => $this->name,
            'surname'       => $this->surname,
            'date_of_birth' => $dateOfBirth,
            'gender'        => $gender,
            'city_of_birth' => $this->cityOfBirth,
        );
    }

    public static function userAge($birthday)
    {
        $birthdayTimestamp = strtotime($birthday);
        $age = date('Y') - date('Y', $birthdayTimestamp);
        if (date('md', $birthdayTimestamp) > date('md')) {
            $age--;
        }
        return $age;
    }

    public static function gender($bool)
    {
        return $bool === 0 ? 'women' : 'men';
    }
}
