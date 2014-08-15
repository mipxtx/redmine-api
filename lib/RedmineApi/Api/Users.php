<?php
namespace RedmineApi\Api;

/**
 * @author: mix
 * @date: 15.08.14
 */

class Users extends Base
{
    public function find($id) {
        $user = $this->request("GET", "/users/$id.json");
        if ($user["user"]) {
            return $user["user"];
        }

        return null;
    }

    public function findByLogin($login) {
        $users = $this->request("GET", "/users.json", ["name" => $login]);

        if (isset($users["users"][0])) {
            return $users["users"][0];
        }

        return null;
    }

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