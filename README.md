# KibokoArmageddonBundle
## When Switching branches, better call Bruce :)

Important notice !
====================

This Bundle is provide as it, whithout any warranty of any kind, unless the warranty of doing what it is aiming to do.
This Bundle is not for production use, If it's called *ArmageddonBundle* , there is a reason.

What does this Bundle do?
====================

This Bundle erase `app/cache/*` folder, `vendor` folder, then runs 
`composer install --no-dev --optimize-autoloader`, 
`app/console cache:warm`, 
`app/console oro:asset:install`,
`app/console assetic:dump`,
`app/console oro:translation:dump`,
`app/console oro:localization:dump` , 
`app/console fos:js-routing:dump` , 

for the environment you have choosen.

Can i run it Twice?
====================
Huu?? can Armageddon be done twice??

In fact if you look at the code, when running `composer install`, it runs `compsoer install --no-dev --optimize-autoloader`
As we've added `kiboko/armageddon` to require-dev, it is not available after running `kiboko:amrageddon`.

TODO : 
- Add more exciting options like `--liv`
- Make `kiboko/armageddon` still available after `kiboko/armageddon`
- and more options :)

How to run Armageddon?
====================

Simply call `app/console kiboko:armageddon --bruce --env={env}`, with `env` equals to `dev` or `prod`.

If the Option `--bruce` isn't specified, the Armageddon can't be run....(Do you ever seen Armageddon without Bruce?? me neither).

How to install it?
====================

Add repository to your `composer.json` and require the bundle in `require-dev` (remember, not for production use !).
```bash
nano composer.json

[... repositories section]
"kiboko-armageddon" : {   
  "type": "vcs",
  "url": "https://github.com/kiboko-labs/KibokoArmageddonBundle"
}
[...]
```

then 
```bash
composer require --dev kiboko/armageddon:"1.0.0"
```

then manually clear your cache 
`rm -rf app/cache/{prod,dev}`
and warm it up 
`app/console ca:wa`

Kiboko, who we are ?
====================

Kiboko is a consulting and development agency for e-commerce and business solutions,
created by the reunion of 3 e-commerce seasoned developers, working on various scale
of e-commerce websites. Depending on your business needs, Kiboko develops and maintains
e-commerce web stores using Magento and OroCommerce. Kiboko also integrates Akeneo (PIM),
OroCRM (CRM) and Marello (ERP) into pre-existing environement or into a new one to build as
your business needs. Kiboko has been one of the first companies to trust OroCommerce as a
true B2B e-commerce solution and one of the first to release live a web store using it.


Contributing
====================

Please feel free to create [new issues on Github](https://github.com/kiboko-labs/KibokoArmageddonBundle/issues)