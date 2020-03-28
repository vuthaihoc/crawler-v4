# Crawler v4



Crawl link and data by rules :

```php
// Define pages
$root = new Page( 'root', 'https://itviec.com/it-jobs/php', true );
$detail = new Page( 'detail', 'https://itviec.com/it-jobs/php-developers-mysql-net-trung-tam-bao-hanh-dell-tai-viet-nam-digipro-1424' );
$company = new Page( "company", 'https://itviec.com/companies/relia-systems?utm_campaign=gsn_brand&utm_medium=key_cpc&utm_source=google%2Fha-noi', false );

// Define get data rules
$company->addDataRule( new DataRule( "company_name", new Selector( "css", "div.name-and-info h1")));
$company->addDataRule( new DataRule( "company_location", new Selector( "css", "div.name-and-info span")));
$company->addDataRule( new DataRule( "company_logo", new Selector( "css", "div.headers .logo img"), DataRule::TYPE_ATTRIBUTE, 'data-src'));

// Define Navigation rules
$root->goTo(
    $detail,
    Selector::css("#jobs h2", true )
);
$root->goTo(
    $root,
    Selector::css( "#show_more .more-jobs-link", 'link' )
);
$detail->goTo( $company, Selector::xpath( "//div[@class='side_bar']//a[text()='View our company page']"));


// Add to site
$this->pages[ $root->name ] = $root;
$this->pages[ $detail->name ] = $detail;
$this->pages[ $company->name ] = $company;
```