<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\Picker;

use Contao\CoreBundle\DependencyInjection\Attribute\AsPickerProvider;
use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\CoreBundle\Picker\AbstractInsertTagPickerProvider;
use Contao\CoreBundle\Picker\DcaPickerProviderInterface;
use Contao\CoreBundle\Picker\PickerConfig;
use Knp\Menu\FactoryInterface;
use Respinar\ProductsBundle\Model\CatalogModel;
use Respinar\ProductsBundle\Model\ProductModel;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsPickerProvider(priority: 128)]
class ProductPickerProvider extends AbstractInsertTagPickerProvider implements DcaPickerProviderInterface, FrameworkAwareInterface
{
    use FrameworkAwareTrait;

    /**
     * @internal
     */
    public function __construct(
        FactoryInterface $menuFactory,
        RouterInterface $router,
        TranslatorInterface|null $translator,
        private readonly Security $security,
    ) {
        parent::__construct($menuFactory, $router, $translator);
    }

    public function getName(): string
    {
        return 'productPicker';
    }

    public function supportsContext(string $context): bool
    {
        return 'link' === $context && $this->security->isGranted('contao_user.modules', 'products');
    }

    public function supportsValue(PickerConfig $config): bool
    {
        return $this->isMatchingInsertTag($config);
    }

    public function getDcaTable(PickerConfig|null $config = null): string
    {
        return 'tl_product';
    }

    public function getDcaAttributes(PickerConfig $config): array
    {
        $attributes = ['fieldType' => 'radio'];

        if ($this->supportsValue($config)) {
            $attributes['value'] = $this->getInsertTagValue($config);

            if ($flags = $this->getInsertTagFlags($config)) {
                $attributes['flags'] = $flags;
            }
        }

        return $attributes;
    }

    public function convertDcaValue(PickerConfig $config, mixed $value): string
    {
        return \sprintf($this->getInsertTag($config), $value);
    }

    protected function getRouteParameters(PickerConfig|null $config = null): array
    {
        $params = ['do' => 'products'];

        if (!$config?->getValue() || !$this->supportsValue($config)) {
            return $params;
        }

        if (null !== ($catalogId = $this->getCatalogId($this->getInsertTagValue($config)))) {
            $params['table'] = 'tl_product';
            $params['id'] = $catalogId;
        }

        return $params;
    }

    protected function getDefaultInsertTag(): string
    {
        return '{{product_url::%s}}';
    }

    private function getCatalogId(int|string $id): int|null
    {
        $productAdapter = $this->framework->getAdapter(ProductModel::class);

        if (!$productModel = $productAdapter->findById($id)) {
            return null;
        }

        if (!$catalog = $this->framework->getAdapter(CatalogModel::class)->findById($productModel->pid)) {
            return null;
        }

        return (int) $catalog->id;
    }
}
