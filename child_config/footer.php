<?php
$logos = [
    [
        "img" => '/images/logo_nsf.gif',
        "link" => 'https://www.nsf.gov',
        "title" => 'NSF'
    ],
    [
        "img" => '/images/logo_idig.png',
        "link" => 'http://idigbio.org',
        "title" => 'iDigBio'
    ],
    [
        "img" => '/images/logo-asu-biokic.png',
        "link" => 'https://biokic.asu.edu',
        "title" => 'Biodiversity Knowledge Integration Center'
    ],
];

?>
<style>
footer p a {
    font-weight: 500;
    font-size: .75rem;
    line-height: calc(1 / 0.75);
}

footer p {
    font-size: .75rem;
    line-height: calc(1 / 0.75);
}

</style>
<footer style="background-color: var(--color-footer); padding: 2rem; text-align:center; line-height:0;">
    <div style="display: flex; flex-wrap: wrap; max-width: var(--max-innertext-width); margin: auto; justify-content: center;">
        <?php foreach($logos as $logo): ?>
        <div style="display:flex; flex-basis:0; flex-grow: 1; justify-content: center; margin: auto 0;">
            <div style="margin: 0 auto; width: fit-content; height: fit-content;">
            <a href="<?= $logo['link']?>">
                <img style="max-width:13rem; max-height:5rem;" src="<?= $logo['img'] ?>" alt="<?= $logo['title'] ?>"/>
            </a>
            </div>
        </div>
        <?php endforeach ?>
    </div>

    <div style="margin-top: 1rem;">
        <div style="font-size:0.75rem;">
            <p style="margin: 0">
                <?= $LANG['F_NSF_AWARDS'] ?> <a href="https://www.nsf.gov/awardsearch/showAward?AWD_ID=" target="_blank">#------</a>
            </p>
            <p style="margin: 0">
                <?= $LANG['F_MORE_INFO'] ?>, <a href="https://docs.symbiota.org/about/" target="_blank" rel="noopener noreferrer"><?= $LANG['F_READ_DOCS'] ?></a> <?= $LANG['F_CONTACT'] ?>
                <a href="https://symbiota.org/contact-the-support-hub/" target="_blank" rel="noopener noreferrer"><?= $LANG['F_SSH'] ?></a>

            </p>
            <p style="margin: 0">
                <?= $LANG['F_POWERED_BY'] ?> <a href="https://symbiota.org/" target="_blank">Symbiota</a>
            </p>
        </div>
    </div>
</footer>
