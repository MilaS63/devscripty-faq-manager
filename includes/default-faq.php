<?php
/**
 * Default DevScripty FAQ content for the one-click importer (FAQ > Import DevScripty FAQ).
 * Each item: category slug, question, answer (HTML).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	array(
		'cat' => 'working',
		'q'   => 'How does working with a husband-and-wife duo differ from hiring a traditional agency?',
		'a'   => '<p>When you partner with DevScripty, you work 100% directly with us — the founders and senior engineers building your site. There are zero account managers, no junior developers learning on your budget, and zero communication gaps. Liudmila leads UI/UX design, Breakdance/Shopify Liquid themes, and PageSpeed performance, while Ivan architects backend microservices, databases, and custom APIs.</p>',
	),
	array(
		'cat' => 'working',
		'q'   => 'What is your Double QA Audit Protocol?',
		'a'   => '<p>Before any website or feature goes live, it undergoes a strict internal double-check. Liudmila audits frontend code quality, clean DOM structure, visual responsiveness, and Core Web Vitals. Ivan audits backend query efficiency, database security, and API integrity. Nothing reaches production without both pairs of senior eyes approving it.</p>',
	),
	array(
		'cat' => 'working',
		'q'   => 'Where are you based and how do you handle communication across time zones?',
		'a'   => '<p>We operate internationally from Dumaguete, Philippines (GMT+8), working seamlessly with clients across North America, Europe, and Asia-Pacific. We structure daily progress via clear asynchronous updates (Loom video walkthroughs, Asana board tracking, dedicated email) alongside scheduled live video discovery calls.</p>',
	),
	array(
		'cat' => 'technical',
		'q'   => 'Why do you build WordPress sites with Breakdance/Bricks instead of heavy pre-made themes?',
		'a'   => '<p>Multi-purpose themes and legacy page builders output bloated DOM structures, inject unnecessary JavaScript files, and severely slow down your site. We engineer custom WordPress builds using Breakdance, Bricks, or custom Gutenberg/ACF Pro components. This approach produces clean, semantic HTML/CSS with minimal plugin dependencies, allowing us to guarantee a 90+ Google PageSpeed score on mobile devices.</p>',
	),
	array(
		'cat' => 'technical',
		'q'   => 'What e-commerce development services do you offer for Shopify?',
		'a'   => '<p>We build custom Shopify storefronts using clean Liquid template code, dynamic AJAX side-carts, Shopify Metafields &amp; Metaobjects architecture, and tailored checkout logic. We avoid installing unneeded third-party apps that slow down mobile loading speed and degrade layout stability (CLS).</p>',
	),
	array(
		'cat' => 'technical',
		'q'   => 'Can you help optimize an existing slow website?',
		'a'   => '<p>Yes. We offer dedicated Speed &amp; Performance Overhauls. We audit dynamic code bloat, defer non-critical scripts, configure dynamic WebP/AVIF image delivery pipelines, tune database queries, and set up Cloudflare Edge Caching to guarantee a 90+ PageSpeed score on mobile and desktop.</p>',
	),
	array(
		'cat' => 'security',
		'dark' => true,
		'icon' => 'lock',
		'q'   => 'How do you handle security, credentials, and AI tools?',
		'a'   => '<p>Good development is about protecting your business. We apply a strict, multi-layered security protocol:</p><ul><li><strong>Zero-Trust Credentials:</strong> We use Bitwarden/Enpass with least-privilege access and time limits. Secrets and API keys are never hardcoded or stored in code repositories.</li><li><strong>Manual AI Code Audits:</strong> We use AI for prototyping, but every generated script, dependency, and package is manually audited and tested to prevent supply-chain attacks and backdoors.</li><li><strong>Isolation &amp; Recovery:</strong> Backend C#/.NET APIs and databases are isolated in Docker containers with SSL encryption. Full system backups are established before every major deployment.</li></ul>',
	),
	array(
		'cat' => 'pricing',
		'q'   => 'How much does a custom website or Shopify build cost?',
		'a'   => '<p>Projects typically range from $2,500 to $6,000+ based on platform requirements, custom UI/UX design scope, backend API complexity, and timeline. You can use our Interactive Cost Estimator on our home page to generate an instant budget range for your project.</p>',
	),
	array(
		'cat' => 'pricing',
		'q'   => 'Do you charge hourly or a fixed project fee?',
		'a'   => '<p>We operate on 100% fixed-budget contracts established after our initial discovery phase. Once the scope is finalized, your quote will not change unless you explicitly request additional features during development. No surprise hourly overages.</p>',
	),
	array(
		'cat' => 'pricing',
		'q'   => 'What are your standard project delivery timelines?',
		'a'   => '<ul><li><strong>High-Speed Landing Page:</strong> 1.5 – 2.5 weeks</li><li><strong>Custom WordPress / Shopify Storefront:</strong> 4 – 6 weeks</li><li><strong>Complex Custom Microservices &amp; API Engineering:</strong> 6 – 8+ weeks</li></ul><p>Priority Rush execution is available upon request.</p>',
	),
	array(
		'cat' => 'guarantees',
		'q'   => 'What post-launch warranties and support do you include?',
		'a'   => '<p>Every project includes 30 days of complimentary post-launch technical support and warranty. We fix any bugs free of charge, provide custom Loom video administration walkthroughs showing how to manage your content, and assist with initial Google Search Console indexing.</p>',
	),
	array(
		'cat' => 'guarantees',
		'q'   => 'Will I be able to update text and products on my website myself?',
		'a'   => '<p>Absolutely. We build intuitive site backends with custom fields (ACF Pro) or visual block layouts so you or your team can update text, add blog posts, and edit products easily without technical skills or fear of breaking the design.</p>',
	),
);
