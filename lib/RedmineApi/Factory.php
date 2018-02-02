<?php
/**
 * @author: mix
 * @date: 15.08.14
 */

namespace RedmineApi;

use RedmineApi\Api\CustomFields;
use RedmineApi\Api\IssueRelations;
use RedmineApi\Api\Issues;
use RedmineApi\Api\Statuses;
use RedmineApi\Api\TimeEntries;
use RedmineApi\Api\Trackers;
use RedmineApi\Api\Users;
use RedmineApi\Sql\MysqlClient;

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

    public function getStatuses() {
        return new Statuses($this->client, $this->acc);
    }

    public function getCustomFields(){
        return new CustomFields($this->client, $this->acc);
    }

    public function getTimeEntries(){
        return new TimeEntries($this->client, $this->acc);
    }

    public function getTrackers(){
        return new Trackers($this->client, $this->acc);
    }
} 