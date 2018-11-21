<?php

namespace App/Repository;

interface RepositoryContract {
    
    public function all();
    public function find();
    public function paginate();
    public function update();
    public function create();
}