<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Support\AdvertFormValidator;
use PHPUnit\Framework\TestCase;

final class AdvertFormValidatorTest extends TestCase
{
    private AdvertFormValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new AdvertFormValidator();
    }

    public function testItRejectsMissingRequiredFieldsOnCreation(): void
    {
        $errors = $this->validator->validateCreation([]);

        self::assertContains('Veuillez entrer votre nom', $errors);
        self::assertContains('Veuillez entrer une adresse mail correcte', $errors);
        self::assertContains('Les mots de passes ne sont pas identiques', $errors);
    }

    public function testItRejectsInvalidEmailAndPrice(): void
    {
        $errors = $this->validator->validateCreation($this->validPayload([
            'email' => 'invalid',
            'price' => 'abc',
        ]));

        self::assertContains('Veuillez entrer une adresse mail correcte', $errors);
        self::assertContains('Veuillez entrer un prix', $errors);
    }

    public function testItAcceptsAValidCreationPayload(): void
    {
        $errors = $this->validator->validateCreation($this->validPayload());

        self::assertSame([], $errors);
    }

    public function testEditionDoesNotRequirePasswordConfirmation(): void
    {
        $payload = $this->validPayload();
        unset($payload['psw'], $payload['confirm-psw']);

        $errors = $this->validator->validateEdition($payload);

        self::assertSame([], $errors);
    }

    /**
     * @param array<string, string> $overrides
     * @return array<string, string>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'nom' => 'Alice',
            'email' => 'alice@example.com',
            'phone' => '0102030405',
            'ville' => 'Paris',
            'departement' => '75',
            'categorie' => '1',
            'title' => 'Titre',
            'description' => 'Description',
            'price' => '150',
            'psw' => 'secret',
            'confirm-psw' => 'secret',
        ], $overrides);
    }
}
