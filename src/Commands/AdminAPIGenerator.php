<?php
namespace LaravelRocket\Generator\Commands;

use LaravelRocket\Generator\FileUpdaters\APIs\Admin\RouterFileUpdater;
use LaravelRocket\Generator\Generators\APIs\Admin\ControllerGenerator;
use LaravelRocket\Generator\Generators\APIs\Admin\ListResponseGenerator;
use LaravelRocket\Generator\Generators\APIs\Admin\RequestGenerator;
use LaravelRocket\Generator\Generators\APIs\Admin\ResponseGenerator;
use LaravelRocket\Generator\Generators\APIs\Admin\UnitTestGenerator;
use LaravelRocket\Generator\Services\DatabaseService;
use function ICanBoogie\pluralize;
use function ICanBoogie\singularize;

class AdminAPIGenerator extends MWBGenerator
{
    protected $name = 'rocket:make:api:admin';

    protected $signature = 'rocket:make:api:admin {name?} {--file=} {--json=}';

    protected $description = 'Create Admin API for CRUD';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $this->tables = $this->getTablesFromMWBFile();
        if ($this->tables === false) {
            return false;
        }
        $this->getAppJson();

        $this->databaseService = new DatabaseService($this->config, $this->files);
        $this->databaseService->resetDatabase();

        $this->generate();

        $this->databaseService->dropDatabase();

        return true;
    }

    protected function normalizeName(string $name): string
    {
        return snake_case(pluralize($name));
    }

    protected function generate()
    {
        /** @var \LaravelRocket\Generator\Generators\TableBaseGenerator[] $generators */
        $generators = [
            new ResponseGenerator($this->config, $this->files, $this->view),
            new ListResponseGenerator($this->config, $this->files, $this->view),
            new ControllerGenerator($this->config, $this->files, $this->view),
            new UnitTestGenerator($this->config, $this->files, $this->view),
            new RequestGenerator($this->config, $this->files, $this->view),
        ];

        /** @var \LaravelRocket\Generator\FileUpdaters\TableBaseFileUpdater[] $fileUpdaters */
        $fileUpdaters = [
            new RouterFileUpdater($this->config, $this->files, $this->view),
        ];

        $name = $this->normalizeName($this->argument('name'));
        if (!empty($name)) {
            $table = $this->findTableFromName($name);
            if (empty($table)) {
                $this->output('No table definition found: '.$name, 'red');

                return;
            }
            $tables = [$table];
        } else {
            $tables = $this->tables;
        }

        foreach ($tables as $table) {
            $this->output('Processing '.ucfirst(singularize($name)).' Admin API...', 'green');
            foreach ($generators as $generator) {
                $generator->generate($table, $this->tables, $this->json);
            }
            foreach ($fileUpdaters as $fileUpdater) {
                $fileUpdater->insert($table, $this->tables, $this->json);
            }
        }
    }
}
