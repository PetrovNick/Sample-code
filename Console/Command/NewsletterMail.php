<?php

namespace Extait\Articles\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Extait\Articles\Helper\NewsLetter;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;

/**
 * Class NewsletterMail
 * @package Extait\Articles\Console\Command
 */
class NewsletterMail extends Command
{
    /**
     * Subscriber email
     */
    const EMAIL = 'e';

    /**
     * Number of month
     */
    const MONTH = 'm';

    /**
     * Help command
     */
    const HELP = 'help';

    /**
     * @var NewsLetter
     */
    protected $newsletter;

    /**
     * @var State
     */
    protected $state;

    /**
     * NewsletterMail constructor.
     * @param NewsLetter $newsLetter
     * @param State $state
     */
    public function __construct(
        NewsLetter $newsLetter,
        State $state
    ) {
        $this->newsletter = $newsLetter;
        $this->state = $state;
        parent::__construct();
    }

    /**
     * Set command name/description/options
     */
    protected function configure()
    {
        $this->setName('extait:digest:send')
            ->setDescription('Send emails with newsletter')
            ->setDefinition($this->getOptions());
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(Area::AREA_FRONTEND);
        $email = $input->getOption(self::EMAIL);
        $month = $input->getOption(self::MONTH);
        if (empty($email)) {
            throw new InvalidArgumentException('Missing require arguments [-help, -e]');
        }
        if (intval($month) || empty($month)) {
            $this->newsletter->notify(array($email), $month);
        } else {
            throw new InvalidArgumentException('Invalid argument -m. Must be integer');
        }
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            new InputOption(self::EMAIL, '-e', InputOption::VALUE_REQUIRED, 'Subscriber email'),
            new InputOption(self::MONTH, '-m', InputOption::VALUE_OPTIONAL, 'Month number'),
        ];
    }
}
