# Contao Products Bundle

A simple and flexible products extension for [Contao CMS](https://contao.org) 5.

## Features

- **Catalogs** – Organize products into catalogs (categories) with protected access support
- **Products** – Manage products with title, alias, date, price, availability, brand, model, SKU, purchase URL, images, and more
- **Frontend modules** – Product List, Product Detail, and Related Products
- **Content elements** – Product List, Product Catalog, and Single Product
- **Insert tag** – `{{product_url::…}}` to link a product from any rich text field or template
- **Twig templates** – Fully customizable templates for lists and detail views
- **Comments support** – Optionally integrate with `contao/comments-bundle`
- **Multilingual support** – Optionally integrate with `terminal42/contao-changelanguage`
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

## Product aliases

A product URL consists of the redirect page (reader page) of its catalog and the
product alias. Therefore, an alias only has to be unique **per website** – among all
catalogs whose redirect page belongs to the same root page. The same alias may be
used by the translations of a product on other websites (e.g. with ChangeLanguage).

- The back end validates the alias when saving a product and prevents changing a
  catalog's redirect page if it would create duplicate aliases on the target website.
- If existing data contains duplicate aliases on the same website, `contao:migrate`
  reports them. The affected products have to be renamed manually, because changing
  aliases automatically would break existing URLs.

## Insert tag

| Insert tag | Description |
|------------|-------------|
| `{{product_url::<id or alias>}}` | URL of a product, e.g. `{{product_url::12}}` or `{{product_url::my-product}}` |

Product IDs are globally unique and can be resolved directly. Product aliases are
unique per website, so they are resolved against the website (root page) of the
current page. If the alias is unambiguous, it is resolved regardless of the website.

## Optional integrations

Both integrations are optional and detected automatically – the related settings only appear in the back end when the bundle is installed:

- **`terminal42/contao-changelanguage`** – Adds *Master* and *Language* settings to each catalog so products can be maintained per language, and translates product URLs when switching languages. Install it with:

    ```bash
    composer require terminal42/contao-changelanguage
    ```

- **`contao/comments-bundle`** – Adds a *Comments* section to each catalog.

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
