<?php

namespace App\Command;

use App\Entity\Film;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class CsvImportCommand
 * @package App\ConsoleCommand
 */
class CreateFilmFromCsvCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * CsvImportCommand constructor.
     *
     * @param EntityManagerInterface $em
     *
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    /**
     * Configure
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName('csv:import')
            ->setDescription('Imports the mock CSV data file')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Attempting import of Feed...');

        $reader = Reader::createFromPath('%kernel.root_dir%/../var/uploads/films.csv');

        $results = $reader->fetchAssoc();

        $io->progressStart(iterator_count($results));

        foreach ($results as $row) {

            $film = (new Film())
                ->setNom($row['nom'])
                ->setDescription($row['description'])
                ->setNote($row['note'])
                ->setNb_votants($row['nb_votants'])
            ;

            $this->em->persist($film);
            $this->em->flush();

        $io->progressFinish();
        $io->success('Command exited cleanly!');
        }
    }
}