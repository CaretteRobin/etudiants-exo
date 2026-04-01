<?php

namespace App;

interface QueryBuilderInterface
{
    public function select(array $fields): QueryBuilderInterface;

    public function from(string $table): QueryBuilderInterface;

    public function where(string $condition): QueryBuilderInterface;

    public function build(): string;
}
