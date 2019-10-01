<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2017-2019 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license.html
 * 
 */
namespace Arikaim\Core\System\Console\Commands\Queue;

use Symfony\Component\Console\Helper\Table;
use Arikaim\Core\System\Console\ConsoleHelper;
use Arikaim\Core\System\Console\ConsoleCommand;
use Arikaim\Core\Arikaim;
use Arikaim\Core\Utils\DateTime;

/**
 * Queue jobs list command
 */
class JobsCommand extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('queue:jobs')->setDescription('Show jobs list.');
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
        $this->showTitle('Queue');  
        $this->showTitle('Recurring Jobs');
        
        $table = new Table($output);
        $table->setHeaders(['','Next Run Time','Handler']);
        $table->setStyle('compact');

        // Recurring jobs
        $items = Arikaim::queue()->getRecuringJobs();
        $rows = [];
        foreach ($items as $item) {                  
            $row = ['',DateTime::dateTimeFormat($item->due_date),$item->handler_class];
            array_push($rows,$row);
        }
        if (empty($rows) == true) {
            array_push($rows,['','..','']);
        }
        $table->setRows($rows);
        $table->render();

        // Scheduled jobs
        $items = Arikaim::queue()->getScheduledJobs();
        $rows = [];
        foreach ($items as $item) {                  
            $row = ['',DateTime::dateTimeFormat($item->schedule_time),$item->handler_class];
            array_push($rows,$row);
        }
        if (empty($rows) == true) {
            array_push($rows,['','..','']);
        }

        $this->showTitle('Scheduled Jobs');
        $table->setHeaders(['','Scheduled Time','Handler']);

        $table->setRows($rows);
        $table->render();

        $this->style->newLine();
    }    
}