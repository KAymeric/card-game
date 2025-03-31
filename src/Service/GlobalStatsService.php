<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\GlobalStats;

class GlobalStatsService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Increment the counter for a given route.
     */
    public function incrementRouteCount(string $route): void
    {
        $key = "route." . $route;
        // Recherche d'une statistique existante par le champ "key"
        $stat = $this->entityManager->getRepository(GlobalStats::class)
            ->findOneBy(['key' => $key]);

        if (!$stat) {
            $stat = new GlobalStats();
            // On stocke toujours la clé avec le préfixe "route."
            $stat->setKey($key);
            // Initialisation de la valeur à "0" (sous forme de string)
            $stat->setValue("0");
            $this->entityManager->persist($stat);
        }
        $stat->incrementValue();
        $this->entityManager->flush();
    }

    public function incrementUserAgent(string $userAgent): void
    {
        $key = "user_agent." . $userAgent;
        // Recherche d'une statistique existante par le champ "key"
        $stat = $this->entityManager->getRepository(GlobalStats::class)
            ->findOneBy(['key' => $key]);

        if (!$stat) {
            $stat = new GlobalStats();
            // On stocke toujours la clé avec le préfixe "user_agent."
            $stat->setKey($key);
            $stat->setValue("0");
            $this->entityManager->persist($stat);
        }
        $stat->incrementValue();
        $this->entityManager->flush();
    }

    public function incrementTotalRequest(): void
    {
        $key = "total_request";
        // Recherche d'une statistique existante par le champ "key"
        $stat = $this->entityManager->getRepository(GlobalStats::class)
            ->findOneBy(['key' => $key]);

        if (!$stat) {
            $stat = new GlobalStats();
            $stat->setKey($key);
            $stat->setValue("0");
            $this->entityManager->persist($stat);
        }
        $stat->incrementValue();
        $this->entityManager->flush();
    }

    /**
     * Returns counts for specific entities.
     * Adapt the list of entities as needed.
     */
    public function getEntityCounts(): array
    {
        $entities = [
            'cards' => \App\Entity\Card::class,
            'custom_medias' => \App\Entity\CustomMedia::class,
            'sets' => \App\Entity\Set::class,
            'stats' => \App\Entity\Stat::class,
            'types' => \App\Entity\Type::class,
            'users' => \App\Entity\User::class,
        ];

        $data = [];
        foreach ($entities as $alias => $className) {
            $count = $this->entityManager->getRepository($className)->count([]);
            $data[$alias] = $count;
        }
        return $data;
    }

    /**
     * Returns all global statistics grouped by prefix.
     * For each statistic, the key is split into:
     * - The prefix (the part before the dot, or the entire key if no dot exists)
     * - The remaining part.
     * The values are converted to integers and summed per group.
     */
    /**
     * Returns all global statistics grouped by prefix.
     * For each statistic, the key is split into:
     * - The prefix (the part before the dot, or the entire key if no dot exists)
     * - The remaining part.
     * The values are converted to integers and summed per group.
     * Additionally, the entity counts are added under the key "entity".
     */
    public function getStats(): array
    {
        $stats = $this->entityManager->getRepository(GlobalStats::class)->findAll();
        $grouped = [];
        foreach ($stats as $stat) {
            $key = $stat->getKey();
            $value = (int) $stat->getValue();
            if (strpos($key, '.') !== false) {
                [$prefix, $suffix] = explode('.', $key, 2);
            } else {
                $prefix = $key;
                $suffix = null;
            }
            if (!isset($grouped[$prefix])) {
                $grouped[$prefix] = [];
            }
            if ($suffix !== null) {
                if (isset($grouped[$prefix][$suffix])) {
                    $grouped[$prefix][$suffix] += $value;
                } else {
                    $grouped[$prefix][$suffix] = $value;
                }
            } else {
                if (isset($grouped[$prefix]['value'])) {
                    $grouped[$prefix]['value'] += $value;
                } else {
                    $grouped[$prefix]['value'] = $value;
                }
            }
        }

        $grouped['entity'] = $this->getEntityCounts();

        return $grouped;
    }
}
