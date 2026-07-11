<?php
///template/global/page_icon.php
$defaultUserDir = $_COOKIE['userLanguageDir'] ?? $userLanguageDir ?? 'ltr';
$userLanguage = $userLanguage ?? 'fa';
$baseUrl = htmlspecialchars($_SERVER['HTTP_HOST'] ?? '');
$requestUri = htmlspecialchars($_SERVER['REQUEST_URI'] ?? '');



if (!isset($_SESSION['pageUserTitle']) or $_SESSION['pageUserTitle'] == '') {
    $pageUserTitle = htmlspecialchars(_lang['user_title'] ?? 'User Panel');
} elseif (!isset($_SESSION['pageUserTitle'][1]) or $_SESSION['pageUserTitle'][1] == '') {
    $pageUserTitle = _lang[$_SESSION['pageUserTitle'][0]];
} else {
    $pageUserTitle = _lang[$_SESSION['pageUserTitle'][0]] . " " . $_SESSION['pageUserTitle'][1];
}
?>

<!DOCTYPE html>
<html lang="<?php echo $userLanguage; ?>" data-layout="topnav" dir="<?php echo $defaultUserDir; ?>">

<head>
    <meta charset="utf-8" />
    <title><?php echo $pageUserTitle; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Mehdi Imeni: Imeni1982@gmail.com" />

    <!-- Primary Meta Tags -->
    <meta name="theme-color" content="#ffffff">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="apple-mobile-web-app-title" content="<?php echo $pageUserTitle; ?>">
    <meta name="application-name" content="<?php echo $pageUserTitle; ?>">
    <link rel="icon"  href="./itheme/panel/icon/favicon.ico">


