# Contao 4 - Demo Bundle
##### This demo is a basic Contao/Symfony Bundle
###### with examples for<br>
• Backend Module<br>
• Backend Widget<br>
• Data Handling<br>
• Content Element<br>
• Frontend Module<br>
• Frontend Widget<br>
• Model<br>
• Service<br>

## Add bundle as Git-Repository<br>
add code to \<contao root path\>/composer.json
```json
{
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/jodermo/petzka-demo-bundle.git"
        }
    ],
    "require": {
        "petzka/demo-bundle": "dev-master"
    },
    "config": {
        "preferred-install": {
            "petzka/*": "source",
            "*": "dist"
        }
    },
}
```

## Add bundle as local repository<br>
add code to \<contao root path\>/composer.json
```json
{
    "...": "...",
    "repositories": [
        {
            "type": "path",
            "url": "repositories/petzka-demo-bundle"
        }
    ],
    "require": {
        "...": "...",
        "petzka/demo-bundle": "dev-master"
    },
    "config": {
        "preferred-install": {
            "petzka/*": "source",
            "*": "dist"
        }
    },
}
```



# Customize


#### Customize this files:
    .php_cs.php
    composer.json
    phpunit.xml.dist

Then rename all files and/or the references to DemoBundle in `src/` and `tests/`:

<br>

#### How to work with Contao 4 and Troubleshooting <br> [jodermo/contao-4-documentation](https://github.com/jodermo/contao-4-documentation)

<br>

#### For this bundle, I used some informations and stuff from this pages:
• Contao Hello World Bundle Tutorial:<br>
    [gist.github.com/joergmoldenhauer/contao-hello-world-bundle-tutorial.md](https://gist.github.com/joergmoldenhauer/90fa0c9c6af2c7a36bdbc2d039095142)<br>
• Contao 4 skeleton bundle:<br>
    [github.com/contao/skeleton-bundle](https://github.com/contao/skeleton-bundle)


Thanks, guys!


