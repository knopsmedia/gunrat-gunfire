<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Model;

final class Product
{
    private int $externalId = 0;
    private string $sku = '';
    private string $name = '';
    private string $description = '';
    private string $externalUrl = '';

    /** @var string[] */
    private array $tags = [];

    private ?Manufacturer $manufacturer = null;
    private ?Category $category = null;

    /** @var Image[] */
    private array $images = [];

    /** @var Attribute[] */
    private array $attributes = [];

    public function getExternalId(): int
    {
        return $this->externalId;
    }

    public function setExternalId(int $externalId): void
    {
        $this->externalId = $externalId;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): void
    {
        $this->sku = $sku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getExternalUrl(): string
    {
        return $this->externalUrl;
    }

    public function setExternalUrl(string $externalUrl): void
    {
        $this->externalUrl = $externalUrl;
    }

    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }

    public function setManufacturer(Manufacturer $manufacturer): void
    {
        $this->manufacturer = $manufacturer;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
        $this->setTags(explode('/', $category->getName()));
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    public function getWeightInKg(): float
    {
        $attribute = $this->findAttributeByName('Weight [g]');
        $weight = $attribute ? (float)$attribute->getValue() : .0;

        return $weight / 1000;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function findAttributeByName(string $name): ?Attribute
    {
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->getName() === $name) {
                return $attribute;
            }
        }

        return null;
    }

    public function addAttributes(array $attributes): self
    {
        foreach ($attributes as $attribute) {
            $this->addAttribute($attribute);
        }

        return $this;
    }

    public function addAttribute(Attribute $attribute): Attribute
    {
        $this->attributes[] = $attribute;

        return $attribute;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function addImages(array $images): self
    {
        foreach ($images as $image) {
            $this->addImage($image);
        }

        return $this;
    }

    public function addImage(Image $image): Image
    {
        $this->images[] = $image;

        return $image;
    }
}