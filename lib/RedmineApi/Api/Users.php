<?php
namespace RedmineApi\Api;

/**
 * @author: mix
 * @date: 15.08.14
 */

class Users extends Base
{
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
        $result = $this->accelerate("users", $ids);

        if ($result === false) {
            $result = $this->multiQuery(
                $ids,
                function ($id) {
                    return $this->find($id);
                }
            );
        }
        return $result;
    }
}