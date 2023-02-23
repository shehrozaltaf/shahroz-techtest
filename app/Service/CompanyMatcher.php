<?php

declare(strict_types=1);

namespace App\Service;

use PDO;

class CompanyMatcher
{
    private PDO $db;
    private $matches = [];

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function match(
        string $postCodeArea,
        string $bedrooms,
        string $type,
        int $limit
    ): void {
        $stmt = $this->db->prepare(<<<EOF
            SELECT c.name, c.id, c.credits, c.website, c.description, c.phone, c.email
            FROM company_matching_settings
            JOIN companies c
            ON company_matching_settings.company_id=c.id
            WHERE type = ? AND bedrooms LIKE ? AND postcodes LIKE ?
            LIMIT {$limit}
        EOF);

        $stmt->execute([$type, "%{$bedrooms}%", "%{$postCodeArea}%"]);

        $this->matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function decrementCredits(array $companies): void
    {
        foreach ($companies as $company) {
            $credits = (int) $company['credits'];
            if (0 === $credits) {
                $logger = new LogService();
                $logger->log(
                    sprintf('The company with #ID "%s" runs out of credits', $company['id'])
                );

                continue;
            }

            $stmt = $this->db->prepare(<<<EOF
                UPDATE
                    companies
                SET
                    companies.credits = :credits
                WHERE
                    companies.id = :id
            EOF);

            $decrementedCredits =  $credits - 1;
            $stmt->bindParam(':credits', $decrementedCredits);
            $stmt->bindParam(':id', $company['id']);

            $stmt->execute();
        }
    }

    public function results(): array
    {
        return $this->matches;
    }
}
