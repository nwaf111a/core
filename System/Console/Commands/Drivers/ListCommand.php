<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2017-2019 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license.html
 * 
 */
namespace Arikaim\Core\System\Console\Commands\Drivers;

use Symfony\Component\Console\Helper\Table;
use Arikaim\Core\System\Console\ConsoleCommand;
use Arikaim\Core\System\Console\ConsoleHelper;
use Arikaim\Core\Arikaim;

/**
 * Drivers list command
 */
class ListCommand extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('drivers:list')->setDescription('Drivers list');
    }

    /**
     * Run command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function executeCommand($input, $output)
    {
        $this->showTitle('Drivers');
      
        $table = new Table($output);
        $table->setHeaders(['Status','Name','Display Name','Category','Full Name','Version']);
        $table->setStyle('compact');

        $items = Arikaim::driver()->getList();

        $rows = [];
        foreach ($items as $driver) {
           
            $label = ($driver->status == 1) ? ConsoleHelper::getLabelText('enabled','green') : ConsoleHelper::getLabelText('disabled','red');
            $row = [$label,$driver->name,$driver->title,$driver->category,$driver->full_name,$driver->version];
            array_push($rows,$row);
        }

        $table->setRows($rows);
        $table->render();
        $this->style->newLine();
    }
}