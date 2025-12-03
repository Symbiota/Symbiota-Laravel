<!-- Responsive viewport -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Symbiota styles -->
@vite(['resources/css/app.css'])
<link href="{{ url('fix_child.css') }}" type="text/css" rel="stylesheet">

<link href="<?= $GLOBALS['CSS_BASE_PATH'] ?>/symbiota/main.css?ver=<?= $GLOBALS['CSS_VERSION'] ?>" type="text/css" rel="stylesheet">
<link href="<?= $GLOBALS['CSS_BASE_PATH'] ?>/symbiota/customizations.css?ver=<?= $GLOBALS['CSS_VERSION'] ?>" type="text/css" rel="stylesheet">

<?php
if($GLOBALS['ACCESSIBILITY_ACTIVE']){
        ?>
        <link href="<?= $GLOBALS['CSS_BASE_PATH'] ?>/symbiota/accessibility-compliant.css?ver=<?= $GLOBALS['CSS_VERSION'] ?>" type="text/css" rel="stylesheet" data-accessibility-link="accessibility-css-link" >
        <link href="<?= $GLOBALS['CSS_BASE_PATH'] ?>/symbiota/condensed.css?ver=<?= $GLOBALS['CSS_VERSION'] ?>" type="text/css" rel="stylesheet" data-accessibility-link="accessibility-css-link" disabled >
        <?php
} else{
        ?>
        <link href="<?= $GLOBALS['CSS_BASE_PATH'] ?>/symbiota/accessibility-compliant.css?ver=<?= $GLOBALS['CSS_VERSION'] ?>" type="text/css" rel="stylesheet" data-accessibility-link="accessibility-css-link" dis>
        <link href="<?= $GLOBALS['CSS_BASE_PATH'] ?>/symbiota/condensed.css?ver=<?= $GLOBALS['CSS_VERSION'] ?>" type="text/css" rel="stylesheet" data-accessibility-link="accessibility-css-link" >
        <?php
}
?>

<script src="<?= $GLOBALS['CLIENT_ROOT'] ?>/js/symb/lang.js" type="text/javascript"></script>
