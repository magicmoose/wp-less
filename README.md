# Enables LESS in WordPress

LESS is an abstraction layer that adds some very powerful features to CSS. It
will speed up your development process and make your life that much easier. Find
out more from the links below and then head on back.

The addon I've written allows you to write and edit .less files directly and
have WordPress do the job of compiling and caching the resulting CSS. It
eliminates the extra step of having to compile the .less files into CSS yourself
before deploying them.

## Installation:

If you are using git to clone the repository do the following:

    git clone git://github.com/sanchothefat/wp-less.git wp-less
    git submodule update --init

If you are downloading the zip or tar don't forget to download the lessphp
dependency too https://github.com/leafo/lessphp and copy it into the `lessc`
folder.

## Usage:

For use with themes add the following lines to your functions.php:

```php
<?php

// Include the class
require_once( 'wp-less/wp-less.php' );

// enqueue a .less style sheet
if ( ! is_admin() )
    wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/style.less' );

// you can also use .less files as mce editor style sheets
add_editor_style( 'editor-style.less' );

?>
```

Any registered styles with the .less suffix will be compiled and the file URL
rewritten.

You won't need a link to your main style sheet in header.php, just make sure
that `wp_head()` is called in the document head.

All the standard LESS features are supported as well as @import rules anywhere
within the file.

### Passing in variables from PHP

You can now pass variables into your .less files using one of two hooks:

```php
<?php

// pass variables into all .less files
add_filter( 'less_vars', 'my_less_vars', 10, 2 );
function my_less_vars( $vars, $handle ) {
    // $handle is a reference to the handle used with wp_enqueue_style()
    $vars[ 'color' ] => '#000000';
    return $vars;
}

// pass variables into a specific .less file
// in this case 'style' is the handle we enqueued the .less file with
add_filter( 'less_vars_style', 'style_less_vars' );
function style_less_vars( $vars ) {
    $vars[ 'color' ] => '#ffffff';
    return $vars;
}

?>
```

Within your .less files you can use the variable as if you had declared it in
the stylesheet eg:

```css
body { color: @color; }
```

*There is a default variable* you can use without worrying about the above code
called `@themeurl`:

```css
body { background-image: url(@{themeurl}/images/background.png); }
```

This is important because you can't use relative paths - the compiled CSS is
stored in the uploads folder as it is the only place you can guarantee being
able to write to in any WordPress installation. As a result relative URLs will
break.

## Further Reading

Read the LESS.js documentation here: http://lesscss.org/
For documenation specific to the PHP parser: http://leafo.net/lessphp/docs/
