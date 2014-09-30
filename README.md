php Redmine Api
==========

A php implementation of redmine rest api:
[http://www.redmine.org/projects/redmine/wiki/Rest_api](http://www.redmine.org/projects/redmine/wiki/Rest_api "rest api")

Can use mysql dierct access to accellerate multigets

example:


```php
use RedmineApi\Factory;
use RedmineApi\HttpClient;
use RedmineApi\MysqlClient;

$client = new \RedmineApi\HttpClient(
    "https://redmine.example.com",             // server url
    "bbb09b217bf17a905a16caa4ce7d4a23a3a0036d" // redmine api key
);

$acc = new MysqlClient("example.com", "user", "pass", "dbname"); // mysql client to speed fetch

$factory = new Factory($c, $acc);

$uids = [];
foreach ($factory->getIssues()->findByIds([1, 2, 3]) as $issue) {  // multiget of given issues
    $uids[] = $ussue["author_id"];
}

$users = $factory->getUsers()->findByIds($uids); //finding users for issues
```



Now implemented:
----------
 * Issues:
    * find issue
    * create issue
    * update issue
    * findByIds (accellerate with mysql)

 * Issue Relations:
    * findFor (issue)
    * create relation

 * Users:
    * find (by id)
    * findByLogin
    * findByIds (accellerate with mysql)