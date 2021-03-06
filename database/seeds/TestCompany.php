<?php

namespace Database\Seeds;

use App\Abstracts\Model;
use App\Jobs\Auth\CreateUser;
use App\Jobs\Common\CreateCompany;
use App\Jobs\Common\CreateContact;
use App\Traits\Jobs;
use Illuminate\Database\Seeder;

class TestCompany extends Seeder
{
    use Jobs;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(Roles::class);

        $this->createCompany();

        $this->createUser();

        $this->createContact();

        Model::reguard();
    }

    private function createCompany()
    {
        $company = $this->dispatch(new CreateCompany([
            'name' => 'My Company',
            'domain' => 'company.com',
            'address' => 'New Street 1254',
            'currency' => 'USD',
            'locale' => 'en-GB',
            'enabled' => '1',
            'settings' => [
                'schedule.send_invoice_reminder' => '1',
                'schedule.send_bill_reminder' => '1',
                'wizard.completed' => '1',
            ],
        ]));

        session(['company_id' => $company->id]);

        $this->command->info('Test company created.');
    }

    public function createUser()
    {
        $this->dispatch(new CreateUser([
            'name' => 'Test User',
            'email' => 'test@company.com',
            'password' => '123456',
            'locale' => 'en-GB',
            'companies' => [session('company_id')],
            'roles' => ['1'],
            'enabled' => '1',
        ]));

        $this->command->info('Test user created.');
    }

    private function createContact()
    {
        $this->dispatch(new CreateContact([
            'type' => 'customer',
            'name' => 'Test Contact',
            'email' => 'contact@company.com',
            'currency_code' => setting('default.currency', 'USD'),
            'password' => '123456',
            'password_confirmation' => '123456',
            'company_id' => session('company_id'),
            'enabled' => '1',
            'create_user' => 1,
        ]));

        $this->command->info('Test contact created.');
    }
}
