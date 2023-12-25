<?php

namespace Kento1221\UserUsergroupCrudApp\Controllers;

class Controller
{
    protected array $data = [];

    public function __construct() {}

    protected function assignData(array $data): void
    {
        $this->data = array_merge($this->data, $data);
    }

    protected function render($view): void
    {
        extract($this->data);
        require_once __DIR__ . '/../Views/' . $view . '.php';
    }

    protected function jsonResponse(array $data): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }
}