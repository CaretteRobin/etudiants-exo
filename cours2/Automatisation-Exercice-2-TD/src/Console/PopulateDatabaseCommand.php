<?php

namespace App\Console;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Office;
use Faker\Factory;
use Faker\Generator;
use Slim\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDatabaseCommand extends Command
{
    private App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('db:populate');
        $this->setDescription('Populate database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Populate database...');

        /** @var \Illuminate\Database\Capsule\Manager $db */
        $db = $this->app->getContainer()->get('db');

        $db->getConnection()->statement("SET FOREIGN_KEY_CHECKS=0");
        $db->getConnection()->statement("TRUNCATE `employees`");
        $db->getConnection()->statement("TRUNCATE `offices`");
        $db->getConnection()->statement("TRUNCATE `companies`");
        $db->getConnection()->statement("SET FOREIGN_KEY_CHECKS=1");

        $faker = Factory::create('fr_FR');
        $companyCount = $faker->numberBetween(2, 4);
        $allOffices = [];

        for ($i = 0; $i < $companyCount; $i++) {
            $company = $this->createCompany($faker);
            $officeCount = $faker->numberBetween(2, 3);
            $companyOffices = [];

            for ($j = 0; $j < $officeCount; $j++) {
                $office = $this->createOffice($faker, $company);
                $companyOffices[] = $office;
                $allOffices[] = $office;
            }

            $headOffice = $faker->randomElement($companyOffices);
            $company->head_office_id = $headOffice->id;
            $company->save();
        }

        $employeesTarget = max($faker->numberBetween(10, 12), count($allOffices));
        foreach ($allOffices as $office) {
            $this->createEmployee($faker, $office);
        }

        for ($i = count($allOffices); $i < $employeesTarget; $i++) {
            $this->createEmployee($faker, $faker->randomElement($allOffices));
        }

        $output->writeln('Database populated successfully!');
        return 0;
    }

    private function createCompany(Generator $faker): Company
    {
        $company = new Company();
        $company->name = $faker->company();
        $company->phone = $faker->phoneNumber();
        $company->email = $faker->companyEmail();
        $company->website = $faker->url();
        $company->image = $faker->imageUrl(1200, 800, 'business', true, 'office');
        $company->save();

        return $company;
    }

    private function createOffice(Generator $faker, Company $company): Office
    {
        $city = $faker->city();
        $office = new Office();
        $office->name = $faker->randomElement(['Siège', 'Bureau', 'Agence', 'Antenne']) . ' ' . $city;
        $office->address = $faker->streetAddress();
        $office->city = $city;
        $office->zip_code = $faker->postcode();
        $office->country = $faker->country();
        $office->email = $faker->optional()->companyEmail();
        $office->phone = $faker->optional()->phoneNumber();
        $office->company_id = $company->id;
        $office->save();

        return $office;
    }

    private function createEmployee(Generator $faker, Office $office): Employee
    {
        $employee = new Employee();
        $employee->first_name = $faker->firstName();
        $employee->last_name = $faker->lastName();
        $employee->office_id = $office->id;
        $employee->email = $faker->unique()->safeEmail();
        $employee->phone = $faker->optional()->phoneNumber();
        $employee->job_title = $faker->jobTitle();
        $employee->save();

        return $employee;
    }
}
