<?php
/**
 * @author: mix
 * @date: 15.08.14
 */

namespace RedmineApi;

use RedmineApi\Api\IssueRelations;
use RedmineApi\Api\Issues;
use RedmineApi\Api\Sprints;
use RedmineApi\Api\Statuses;
use RedmineApi\Api\Users;

class Factory
{
    private $client;

    private $acc;

    public function __construct(HttpClient $client, MysqlClient $accellerator = null) {
        $this->client = $client;
        $this->acc = $accellerator;
    }

    public function getIssues() {
        return new Issues($this->client, $this->acc);
    }

    public function getIssueRelations() {
        return new IssueRelations($this->client, $this->acc);
    }

    public function getUsers() {
        return new Users($this->client, $this->acc);
    }

    public function getSprints(){
        return new Sprints($this->client, $this->acc);
    }
    
    public function getStatuses(){
        return new Statuses($this->client, $this->acc);
    }
} 