<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2017-2019 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license.html
 * 
 */
namespace Arikaim\Core\System\Console\Commands;

use Symfony\Component\Console\Question\Question;
use Arikaim\Core\System\Console\ConsoleCommand;
use Arikaim\Core\System\Console\ConsoleHelper;
use Arikaim\Core\Db\Model;

/**
 * Reset control panel user command
 */
class AdminResetCommand extends ConsoleCommand
{  
    /**
     * Command config
     * name: install
     * @return void
     */
    protected function configure()
    {
        $this->setName('admin:reset')->setDescription('Reset control panel user password');
    }

    /**
     * Command code
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function executeCommand($input, $output)
    {
        $this->showTitle('Reset control panel user password');
         
        $helper = $this->getHelper('question');
        $validator = function($value) {                
            if (empty(trim($value)) == true) {
                throw new \Exception('Cannot be empty');              
                return null;
            }
            return $value;
        };
        $question = new Question("Enter new password: ",null);    
        $question->setValidator($validator);      
        $new_password = trim($helper->ask($input, $output, $question));
        
        $question = new Question("Repeat new passsord: ");
        $repeat_pasword = trim($helper->ask($input, $output, $question));

        if ($new_password != $repeat_pasword) {
            $this->showError("New password and repeat password mot mach!");
            return;
        }
        $user = Model::create('Users')->getControlPanelUser();
        if (is_object($user) == false) {
            $this->showError("Missing control panel user!");
            return;
        }
        
        $result = $user->changePassword($user->id,$new_password);
        if ($result == true) {
            $this->showCompleted();            
        } else {
            $this->showError("Can't change control panel user password!");
        }

        return;
    }
}