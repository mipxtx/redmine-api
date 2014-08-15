<?php
/**
 * @author: mix
 * @date: 15.08.14
 */

namespace RedmineApi;

use RedmineApi\Api\IssueRelations;
use RedmineApi\Api\Issues;

class Factory {

    private $client;

    public function construct(Client $client){
        $this->client = $client;
    }

    public function getIssues(){
        return new Issues($this->client);
    }
    public function getIssueRelations(){
        return new IssueRelations($this->client);
    }
} 