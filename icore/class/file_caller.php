<?php 
class FileCaller
{
    private static $instance;

    const TYPE_REQUIRE = 'require';
    const TYPE_INCLUDE = 'include';

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function includeFileWithController($fullDirAddress, $nameFolder, $name, $typeInclude = self::TYPE_REQUIRE)
    {
        $this->ensureFileLocationExists($fullDirAddress, '/controller/', $nameFolder, $name . '.php');
        $this->ensureFileLocationExists($fullDirAddress, '/template/', $nameFolder, $name . '.php');
        $this->ensureFileLocationExists($fullDirAddress, '/view/', $nameFolder, $name . '.php');

        $filePath = $fullDirAddress . '/view/' . $nameFolder . '/' . $name . '.php';

        if ($typeInclude === self::TYPE_REQUIRE) {
            require_once $filePath;
        } else {
            include $filePath;
        }
    }

    public function includeFileJustController($fullDirAddress, $nameFolder, $name, $typeInclude = self::TYPE_REQUIRE)
    {
        $this->ensureFileLocationExists($fullDirAddress, '/controller/', $nameFolder, $name . '.php');
        $filePath = $fullDirAddress . '/controller/' . $nameFolder . '/' . $name . '.php';

        if ($typeInclude === self::TYPE_REQUIRE) {
            require_once $filePath;
        } else {
            include $filePath;
        }
    }

    public function includeModifiedFileWithController($fullDirAddress, $nameFolder, $name, $typeModify, $typeInclude = self::TYPE_REQUIRE)
    {
        $this->ensureFileLocationExists($fullDirAddress, '/controller/', $nameFolder, $name . 'Modify.php');
        $this->ensureFileLocationExists($fullDirAddress, '/template/', $nameFolder, $name . 'Modify.php');
        $this->ensureFileLocationExists($fullDirAddress, '/view/', $nameFolder, $name . 'Modify.php');

        $filePath = $fullDirAddress . '/view/' . $nameFolder . '/' . $name . 'Modify.php';

        if ($typeInclude === self::TYPE_REQUIRE) {
            require_once $filePath;
        } else {
            include $filePath;
        }
    }

    private function ensureFileLocationExists($fullDirAddress, $type, $nameFolder, $nameFile)
    {
        $folderPath = $fullDirAddress . $type . $nameFolder;

        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        $filePath = $folderPath . '/' . $nameFile;

        if (!file_exists($filePath)) {
            $this->createFile($filePath, $type, $nameFolder, $nameFile,$fullDirAddress);
        }
    }

    private function createFile($filePath, $type, $nameFolder, $nameFile,$fullDirAddress)
    {
        $fOpen = fopen($filePath, 'x');
        fwrite($fOpen, "<?php\n");
        fwrite($fOpen, "//$type$nameFolder$nameFile\n");

        if ($type == '/template/') {
            fwrite($fOpen, "?>\n");
        }

        if ($type == '/view/') {
            fwrite($fOpen, "include '$fullDirAddress/controller/$nameFolder$nameFile';\n");
            fwrite($fOpen, "include '$fullDirAddress/template/$nameFolder$nameFile';\n");
        }

        fclose($fOpen);
    }
}
