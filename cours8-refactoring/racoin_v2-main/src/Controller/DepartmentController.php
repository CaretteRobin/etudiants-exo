<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Department;

class DepartmentController
{
    public function getAllDepartments(): array
    {
        return Department::orderBy('nom_departement')->get()->toArray();
    }
}
