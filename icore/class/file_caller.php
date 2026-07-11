<?php

class FileCaller
{
    private static ?FileCaller $instance = null;

    public const TYPE_REQUIRE = 'require';
    public const TYPE_INCLUDE = 'include';

    private function __construct()
    {
    }

    public static function getInstance(): FileCaller
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function includeFileWithController(
        string $baseDir,
        string $folder,
        string $name,
        string $typeInclude = self::TYPE_REQUIRE
    ): void {
        $this->ensureMvcFiles($baseDir, $folder, $name . '.php');

        $this->loadFile(
            $this->buildPath($baseDir, 'view', $folder, $name . '.php'),
            $typeInclude
        );
    }

    public function includeFileJustController(
        string $baseDir,
        string $folder,
        string $name,
        string $typeInclude = self::TYPE_REQUIRE
    ): void {
        $folder = $this->normalizeName($folder);
        $name = $this->normalizeName($name);

        $this->validateName($folder);
        $this->validateName($name);

        $fileName = $name . '.php';

        $this->ensureFileLocationExists($baseDir, 'controller', $folder, $fileName);

        $this->loadFile(
            $this->buildPath($baseDir, 'controller', $folder, $fileName),
            $typeInclude
        );
    }

    public function includeModifiedFileWithController(
        string $baseDir,
        string $folder,
        string $name,
        string $typeModify = 'Modify',
        string $typeInclude = self::TYPE_REQUIRE
    ): void {
        $suffix = $typeModify !== '' ? $typeModify : 'Modify';
        $fileName = $name . $suffix . '.php';

        $this->ensureMvcFiles($baseDir, $folder, $fileName);

        $this->loadFile(
            $this->buildPath($baseDir, 'view', $folder, $fileName),
            $typeInclude
        );
    }



    private function ensureFileLocationExists(
        string $baseDir,
        string $type,
        string $folder,
        string $fileName
    ): void {
        $folderPath = $this->buildPath($baseDir, $type, $folder);

        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        $filePath = $this->buildPath($baseDir, $type, $folder, $fileName);

        if (!file_exists($filePath)) {
            $this->createFile($filePath, $type, $folder, $fileName, $baseDir);
        }
    }

    private function createFile(
        string $filePath,
        string $type,
        string $folder,
        string $fileName,
        string $baseDir
    ): void {
        $handle = fopen($filePath, 'x');

        if ($handle === false) {
            throw new RuntimeException("Cannot create file: {$filePath}");
        }

        fwrite($handle, "<?php\n");
        fwrite($handle, "// {$type}/{$folder}/{$fileName}\n");

        if ($type === 'template') {
            fwrite($handle, "?>\n");
        }

        if ($type === 'view') {
            $controllerPath = $this->buildPath($baseDir, 'controller', $folder, $fileName);
            $templatePath = $this->buildPath($baseDir, 'template', $folder, $fileName);

            fwrite($handle, "require_once " . var_export($controllerPath, true) . ";\n");
            fwrite($handle, "require_once " . var_export($templatePath, true) . ";\n");
        }

        fclose($handle);
    }

    private function loadFile(string $filePath, string $typeInclude): void
    {
        if (!is_file($filePath)) {
            throw new RuntimeException("File not found: {$filePath}");
        }

        if ($typeInclude === self::TYPE_REQUIRE) {
            require_once $filePath;
            return;
        }

        include $filePath;
    }

    private function buildPath(string ...$parts): string
    {
        $cleanParts = [];

        foreach ($parts as $part) {
            $part = trim($part, "/\\");

            if ($part !== '') {
                $cleanParts[] = $part;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $cleanParts);
    }



    private function validateFileName(string $fileName): void
    {
        if (!preg_match('/^[a-zA-Z0-9_-]+\.php$/', $fileName)) {
            throw new InvalidArgumentException("Invalid file name: {$fileName}");
        }
    }

    private function __clone()
    {
    }

    public function __wakeup()
    {
        throw new Exception('Cannot unserialize FileCaller singleton.');
    }


    private function normalizeName(string $name): string
    {
        return trim($name, "/\\");
    }

    private function validateName(string $name): void
    {
        $name = $this->normalizeName($name);

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $name)) {
            throw new InvalidArgumentException("Invalid name: {$name}");
        }
    }

    private function ensureMvcFiles(string $baseDir, string $folder, string $fileName): void
    {
        $folder = $this->normalizeName($folder);

        $this->validateName($folder);
        $this->validateFileName($fileName);

        $this->ensureFileLocationExists($baseDir, 'controller', $folder, $fileName);
        $this->ensureFileLocationExists($baseDir, 'template', $folder, $fileName);
        $this->ensureFileLocationExists($baseDir, 'view', $folder, $fileName);
    }
}