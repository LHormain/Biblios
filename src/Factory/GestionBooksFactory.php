<?php

namespace App\Factory;

use App\Entity\GestionBooks;
use App\Repository\GestionBooksRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<GestionBooks>
 *
 * @method        GestionBooks|Proxy                     create(array|callable $attributes = [])
 * @method static GestionBooks|Proxy                     createOne(array $attributes = [])
 * @method static GestionBooks|Proxy                     find(object|array|mixed $criteria)
 * @method static GestionBooks|Proxy                     findOrCreate(array $attributes)
 * @method static GestionBooks|Proxy                     first(string $sortedField = 'id')
 * @method static GestionBooks|Proxy                     last(string $sortedField = 'id')
 * @method static GestionBooks|Proxy                     random(array $attributes = [])
 * @method static GestionBooks|Proxy                     randomOrCreate(array $attributes = [])
 * @method static GestionBooksRepository|RepositoryProxy repository()
 * @method static GestionBooks[]|Proxy[]                 all()
 * @method static GestionBooks[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static GestionBooks[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static GestionBooks[]|Proxy[]                 findBy(array $attributes)
 * @method static GestionBooks[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static GestionBooks[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class GestionBooksFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'DateSortie' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'book' => BookFactory::random(['status' => 'borrowed']),             // filtre avec l'attribut status = borrowed
            'user' => UserFactory::random(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(GestionBooks $gestionBooks): void {})
        ;
    }

    protected static function getClass(): string
    {
        return GestionBooks::class;
    }
}
