<?php

namespace App\Command;

use App\Entity\Beer;
use App\Entity\Brewery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Finder\Finder;

class LoadDataFixtureCommand extends Command
{
    protected static $defaultName = 'load:data-fixture';

    protected $arrayCsvParsed = [];

    protected $breweries = [];

    protected $beers = [];

    protected $em;

    protected $arrayAssociated = [];

    protected $date;

    protected $io;

    public function __construct(string $name = null, EntityManagerInterface $em)
    {
        parent::__construct($name);
        $this->em = $em;
        $this->date = new \DateTime();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('file_path', InputArgument::REQUIRED, 'File path to load data csv')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $path = $input->getArgument('file_path');

        if (!empty($path)) {
            if (!file_exists($path) || !is_readable($path)) {
                throw new FileNotFoundException(null, 404, null, $path);
            }

            $this->io->note(sprintf('you declare file: %s  to load data in database', $path));

            $this->parseCsvAndHydrateBreweryEntity($path);
            $totalBreweryLoaded = $this->loadBreweryInDatabase();
        }

        $totalBrewery = $totalBreweryLoaded ? $totalBreweryLoaded : 0;
        $this->io->success('We have load '.$totalBrewery.' brewery and load '.$totalBeerLoaded.' beers');

        return 0;
    }

    private function parseCsvAndHydrateBreweryEntity(string $csvPath)
    {
        // create an array of header csv
        $csvArray = file($csvPath);
        $header = str_getcsv(array_shift($csvArray), ';');
        $countElementHeader = count($header);
        foreach ($csvArray as $row) {
            $csvRowParsed = str_getcsv($row, ';');
            $countCsvRecord = count($csvRowParsed);

            //check if can combine the header array and the row analyzed
            if ($countElementHeader != $countCsvRecord ) {
                continue;
            }

            $arrayAssociated = array_combine($header, $csvRowParsed);

            if (empty($arrayAssociated['Brewer'])) {
                continue;
            }

            $this->arrayAssociated[] = $arrayAssociated;

            $this->hydrateBrewery($arrayAssociated);
        }

        $this->removeDuplicatedBrewery();
    }

    private function hydrateBrewery(array $data)
    {
        $brewery = new Brewery();
        $brewery
            ->setId((int)$data['brewery_id'])
            ->setName($data['Brewer'])
            ->setAddress($data['Address'])
            ->setCity($data['City'])
            ->setCountry($data['Country'])
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        $this->breweries[] = $brewery;
    }

    private function loadBreweryInDatabase(): int
    {
        $this->io->write('Start load brewery in database', true);
        $this->io->progressStart(count($this->breweries));

        $counterFlush = 0;
        foreach ($this->breweries as $brewery) {
            $this->em->persist($brewery);
            $metadata = $this->em->getClassMetaData(get_class($brewery));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new AssignedGenerator());
            if($counterFlush % 50  === 0) {
                $this->em->flush();
                $this->em->clear(Brewery::class);
            }
            $counterFlush++;
            $this->io->progressAdvance();
        }

        $this->em->flush();
        $this->em->clear(Brewery::class);

        $this->io->progressFinish();
        $this->io->write('End load brewery in database', true);

        return $counterFlush;
    }


    private function removeDuplicatedBrewery()
    {
        $cleaned = [];
        foreach ($this->breweries as $brewery) {
            $cleaned[$brewery->getId()] = $brewery;
        }
        $this->breweries = $cleaned;
    }
}
