<?php

namespace App\Repository;

interface RepositoryContract {
    
    public function paginate($limit=10, $start=0, $conditions=[]);
    public function find($conditions=[]);
    public function update($data=[], $conditions=[]);
    public function create($data=[]);
}