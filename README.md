# Contao Products Bundle

A simple and flexible products extension for [Contao CMS](https://contao.org) 5.

## Features

- **Catalogs** – Organize products into catalogs (categories) with protected access support
- **Products** – Manage products with title, alias, date, price, availability, brand, model, SKU, purchase URL, images, and more
- **Frontend modules** – Product List, Product Detail, and Related Products
- **Content elements** – Product List, Product Catalog, and Single Product
- **Twig templates** – Fully customizable templates for lists and detail views
- **Comments support** – Optionally integrate with `contao/comments-bundle`
- **Access control** – Catalog-level protection with member group restrictions

## Requirements

- PHP `^8.3`
- Contao `^5.7`

## Installation

```bash
composer require respinar/contao-products
```

After installation, run a database update and rebuild the Symfony cache:

```bash
vendor/bin/contao-console contao:migrate
vendor/bin/contao-console cache:clear
```

## Usage

1. **Create a catalog** in the Contao back end under *Products*.
2. **Add products** to the catalog.
3. **Create a frontend module** (e.g., *Product List*) and assign it to a page layout, or insert a *Product List* content element into an article.
4. **Customize templates** by copying Twig templates from `contao/templates/` to your theme.

## Templates

| Template | Description |
|----------|-------------|
| `frontend_module/product_list.html.twig` | List view with pagination |
| `frontend_module/product_detail.html.twig` | Single product detail view |
| `frontend_module/product_related.html.twig` | Related products list |
| `product_full.html.twig` | Full product output |
| `product_short.html.twig` | Short product output |
| `product_simple.html.twig` | Simple product output |

## License

This bundle is released under the [MIT license](LICENSE).
