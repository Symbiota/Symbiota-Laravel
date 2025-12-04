<?php
if ($LANG_TAG == 'en' || ! file_exists($SERVER_ROOT . '/content/lang/templates/header.' . $LANG_TAG . '.php')) {
    include_once $SERVER_ROOT . '/content/lang/templates/header.en.php';
} else {
    include_once $SERVER_ROOT . '/content/lang/templates/header.' . $LANG_TAG . '.php';
}
$collectionSearchPage = ! empty($SHOULD_USE_HARVESTPARAMS) ? '/collections/index.php' : '/collections/search/index.php';

$navigations = [
    $LANG['H_HOME'] => $CLIENT_ROOT . '/index.php',
    $LANG['H_SEARCH'] => $CLIENT_ROOT . $collectionSearchPage,
    $LANG['H_MAP_SEARCH'] => $CLIENT_ROOT . '/collections/map/index.php',
    $LANG['H_INVENTORIES'] => $CLIENT_ROOT . '/checklists/index.php',
    $LANG['H_IMAGES'] => $CLIENT_ROOT . '/imagelib/search.php',
    $LANG['H_DATA_USAGE'] => $CLIENT_ROOT . '/includes/usagepolicy.php',
    $LANG['H_HELP'] => 'https://docs.symbiota.org/about/', // target="_blank" rel="noopener noreferrer
    $LANG['H_SITEMAP'] => $CLIENT_ROOT . '/sitemap.php',
];

?>
<style>
.menu li a:hover {
    color: var(--color-accent);
}

.menu li a{
    color: var(--color-navbar-content);
    text-decoration:none;
    font-weight: 700;
}

nav .button-tertiary:hover, header .button-secondary:hover {
	--tw-shadow: 0 20px 25px -5px var(--tw-shadow-color, rgb(0 0 0 / 0.1)), 0 8px 10px -6px var(--tw-shadow-color, rgb(0 0 0 / 0.1));
    --tw-ring-color: var(--color-accent);
    --tw-ring-offset-width: 0px;
    --tw-ring-inset: 0;

    background-color: var(--color-primary-lighter);
    box-shadow: 0 0 0 calc(4px + var(--tw-ring-offset-width)) var(--tw-ring-color);
}

header .button-tertiary, header .button-secondary {
    background-color: var(--color-primary);
    color: var(--color-primary-content);
    border: 0 solid;
    border-radius: 0.5rem;
    padding: 0.25rem 0.625rem;
    line-height: 1.5;
    display: flex;
    align-items: center;
    height: fit-content;
    width: fit-content;
    letter-spacing: 0px;
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    transition: none;
    font-weight: 700;
    font-size: 1rem;
}

</style>
<header style="display:flex; flex-direction: column;">
		<div style="background-image: var(--symb-banner-url); background-position: var(--symb-banner-position); background-size:cover; height:7rem;">
			<a class="screen-reader-only" href="#end-nav"><?= $LANG['H_SKIP_NAV'] ?></a>
            <div style="background-color: var(--color-banner-overlay); width:100%; height:100%; padding: 1rem 0; display:flex; align-items:center; color:var(--color-banner-overlay-content); font-family: sans-serif;">

            <div style="display: flex; padding-left:3rem;">
                <div style="width: 7.5rem; height:fit-content;">
                    <a style="display:block; line-height:0" href="<?= $CLIENT_ROOT ?>"><img src="/icons/brand.svg" alt="Symbiota logo"></a>
                </div>

                <div style="margin-left:2rem; display: flex; justify-content:center; flex-direction: column; text-shadow:0px 1px 2px var(--tw-text-shadow-color, rgb(0 0 0 / 0.1)), 0px 3px 2px var(--tw-text-shadow-color, rgb(0 0 0 / 0.1)), 0px 4px 8px var(--tw-text-shadow-color, rgb(0 0 0 / 0.1));">
                    <h1 style="margin:0; font-weight: 700; font-size: 2.25rem; color:var(--color-banner-overlay-content); line-height:calc(2.5/2.25);">Symbiota Brand New Portal</h1>
                    <h4 style="margin:0; font-weight: 700; color:var(--color-banner-overlay-content); line-height:calc(1.5);" >Redesigned by the Symbiota Support Hub</h4>
                </div>
            </div>

			<nav style="display:flex;align-items:center; gap:0.75rem; flex-grow:1; justify-content:end; margin-right: 1rem;" class="top-login" aria-label="horizontal-nav">
				<?php
                if ($USER_DISPLAY_NAME) {
                    ?>
					<div>
						<?= $LANG['H_WELCOME'] . ' ' . $USER_DISPLAY_NAME ?>!
					</div>
					<span id="profile">
						<form name="profileForm" method="post" action="<?= $CLIENT_ROOT . '/profile/viewprofile.php' ?>">
							<button class="button button-tertiary" name="profileButton" type="submit"><?= $LANG['H_MY_PROFILE'] ?></button>
						</form>
					</span>
					<span id="logout">
						<form name="logoutForm" method="post" action="<?= $CLIENT_ROOT ?>/profile/index.php?submit=logout">
							<button class="button button-secondary" name="logoutButton" type="submit"><?= $LANG['H_LOGOUT'] ?></button>
						</form>
					</span>
					<?php
                } else {
                    ?>
					<span id="contactUs">
						<button class="button button-tertiary bottom-breathing-room-rel left-breathing-room-rel" onclick="window.location.href='#'"><?= $LANG['H_CONTACT_US'] ?></button>
					</span>
					<span id="login">
						<form name="loginForm" method="post" action="<?= $CLIENT_ROOT . '/profile/index.php' ?>">
							<input name="refurl" type="hidden" value="<?= htmlspecialchars($_SERVER['SCRIPT_NAME'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES) ?>">
							<button class="button button-secondary bottom-breathing-room-rel left-breathing-room-rel" name="loginButton" type="submit"><?= $LANG['H_LOGIN'] ?></button>
						</form>
					</span>
					<?php
                }
?>
			</nav>
            </div>
		</div>
		<div class="menu-wrapper" style="background-color: var(--color-primary);">
			<nav class="top-menu" aria-label="hamburger-nav">
				<ul class="menu" style="display:flex; list-style:none; gap:0.5rem; align-items:center; justify-content: center; margin:0; height:3.5rem; padding: 0;">
                    <?php foreach ($navigations as $name => $link) { ?>
                        <li style="padding: 0.5rem; margin: 0.5rem 0;">
                            <a style="" href="<?= $link ?>"><?= $name ?></a>
                        </li>
                    <?php } ?>
                    <li id="lang-select-li" style="padding:0.5rem;">
                        <label class="screen-reader-only" for="language-selection"><?= $LANG['H_SELECT_LANGUAGE'] ?>: </label>
                        <select style="font-size: 0.75rem; background-color: var(--color-base-300); padding: 0.25rem 0.5rem; border: none; border-radius: .375rem; font-weight: 700;" oninput="setLanguage(this)" id="language-selection" name="language-selection">
                            <option value="en">English</option>
                            <option value="es" <?= ($LANG_TAG == 'es' ? 'SELECTED' : '') ?>>Español</option>
                            <option value="fr" <?= ($LANG_TAG == 'fr' ? 'SELECTED' : '') ?>>Français</option>
                        </select>
                    </li>
				</ul>
			</nav>
		</div>
		<div id="end-nav"></div>
</header>
