<?php

declare(strict_types=1);

namespace App\Tests\Helper;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class TestQuestionHelper
 *
 * Used to override hidden questions so they can be unit tested like all other questions
 * @link https://github.com/symfony/symfony/issues/19463
 *
 * Solution from https://github.com/symfony/symfony/issues/19463#issuecomment-322782602
 *
 */
final class TestQuestionHelper extends QuestionHelper
{
    public function ask(InputInterface $input, OutputInterface $output, Question $question): mixed
    {
        if ($question->isHidden()) {
            $question->setHidden(false);
        }
        return parent::ask($input, $output, $question);
    }
}
