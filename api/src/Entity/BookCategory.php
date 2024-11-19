<?php

declare (strict_types = 1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\UrlGeneratorInterface;
use App\Repository\BookCategoryRepository;
use App\State\Processor\BookCategoryPersistProcessor;
use App\State\Processor\BookCategoryRemoveProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A book categories.
 *
 * @see https://schema.org/Book
 */
#[ApiResource(
    uriTemplate: '/admin/book-categories{._format}',
    types: ['https://schema.org/category'],
    operations: [
        new GetCollection(
            itemUriTemplate: '/admin/book-categories/{id}{._format}',
            paginationClientItemsPerPage: true
        ),
        new Post(
            processor: BookCategoryPersistProcessor::class,
            itemUriTemplate: '/admin/book-categories'
        ),
        new Get(
            uriTemplate: '/admin/book-categories/{id}{._format}'
        ),
        new Put(
            uriTemplate: '/admin/book-categories/{id}{._format}',
            processor: BookCategoryPersistProcessor::class
        ),
        new Delete(
            uriTemplate: '/admin/book-categories/{id}{._format}',
            processor: BookCategoryRemoveProcessor::class
        ),
    ],
    normalizationContext: [
        AbstractNormalizer::GROUPS => ['BookCategory:read:admin', 'Enum:read'],
        AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
    ],
    denormalizationContext: [
        AbstractNormalizer::GROUPS => ['BookCategory:write'],
    ],
    collectDenormalizationErrors: true,
    security: 'is_granted("OIDC_ADMIN")',
    mercure: [
        'topics' => [
            '@=iri(object, ' . UrlGeneratorInterface::ABS_URL . ', get_operation(object, "/admin/book-categories/{id}{._format}"))',
            '@=iri(object, ' . UrlGeneratorInterface::ABS_URL . ', get_operation(object, "/book-categories/{id}{._format}"))',
        ],
    ]
)]
#[ApiResource(
    types: ['https://schema.org/Book', 'https://schema.org/Offer'],
    operations: [
        new GetCollection(
            itemUriTemplate: '/book-categories/{id}{._format}'
        ),
        new Get(),
    ],
    normalizationContext: [
        AbstractNormalizer::GROUPS => ['BookCategory:read', 'Enum:read'],
        AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
    ],
    mercure: [
        'topics' => [
            '@=iri(object, ' . UrlGeneratorInterface::ABS_URL . ', get_operation(object, "/admin/book-categories/{id}{._format}"))',
            '@=iri(object, ' . UrlGeneratorInterface::ABS_URL . ', get_operation(object, "/book-categories/{id}{._format}"))',
        ],
    ]
)]
#[ORM\Entity(repositoryClass: BookCategoryRepository::class)]
class BookCategory
{
    #[ApiProperty(identifier: true, types: ['https://schema.org/identifier'])]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    private ?Uuid $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['category:read', 'category:write', 'book:read'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Book>
     */
    #[Groups(['category:read'])]
    #[ORM\ManyToMany(targetEntity: Book::class, inversedBy: 'category')]
    private Collection $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    #[Groups(groups: ['BookCategory:read', 'BookCategory:read:admin', 'BookCategory:admin:write'])]
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        $this->books->removeElement($book);

        return $this;
    }
}
