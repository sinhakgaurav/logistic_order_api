<?php

namespace App\Http\Repository;

interface RepositoryContract {
    
    public function paginate($limit, $start, $conditions=[]);
    public function find($conditions=[]);
    public function update($data=[], $conditions=[]);
    public function create($data=[]);
}