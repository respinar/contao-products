<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2026 <hamid@respinar.com>
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\Product;

use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\FrontendTemplate;
use Contao\ModuleModel;
use Contao\StringUtil;
use Contao\System;
use Respinar\ProductsBundle\Model\ProductModel;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ProductParser
{

    public function __construct(
        private readonly Studio $studio,
        private readonly ContentUrlGenerator $contentUrlGenerator,
        private readonly SchemaGenerator $schema_generator,
        private readonly MetaGenerator $meta_generator,
    ) {
    }

    /**
     * Parse a product.
     */
    public function parseProduct(ProductModel $product, ModuleModel|ContentModel $model, bool $addCategory = false): string {
        $template = new FrontendTemplate(
            $model->product_template ?: 'product_short'
        );

        $template->setData($product->row());

        $template->hasSummary = false;
        $template->hasText = false;
        $template->hasEnclosure = false;

        $class = '';

        if ($model->product_singleClass) {
            $class .= ' '.$model->product_singleClass;
        }

        if (time() - $product->date < 2592000) {
            $template->new_product = true;
            $class .= ' new';
        }

        if ($product->featured) {
            $class .= ' featured';
        }

        if ($product->cssClass) {
            $class .= ' '.$product->cssClass;
        }

        $template->class = trim($class);

        $template->category = $product->getRelated('pid');

        $template->meta = $this->meta_generator->generate($product);

        if (null !== $product->summary) {
            $template->hasSummary = true;
            $template->summary = StringUtil::encodeEmail($product->summary);
        }

        $elements = ContentModel::findPublishedByPidAndTable(
            $product->id,
            'tl_product'
        );

        if (null !== $elements) {
            $template->hasText = true;

            while ($elements->next()) {
                $template->text .= Controller::getContentElement(
                    $elements->current()
                );
            }

            // $template->link = UrlGenerator::generate(
            //     $product,
            //     $addCategory
            // );

            $template->link = $this->contentUrlGenerator->generate($product,[],UrlGeneratorInterface::ABSOLUTE_PATH);
        }

        $template->addImage = false;

        if ($product->singleSRC) {
            $size = null;

            if ($model->imgSize) {
                $imgSize = StringUtil::deserialize($model->imgSize);

                if (
                    $imgSize[0] > 0
                    || $imgSize[1] > 0
                    || is_numeric($imgSize[2])
                    || ($imgSize[2][0] ?? null) === '_'
                ) {
                    $size = $model->imgSize;
                }
            }            

            $figure = $this->studio
                ->createFigureBuilder()
                ->setSize($size)
                ->from($product->singleSRC)
                ->build();
            
            $template->figure = $figure;
            
        }

        $template->enclosure = [];

        if ($product->addEnclosure) {
            $template->hasEnclosure = true;

            Controller::addEnclosuresToTemplate(
                $template,
                $product->row()
            );
        }

        $template->featured_text = 'Featured';
        $template->new_text = 'New';

        $template->schemaOrgData = $this->schema_generator->generate($product);

        return $template->parse();
    }

    /**
     * Parse multiple products.
     */
    public function parseProducts(
        object $products,
        object $model,
        bool $addCategory = false,
    ): array {       

        $items = [];

        while ($products->next()) {
            $items[] = self::parseProduct($products->current(), $model, $addCategory);
        }

        return $items;
    }
}