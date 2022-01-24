# New theme setup

-   Run `npm install` and `composer install`
-   Create a .env file at the root of the theme (cf. `.env-sample` file)
-   You can create custom field types now, under the `custom-field-types` folder; functionality mirrors blocks (each field type has its own js and scss file)
-   See gulpfile for updated setup/tasks:
    -- Using [rollup](https://rollupjs.org/guide/en/) and babel for module bundling, fully ES6 compatible; you can use npm packages directly in the custom blocks now. If you need a dependency, just `npm install --save-dev` it and import it.


# Recommended plugins:

- Post ordering: wp plugin install simple-custom-post-order
