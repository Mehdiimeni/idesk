<?php
///template/global/page_icon.php
$defultAdminDir = $_COOKIE['adminLanguageDir'] ?? $adminLanguageDir ?? 'ltr';
$adminLanguage = $adminLanguage ?? 'fa';


if (!isset($_SESSION['pageTitle']) or $_SESSION['pageTitle'] == '') {
    $pageTitle = htmlspecialchars(_lang['admin_title'] ?? 'Admin Panel');
} elseif(!isset($_SESSION['pageTitle'][1]) or $_SESSION['pageTitle'][1] == '') {
    $pageTitle = _lang[$_SESSION['pageTitle'][0]];
}else{
    $pageTitle = _lang[$_SESSION['pageTitle'][0]] ." ". $_SESSION['pageTitle'][1];
}

?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($adminLanguage); ?>" data-layout="topnav"
    dir="<?php echo htmlspecialchars($defultAdminDir); ?>">

<head>
    <meta charset="utf-8" />
    <title><?php echo $pageTitle; ?></title>
    <link rel="icon" href="../itheme/panel/icon/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="author" content="Mehdi Imeni: Imeni1982@gmail.com" />

    <!-- Primary Meta Tags -->
    <meta name="theme-color" content="#ffffff">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="apple-mobile-web-app-title" content="<?php echo $pageTitle; ?>">
    <meta name="application-name" content="<?php echo $pageTitle; ?>">