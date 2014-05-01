php Redmine Api
==========

a php implementation of redmine rest api:
[http://www.redmine.org/projects/redmine/wiki/Rest_api](http://www.redmine.org/projects/redmine/wiki/Rest_api "rest api")


example:


```php
$client = new \RedmineApi\Client(
    "https://redmine.example.com",             // server url
    "bbb09b217bf17a905a16caa4ce7d4a23a3a0036d" // redmine api key
);
$issueApi = new \RedmineApi\Api\Issues($client);
$issue = $issueApi->find(123);
```



Now implemented:
----------
 * Issues:
    * find issue
    * create issue

 * Issue Relations:
    * find relation for issue
    * create relation
