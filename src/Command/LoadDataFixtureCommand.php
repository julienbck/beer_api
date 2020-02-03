<?php

namespace App\Command;

use App\Entity\Beer;
use App\Entity\Brewery;
use App\Entity\Style;
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

    protected $styles = [];

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
            $totalStyleLoaded = $this->loadStyleInDatabase();
            $this->hydrateBeer();
            $totalBeerLoaded = $this->loadBeerInDatabase();
        }

        $totalBrewery = $totalBreweryLoaded ? $totalBreweryLoaded : 0;
        $this->io->success('We have load '.$totalBrewery.' brewery and load '.$totalBeerLoaded.' beers and load '.$totalStyleLoaded.' styles');

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
            $this->hydrateBreweryAndStyle($arrayAssociated);
        }

        $this->removeDuplicated('breweries');
        $this->removeDuplicated('styles');
    }

    private function hydrateBreweryAndStyle(array $data)
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

        if ($data['style_id'] > 0) {
            $style = new Style();

            $style
                ->setId($data['style_id'])
                ->setName($data['Style'])
                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime());

            $this->styles[] = $style;
        }
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
            if($counterFlush % 100  === 0) {
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

    private function loadStyleInDatabase(): int
    {
        $this->io->write('Start load style in database', true);
        $this->io->progressStart(count($this->styles));

        $counterFlush = 0;
        foreach ($this->styles as $style) {
            $this->em->persist($style);
            $metadata = $this->em->getClassMetaData(get_class($style));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new AssignedGenerator());
            if($counterFlush % 20  === 0) {
                $this->em->flush();
                $this->em->clear(Style::class);
            }
            $counterFlush++;
            $this->io->progressAdvance();
        }

        $this->em->flush();
        $this->em->clear(Style::class);

        $this->io->progressFinish();
        $this->io->write('End load style in database', true);

        return $counterFlush;
    }

    private function hydrateBeer()
    {
        $this->breweries = $this->em->getRepository(Brewery::class)->findAll();
        $this->styles = $this->em->getRepository(Style::class)->findAll();
        foreach ($this->arrayAssociated as $data) {
            $brewery = array_filter(
                $this->breweries,
                function ($e) use ($data) {
                    return $e->getId() == $data['brewery_id'];
                }
            );

            $beer = new Beer();
            $beer
                ->setId($data['id'])
                ->setName($data['Name'])
                ->setIbu($data['International Bitterness Units'])
                ->setAbv($data['Alcohol By Volume'])
                ->setBrewery(reset($brewery))
                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime());

            if ($data['style_id'] > 0 ) {
                $style = array_filter(
                    $this->styles,
                    function ($e) use ($data) {
                        return $e->getId() == $data['style_id'];
                    }
                );
                $beer->setStyle(reset($style));
            }

            $this->beers[] = $beer;
        }
        $this->removeDuplicated('beers');
    }


    private function loadBeerInDatabase()
    {
        $this->io->write('Start load beer in database', true);
        $this->io->progressStart(count($this->beers));
        $counterFlush = 0;
        foreach ($this->beers as $beer) {
            $this->em->persist($beer);
            $metadata = $this->em->getClassMetaData(get_class($beer));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new AssignedGenerator());
            if($counterFlush % 100  === 0) {
                $this->em->flush();
                $this->em->clear(Beer::class);
            }
            $counterFlush++;
            $this->io->progressAdvance();
        }

        $this->em->flush();
        $this->em->clear(Beer::class);

        $this->io->progressFinish();
        $this->io->write('End load brewery in database', true);

        return $counterFlush;
    }

    private function removeDuplicated(string $attribute)
    {
        $cleaned = [];
        foreach ($this->$attribute as $item) {
            $cleaned[$item->getId()] = $item;
        }
        $this->$attribute = $cleaned;
    }
}
