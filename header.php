<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kris & Li Cleaning Services</title>
    <!--  google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <?php wp_head(); ?>
</head>
<body>

<!-- desktop nav -->
<header class="main-header">
        <a href="#">
            <?php $logoimg=get_header_image();  ?>
            <img src="<?php echo $logoimg ?>" alt="this is logo">
        </a>
        <?php 
            wp_nav_menu( [
                'theme_location' => 'top-menu'
            ] );
        ?>
     
</header>


<!-- mobile nav -->
 <header class="mobile-nav">
    <a href="#" class="mobile-nav__logo">
        <?php $logoimg=get_header_image();  ?>
        <img src="<?php echo $logoimg ?>" alt="this is logo">
    </a>
    <a href="" class="navbar-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-align-justify-icon lucide-align-justify"><path d="M3 12h18"/><path d="M3 18h18"/><path d="M3 6h18"/></svg>
    </a>
    <nav class="mobile-nav__container">
        <?php 
                wp_nav_menu( [
                    'theme_location' => 'mobile-menu'
                ] );
            ?>
    </nav>
 </header>