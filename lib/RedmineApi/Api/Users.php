<?php
namespace RedmineApi\Api;

/**
 * @author: mix
 * @date: 15.08.14
 */

class Users extends Base
{
    const TABLE = 'users';

    /**
     * find user by id
     *
     * @param $id
     * @return null
     */
    public function find($id) {
        $user = $this->request("GET", "/users/$id.json");
        if ($user["user"]) {
            return $user["user"];
        }

        return null;
    }

    /**
     * find user by login
     * @param $login
     * @return null
     */
    public function findByLogin($login) {
        $users = $this->request("GET", "/users.json", ["name" => $login]);

        if (isset($users["users"][0])) {
            return $users["users"][0];
        }

        return null;
    }

    /**
     * multiget for users
     *
     * @param array $ids
     * @return array|bool
     */
    public function findByIds(array $ids) {
        return $this->multiAccelerate(self::TABLE, $ids, function ($id) { return $this->find($id); });
    }

    /**
     * @param array $logins
     * @return array
     */
    public function findByLogins(array $logins) {
        return $this->multiAccelerate(self::TABLE, $logins, function ($id) { return $this->findByLogin($id); }, "login");
    }

    /**
     * @param array $emails
     * @return array
     */
    public function findByEmails(array $emails) {
        return $this->multiAccelerate(self::TABLE, $emails, function ($id) { return $this->findByLogin($id); }, "email");
    }
}
