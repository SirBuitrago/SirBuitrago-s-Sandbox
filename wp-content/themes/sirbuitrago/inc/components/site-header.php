<header class="site-header">
    <div class="container">

        <nav class="site-header__nav">

            <a href="/" class="logo">
                LOGO
            </a>

            <?php wp_nav_menu([
                'menu' => 'Main Menu',
                'menu_class' => 'nav-items',
                'container' => false
            ]); ?>

        </nav>

        <?php // include('searchform.php') 
        ?>

        <button class="mobile-menu-button" aria-expanded="false" aria-controls="menu"><span></span></button>

    </div>
</header>