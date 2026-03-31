<?php

namespace App\Command;

use App\Entity\Artist;
use App\Entity\Card;
use App\Repository\ArtistRepository;
use App\Repository\CardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressIndicator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'import:card',
    description: 'Add a short description for your command',
)]
class ImportCardCommand extends Command
{
    private const DEFAULT_BATCH_SIZE = 500;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        private array $csvHeader = []
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Maximum number of rows to read from the CSV')
            ->addOption('batch-size', null, InputOption::VALUE_REQUIRED, 'Flush frequency for the import', (string) self::DEFAULT_BATCH_SIZE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '2G');
        $io = new SymfonyStyle($input, $output);
        $filepath = __DIR__ . '/../../data/cards.csv';
        $start = microtime(true);
        $limit = $this->getPositiveIntegerOption($input, 'limit');
        $batchSize = $this->getPositiveIntegerOption($input, 'batch-size') ?? self::DEFAULT_BATCH_SIZE;
        $handle = fopen($filepath, 'r');

        $this->logger->info('Card import started', [
            'file' => $filepath,
            'limit' => $limit,
            'batchSize' => $batchSize,
        ]);

        if ($handle === false) {
            $this->logger->error('Card import failed: source file missing', ['file' => $filepath]);
            $io->error('File not found');

            return Command::FAILURE;
        }

        $progressIndicator = new ProgressIndicator($output);
        $progressIndicator->start('Importing cards...');

        try {
            $header = fgetcsv($handle);
            if ($header === false) {
                throw new RuntimeException('The CSV file is empty.');
            }
            $this->csvHeader = $header;

            /** @var CardRepository $cardRepository */
            $cardRepository = $this->entityManager->getRepository(Card::class);
            /** @var ArtistRepository $artistRepository */
            $artistRepository = $this->entityManager->getRepository(Artist::class);

            $knownUuids = array_fill_keys($cardRepository->getAllUuids(), true);
            $artistIdsByExternalId = $artistRepository->getIndexedByExternalId();
            $pendingArtistsByExternalId = [];
            $processedRows = 0;
            $importedCards = 0;
            $skippedCards = 0;

            while (($row = $this->readCSV($handle)) !== false) {
                if ($limit !== null && $processedRows >= $limit) {
                    break;
                }

                $processedRows++;
                $uuid = (string) ($row['uuid'] ?? '');

                if ($uuid === '' || isset($knownUuids[$uuid])) {
                    $skippedCards++;

                    continue;
                }

                $this->entityManager->persist(
                    $this->createCard($row, $artistIdsByExternalId, $pendingArtistsByExternalId)
                );
                $knownUuids[$uuid] = true;
                $importedCards++;

                if ($processedRows % $batchSize === 0) {
                    $this->flushBatch($pendingArtistsByExternalId, $artistIdsByExternalId);
                    $progressIndicator->advance();
                }
            }

            $this->flushBatch($pendingArtistsByExternalId, $artistIdsByExternalId);
            $progressIndicator->finish('Importing cards done.');
            fclose($handle);

            $timeElapsed = microtime(true) - $start;
            $this->logger->info('Card import finished', [
                'processedRows' => $processedRows,
                'importedCards' => $importedCards,
                'skippedCards' => $skippedCards,
                'durationMs' => (int) round($timeElapsed * 1000),
            ]);
            $io->success(sprintf(
                'Processed %d rows, imported %d cards and skipped %d cards in %.2f seconds.',
                $processedRows,
                $importedCards,
                $skippedCards,
                $timeElapsed
            ));

            return Command::SUCCESS;
        } catch (\Throwable $exception) {
            fclose($handle);
            $timeElapsed = microtime(true) - $start;
            $this->logger->error('Card import failed', [
                'error' => $exception->getMessage(),
                'durationMs' => (int) round($timeElapsed * 1000),
            ]);
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }
    }

    private function readCSV(mixed $handle): array|false
    {
        $row = fgetcsv($handle);
        if ($row === false) {
            return false;
        }

        if (count($row) !== count($this->csvHeader)) {
            throw new RuntimeException('Invalid CSV row: header and row column counts do not match.');
        }

        return array_combine($this->csvHeader, $row);
    }

    /**
     * @param array<string, int> $artistIdsByExternalId
     * @param array<string, Artist> $pendingArtistsByExternalId
     */
    private function createCard(
        array $row,
        array $artistIdsByExternalId,
        array &$pendingArtistsByExternalId
    ): Card {
        $card = new Card();
        $card->setUuid((string) $row['uuid']);
        $card->setManaValue($this->normalizeNullableInt($row['manaValue'] ?? null));
        $card->setManaCost($this->normalizeNullableString($row['manaCost'] ?? null));
        $card->setName((string) $row['name']);
        $card->setRarity($this->normalizeNullableString($row['rarity'] ?? null));
        $card->setSetCode($this->normalizeNullableString($row['setCode'] ?? null));
        $card->setSubtype($this->normalizeNullableString($row['subtypes'] ?? null));
        $card->setText($this->normalizeNullableString($row['text'] ?? null));
        $card->setType($this->normalizeNullableString($row['type'] ?? null));
        $card->setArtist($this->resolveArtist($row, $artistIdsByExternalId, $pendingArtistsByExternalId));

        return $card;
    }

    /**
     * @param array<string, Artist> $pendingArtistsByExternalId
     * @param array<string, int> $artistIdsByExternalId
     */
    private function flushBatch(array &$pendingArtistsByExternalId, array &$artistIdsByExternalId): void
    {
        if ($this->entityManager->getUnitOfWork()->size() === 0) {
            return;
        }

        $this->entityManager->flush();

        foreach ($pendingArtistsByExternalId as $artistExternalId => $artist) {
            if ($artist->getId() !== null) {
                $artistIdsByExternalId[$artistExternalId] = $artist->getId();
            }
        }

        $pendingArtistsByExternalId = [];
        $this->entityManager->clear();
    }

    /**
     * @param array<string, int> $artistIdsByExternalId
     * @param array<string, Artist> $pendingArtistsByExternalId
     */
    private function resolveArtist(
        array $row,
        array $artistIdsByExternalId,
        array &$pendingArtistsByExternalId
    ): ?Artist {
        $artistName = $this->normalizeNullableString($row['artist'] ?? null);
        if ($artistName === null) {
            return null;
        }

        $artistExternalId = $this->resolveArtistExternalId($row, $artistName);

        if (isset($pendingArtistsByExternalId[$artistExternalId])) {
            return $pendingArtistsByExternalId[$artistExternalId];
        }

        if (isset($artistIdsByExternalId[$artistExternalId])) {
            return $this->entityManager->getReference(Artist::class, $artistIdsByExternalId[$artistExternalId]);
        }

        $artist = new Artist();
        $artist->setName($artistName);
        $artist->setArtistExternalId($artistExternalId);
        $this->entityManager->persist($artist);
        $pendingArtistsByExternalId[$artistExternalId] = $artist;

        return $artist;
    }

    private function resolveArtistExternalId(array $row, string $artistName): string
    {
        $rawArtistId = $this->normalizeNullableString($row['artistIds'] ?? $row['artistId'] ?? null);
        if ($rawArtistId === null) {
            return 'name:' . substr(sha1(mb_strtolower($artistName)), 0, 40);
        }

        $decodedArtistIds = json_decode($rawArtistId, true);
        if (is_array($decodedArtistIds) && $decodedArtistIds !== []) {
            return (string) reset($decodedArtistIds);
        }

        $artistIds = preg_split('/\s*,\s*/', trim($rawArtistId, "[]"));
        if (is_array($artistIds) && $artistIds !== [] && $artistIds[0] !== '') {
            return (string) $artistIds[0];
        }

        return $rawArtistId;
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmedValue = trim($value);

        return $trimmedValue === '' ? null : $trimmedValue;
    }

    private function normalizeNullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function getPositiveIntegerOption(InputInterface $input, string $optionName): ?int
    {
        $value = $input->getOption($optionName);
        if ($value === null || $value === '') {
            return null;
        }

        $normalizedValue = (int) $value;
        if ($normalizedValue <= 0) {
            throw new RuntimeException(sprintf('The option "%s" must be a positive integer.', $optionName));
        }

        return $normalizedValue;
    }
}
