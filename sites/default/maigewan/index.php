<?php
/**
 * Default Site Template - Maintenance Page
 * Tabler Material Design - Minimal & Clean
 */

defined('BLUDIT') or define('BLUDIT', true);

// 获取语言配置
$lang = 'zh_CN';
$usersFile = __DIR__ . '/../../../bl-kernel/_maigewan/authz/users.php';
if (file_exists($usersFile)) {
    $data = json_decode(str_replace("<?php defined('BLUDIT') or die('Bludit CMS.'); ?>", '', file_get_contents($usersFile)), true);
    if (isset($data['language'])) $lang = $data['language'];
}

// 加载翻译
$i18n = json_decode(file_get_contents(__DIR__ . '/../../../bl-languages/pages/site-bootstrap/' . $lang . '.json'), true) ?: [];
$t = function($key, $default = '') use ($i18n) { return $i18n[$key] ?? $default; };

// 当前域名
$domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
?>
<!doctype html>
<html lang="<?= $lang === 'zh_CN' ? 'zh-CN' : 'en' ?>">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title><?= htmlspecialchars($t('page_title', 'Website Under Construction')) ?></title>
    <link href="/bl-kernel/admin/themes/tabler/css/tabler.min.css" rel="stylesheet"/>
    <style>@import url("https://rsms.me/inter/inter.css");</style>
</head>
<body class="border-top-wide border-primary">
    <script src="/bl-kernel/admin/themes/tabler/js/tabler-theme.min.js"></script>
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="empty">
                <div class="empty-img"><svg class="img" xmlns="http://www.w3.org/2000/svg" height="160" fill="none" viewBox="0 0 800 600"><style>:where(.theme-dark,[data-bs-theme=dark]) .tblr-illustrations-computer-fix-a{fill:#000;opacity:.07}:where(.theme-dark,[data-bs-theme=dark]) .tblr-illustrations-computer-fix-b{fill:#1a2030}:where(.theme-dark,[data-bs-theme=dark]) .tblr-illustrations-computer-fix-c{fill:#232b41}:where(.theme-dark,[data-bs-theme=dark]) .tblr-illustrations-computer-fix-d{fill:#454c5e}@media (prefers-color-scheme:dark){.tblr-illustrations-computer-fix-a{fill:#000;opacity:.07}.tblr-illustrations-computer-fix-b{fill:#1a2030}.tblr-illustrations-computer-fix-c{fill:#232b41}.tblr-illustrations-computer-fix-d{fill:#454c5e}}</style><path d="M165.042 305.17C165.042 347.031 209.306 377.394 228.857 411.189 249.036 446.056 253.885 499.359 288.752 519.524c33.81 19.551 81.921-2.317 123.782-2.317s90.972 21.868 124.767 2.317c34.867-20.165 39.716-73.468 59.895-108.335 19.551-33.795 63.815-64.158 63.815-106.019s-44.264-72.209-63.815-105.004c-20.179-34.868-25.028-88.17-59.895-108.336C502.506 71.2798 454.381 93.1471 412.534 93.1471c-41.847 0-89.972-21.8673-123.782-2.3169-34.867 20.1659-39.716 73.4682-59.895 108.3358C209.306 232.961 165.042 263.323 165.042 305.17Z" fill="#F7F8FC" class="tblr-illustrations-computer-fix-a"/><path d="M375.492 479.923c94.989 0 171.993-3.099 171.993-6.922s-77.004-6.922-171.993-6.922c-94.989 0-171.992 3.099-171.992 6.922s77.003 6.922 171.992 6.922Z" fill="#A6A9B3" class="tblr-illustrations-computer-fix-b"/><path d="M191.729 429.895h367.497c11.785 0 21.338-9.553 21.338-21.338V198.408c0-11.785-9.553-21.338-21.338-21.338H191.729c-11.784 0-21.338 9.553-21.338 21.338v210.149c0 11.785 9.554 21.338 21.338 21.338Z" fill="#fff" class="tblr-illustrations-computer-fix-c"/><path d="M585.585 197.736c-.19-6.865-3.047-13.386-7.966-18.179-4.918-4.793-11.511-7.481-18.378-7.493H191.687c-6.985.008-13.681 2.785-18.62 7.724s-7.717 11.619-7.724 18.604v210.235c.007 6.985 2.785 13.681 7.724 18.62s11.635 7.716 18.62 7.724h134.321v8.953c-1.43 9.739-7.966 12.485-12.542 13.186h-50.929c-1.034.004-2.025.416-2.756 1.148-.731.731-1.143 1.722-1.147 2.756v4.52c.004 1.034.416 2.025 1.147 2.756.731.732 1.722 1.144 2.756 1.148h225.967c.513 0 1.021-.101 1.495-.297s.904-.483 1.266-.846c.363-.362.65-.793.847-1.266.196-.474.297-.982.297-1.494v-4.52c0-.513-.101-1.021-.297-1.494s-.484-.904-.847-1.267c-.362-.362-.792-.65-1.266-.846s-.982-.297-1.495-.297h-51.028c-4.577-.701-11.17-3.461-12.543-13.258v-8.953h134.308c6.985-.007 13.683-2.785 18.624-7.723s7.722-11.638 7.734-18.621V198.336c0-.157 0-.372-.014-.6Zm-10.012 210.835c-.003 4.327-1.724 8.476-4.783 11.535s-7.208 4.78-11.535 4.784H191.701c-4.327-.004-8.475-1.725-11.535-4.784s-4.781-7.208-4.784-11.535V198.336c.003-4.326 1.724-8.475 4.784-11.534s7.208-4.78 11.535-4.784h367.554c2.229-.006 4.436.451 6.479 1.344 2.922 1.264 5.41 3.355 7.158 6.016s2.681 5.775 2.681 8.958v210.235Z" fill="#232B41" class="tblr-illustrations-computer-fix-d"/><path d="M211.108 222.706h232.346c1.043 0 1.888-.845 1.888-1.887v-6.021c0-1.043-.845-1.888-1.888-1.888H211.108c-1.042 0-1.888.845-1.888 1.888v6.021c0 1.042.846 1.887 1.888 1.887Z" fill="#0455A4"/></svg></div>
                <p class="empty-title"><?= htmlspecialchars($t('main_title', 'Website Not Created')) ?></p>
                <p class="empty-subtitle text-secondary"><?= htmlspecialchars($t('description', 'This domain has not been created yet.')) ?></p>
                <p class="text-muted"><small><?= htmlspecialchars($t('domain_label', 'Domain')) ?>: <strong><?= htmlspecialchars($domain) ?></strong></small></p>
                <div class="empty-action">
                    <a href="/admin/" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                        <?= htmlspecialchars($t('admin_button', 'Go to Admin Panel')) ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php
    // Footer
    if (!isset($L)) {
        $L = new class($lang) {
            private $d;
            function __construct($l) { $this->d = json_decode(file_get_contents(__DIR__.'/../../../bl-languages/pages/site-bootstrap/'.$l.'.json'), true) ?: []; }
            function get($k) { return $this->d[$k] ?? $k; }
        };
    }
    include __DIR__ . '/../../../bl-kernel/admin/themes/tabler/includes/footer.php';
    ?>
    <script src="/bl-kernel/admin/themes/tabler/js/tabler.min.js" defer></script>
</body>
</html>
