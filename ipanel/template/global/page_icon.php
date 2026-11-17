<?php
///template/global/page_icon.php
$defultAdminDir = $_COOKIE['adminLanguageDir'] ?? $adminLanguageDir ?? 'ltr';
$adminLanguage = $adminLanguage ?? 'fa';

$pageTitleParts = $_SESSION['pageTitle'] ?? [];
$pageTitleKey = is_array($pageTitleParts) ? (string) ($pageTitleParts[0] ?? '') : '';

if ($pageTitleKey === '') {
    $pageTitle = (string) (_lang['admin_title'] ?? 'Admin Panel');
} else {
    // Never raise Undefined array key for a new route. The route key is used as
    // a safe fallback until its dictionary caption is added.
    $pageTitle = (string) (_lang[$pageTitleKey] ?? $pageTitleKey);

    if (is_array($pageTitleParts) && !empty($pageTitleParts[1])) {
        $pageTitle .= ' ' . (string) $pageTitleParts[1];
    }
}

$pageTitleEscaped = htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($adminLanguage, ENT_QUOTES, 'UTF-8'); ?>" data-layout="topnav"
    dir="<?php echo htmlspecialchars($defultAdminDir, ENT_QUOTES, 'UTF-8'); ?>">

<head>
    <meta charset="utf-8" />
    <title><?php echo $pageTitleEscaped; ?></title>
    <link rel="icon" href="../itheme/panel/icon/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="author" content="Mehdi Imeni: Imeni1982@gmail.com" />

    <!-- Primary Meta Tags -->
    <meta name="theme-color" content="#ffffff">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="apple-mobile-web-app-title" content="<?php echo $pageTitleEscaped; ?>">
    <meta name="application-name" content="<?php echo $pageTitleEscaped; ?>">
