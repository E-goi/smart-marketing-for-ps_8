# Egoi Email Marketing for Prestashop 8.X.X (V.3.0.5)

Keep your contacts sync with E-goi and increase your sales! Easily design email marketing or SMS campaigns, create the segmentation you want for your contacts or build intuitive automation processes for all channels available on E-goi.

## Getting Started

Clone and code

### Prerequisites

Php enabled server / Mysql enabled server

- Curl lib

### Installing and Deploy

For install, clone the repo, rename folder to smartmarketingps

And compress

```
$ zip -r smart-marketing-ps.8.vx.x.x.zip /home/you/smartmarketingps/
```

Upload to deploy server

### Track&Engage theme hook
If your theme is not calling the hook 'displayTop' the script injection will not work.
For a workaround you can add this hook call to your **header.tpl**
```
{hook h='egoiDisplayTE'}
```

### Changelog
```
-- V.3.0.5
   -- Fix errors on debug mode
   -- Removed listing of contact lists and creation of contact lists from the plugin's account page
   -- On the customer sync page, locked list of listings to the one originally chosen
   -- Bump plugin version to 3.0.5

-- V.3.0.4
   -- Added verification for when there are no customers and no customers who have subscribed to the newsletter
   -- Bump plugin version to 3.0.4
   
-- V.3.0.3
   -- Fix Pagination on sync newsletter subscription
   -- Fix Validation to make it compatible with erp
   -- Bump plugin version to 3.0.3
   
-- V.3.0.2
   -- Fix store id on sync newsletter subscription
   -- Bump plugin version to 3.0.2
   
-- V.3.0.1
   -- Remove unused overrides
   -- Add Prestashop Logger on installation
   -- Tested up to Prestashop 8.1.2
   -- Bump plugin version to 3.0.1
   
-- V.3.0.0
   -- Add support to Prestashop 8.X.X
   -- Remove all requests to E-goi deprecated API V2 
   -- Add suport to E-goi API V3
   -- Removed deprecated menu options
   -- Removed deprecated forms
   -- Removed deprecated remarketing
   -- Add composer support
   -- Add new E-goi API V3
   -- Bumped plugin version until 8.1.2
   -- Performance improvements

-- V.2.0.9
   -- Add sync newsletter subscription to Egoi Lists
   -- Improve syncronization flow
   -- Add tag "NewsletterSubscriptions"
   
-- V.2.0.8
   -- Compatibility with EuPago Multibanco plugin

-- V.2.0.7
   -- Fix new e-goi plugin version error on admin dashboard

-- V.2.0.6
   -- Fix headers warning on product sync process

-- V.2.0.5
   -- Improve syncronization flow

-- V.2.0.4
   -- Fix sync contacts phone

-- V.2.0.3
   -- Fix sync contacts field mapping
   -- Fix php warnings

-- V.2.0.2
   -- Improve exception handling and messages

-- V.2.0.1
   -- Improv on sync products. If Product doesn't have short description, description (limited to 800 characters) will be imported.

-- V.2.0.0
   -- Deprecate old forms. iframe and popup forms are now created an shown by using connected sites.
   -- Contact sync (Newsletter subscriber fix)
   -- Deprecate Remarketing option

-- V.1.6.15
   -- Improve Connected Sites flow
   -- Team Permissions
   -- New version notify

-- V.1.6.14
   -- Connected Sites Integration

-- V.1.6.13
   -- Fixed installation errors
   -- Minor fix on Ifthenpay sms

-- V.1.6.12
   -- EuPago sms fix
   -- Senders minor fix

-- V.1.6.11
   -- Synchronization cron performance improvements

-- V.1.6.10
   -- Select conversion state Track&Engage

-- V.1.6.9
   -- Customer info sync with execution window

-- V.1.6.8
   -- Backend order conversion (fix 1.7.7.x prestashop issue #18789)

-- V.1.6.7
   -- Add cellphone field to subscriber

-- V.1.6.6
   -- Better plugin permission flow 

-- V.1.6.5
   -- Failsafe on TE order conversion (modified thankyou page)

-- V.1.6.4
   -- Fix disable/enable menu bug

-- V.1.6.3
   -- Fix WebserviceRequest compatibility

-- V.1.6.2
   -- IfThenPay SMS upgrade

-- V.1.6.1
   -- Perform TE flow optimizations

-- V.1.6.0
   -- Added push notifications

-- V.1.5.6
   -- Bumped readme file
   -- Bumped plugin version
   -- Temporary fix on price and sale price

-- V.1.5.5
   -- Add readme file
   -- Add Changelog to readme file
   -- Bumped plugin version

```
