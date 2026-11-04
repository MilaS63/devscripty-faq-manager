# DevScripty FAQ Manager

A lightweight WordPress plugin for managing questions and answers in the admin and showing them anywhere with a shortcode. Built for Breakdance sites, works with any theme or builder.

## Features

- Questions and answers as a custom post type (classic editor, no REST dependency)
- Category tabs built from FAQ categories, animated accessible accordion (`aria-expanded`)
- Optional client-side search, no AJAX and no REST calls on the front end
- Per-question "Card style": dark card and optional icon
- FAQPage JSON-LD for SEO (printed once per page, can be switched off)
- One-click importer with ready-made content
- Styling through CSS variables (`--ds-faq-*`), no fonts loaded by the plugin

## Installation

1. Download the plugin as a `.zip` (see Releases) and upload it under **Plugins → Add New → Upload Plugin**.
2. Activate it. Five default categories are created automatically.
3. Add questions under **FAQ → Add new question**, or run **FAQ → Import DevScripty FAQ**.
4. Place the shortcode on a page, for example in a Breakdance Shortcode element:

```
[ds_faq open="first"]
```

## Shortcode

| Attribute  | Values                         | Default | Description |
|------------|--------------------------------|---------|-------------|
| `category` | comma-separated slugs          | all     | Limit to categories (tabs hide automatically) |
| `ids`      | comma-separated question IDs   | all     | Show specific questions |
| `limit`    | number                         | all     | Maximum number of questions |
| `open`     | `no`, `first`, `all`           | `no`    | Which answers start open |
| `single`   | `yes`, `no`                    | `yes`   | Opening one answer closes the others |
| `tabs`     | `auto`, `yes`, `no`            | `auto`  | Category tabs |
| `search`   | `yes`, `no`                    | `no`    | Search box |
| `theme`    | `light`, `dark`                | `light` | Dark style for every card |
| `schema`   | `yes`, `no`                    | `yes`   | FAQPage structured data |

## Styling

Override the CSS variables on `.ds-faq`, for example in Breakdance custom CSS:

```css
.ds-faq { --ds-faq-accent: #9E886D; --ds-faq-radius: 24px; }
```

## Requirements

WordPress 5.8+, PHP 7.4+.

## License

GPL-2.0-or-later.
