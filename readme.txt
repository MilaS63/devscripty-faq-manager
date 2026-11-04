=== DevScripty FAQ Manager ===
Version: 1.3.1 | License: GPL-2.0-or-later

Admin: FAQ > Add question (title = question, editor = answer, "Order" = manual sort, categories optional).
On activation five categories are created if none exist: Working With Us, Technical & Speed, Security & AI Code, Pricing & Timelines, Guarantees.
Tabs follow the order in which categories were created. Rename or add categories under FAQ > FAQ categories.

Import: FAQ > Import DevScripty FAQ creates the 5 categories and 12 ready-made questions (safe to run twice: existing titles are skipped).

Shortcode:
[ds_faq]
[ds_faq open="first" search="yes"]
[ds_faq category="pricing" limit="5"]
[ds_faq ids="12,15,18" schema="no"]

Attributes:
category  comma-separated category slugs (limits the list; tabs are hidden automatically)
ids       comma-separated question IDs
limit     max number of questions (default: all)
open      no | first | all
single    yes | no (yes = opening one answer closes the others; default yes)
tabs      auto | yes | no (auto = show category tabs when the list is not already filtered by category)
search    yes | no (client-side filter, no AJAX)
theme     light | dark (dark = every card dark; for a single dark card use the Card style box on the question)
schema    yes | no (FAQPage JSON-LD, printed once per page)

Rank Math / Yoast: they do not read shortcode content, so leave schema="yes". Add schema="no" only if you also create FAQ schema for the same page inside Rank Math (FAQ block or Schema tab), to avoid duplicates.

Fonts: not loaded by the plugin. Set Plus Jakarta Sans and Playfair Display in Breakdance global fonts (or self-host them).
Styling: override the --ds-faq-* CSS variables on .ds-faq.

Troubleshooting: if the block is empty, log in and view the page: editors see a notice when no published questions match. In Breakdance use the Shortcode element (a Code Block does not run shortcodes).

Card style: on each question's edit screen (right column) tick Dark card and pick an optional icon (lock, shield, lightning, clock, check).
