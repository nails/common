<?php

namespace Nails\Console\Apps;

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Command\Command;

class CORE_NAILS_App extends Command
{
    /**
     * Confirms something with the user
     * @param  string          $question The question to confirm
     * @param  boolean         $default  The default answer
     * @param  InputInterface  $input    The Input Interface proivided by Symfony
     * @param  OutputInterface $output   The Output Interface proivided by Symfony
     * @return string
     */
    protected function confirm($question, $default, $input, $output)
    {
        $question      = is_array($question) ? implode("\n", $question) : $question;
        $helper        = $this->getHelper('question');
        $defaultString = $default ? 'Y' : 'N';
        $question      = new ConfirmationQuestion($question . ' [' . $defaultString . ']: ', $default) ;

        return $helper->ask($input, $output, $question);
    }

    // --------------------------------------------------------------------------

    /**
     * Asks the user for some input
     * @param  string          $question The question to ask
     * @param  mixed           $default  The default answer
     * @param  InputInterface  $input    The Input Interface proivided by Symfony
     * @param  OutputInterface $output   The Output Interface proivided by Symfony
     * @return string
     */
    protected function ask($question, $default, $input, $output)
    {
        $question = is_array($question) ? implode("\n", $question) : $question;
        $helper   = $this->getHelper('question');
        $question = new Question($question . ' [' . $default . ']: ', $default) ;

        return $helper->ask($input, $output, $question);
    }
}
