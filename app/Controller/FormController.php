<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CompanyMatcher;
use App\Service\PostCodeParser;
use UnexpectedValueException;

class FormController extends Controller
{
    public function home(): void
    {
        $this->render('home.twig');
    }

    public function index(): void
    {
        $this->render('form.twig');
    }

    public function submit(): void
    {
        if ('POST' !== $_SERVER['REQUEST_METHOD']) {
            throw new UnexpectedValueException('Only POST request allowed');
        }

        $postCode = $this->validateField('postcode');
        $bedroomsNumber = $this->validateField('bedrooms');
        $type = $this->validateField('type');

        $postCodeArea = (new PostCodeParser($postCode))->getArea();

        $matcher = new CompanyMatcher($this->db());
        $limit = (int) $_SERVER['MAX_MATCHED_COMPANIES'] ?? 3;
        $matcher->match($postCodeArea, $bedroomsNumber, $type, $limit);
        $matchedCompanies = $matcher->results();

        $matcher->decrementCredits($matchedCompanies);

        $this->render('results.twig', [
            'matchedCompanies'  => $matchedCompanies,
        ]);
    }

    private function validateField(string $name): string
    {
        $value = $_POST[$name] ?? null;

        if (null === $value ||empty($value)) {
            throw new UnexpectedValueException(
                sprintf('Invalid "%s", expected not empty value, got "%s".', $name, $value)
            );
        }

        return $value;
    }
}
