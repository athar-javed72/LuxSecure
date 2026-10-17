<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ZipArchive;

class BackupCommand extends Command
{
    protected $signature = 'luxsecure:backup {--db : Dump the database} {--storage : Archive public storage}';

    protected $description = 'Create a LuxSecure backup archive (database dump and/or storage).';

    public function handle(): int
    {
        $doDb = $this->option('db') || (! $this->option('db') && ! $this->option('storage'));
        $doStorage = $this->option('storage') || (! $this->option('db') && ! $this->option('storage'));

        $stamp = now()->format('Ymd_His');
        $relativeDir = trim(config('luxsecure.backup.path', 'backups'), '/').'/'.$stamp;
        $absoluteDir = storage_path('app/'.$relativeDir);
        File::ensureDirectoryExists($absoluteDir);

        if ($doDb) {
            $this->backupDatabase($absoluteDir);
        }

        if ($doStorage) {
            $this->backupStorage($absoluteDir);
        }

        $this->pruneOldBackups();

        $this->info('Backup written to storage/app/'.$relativeDir);

        return self::SUCCESS;
    }

    private function backupDatabase(string $dir): void
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver === 'sqlite') {
            $dbPath = config("database.connections.{$connection}.database");
            if (is_string($dbPath) && File::exists($dbPath)) {
                File::copy($dbPath, $dir.'/database.sqlite');
                $this->line('SQLite database copied.');
            }

            return;
        }

        if ($driver === 'mysql') {
            $host = config("database.connections.{$connection}.host");
            $port = config("database.connections.{$connection}.port");
            $database = config("database.connections.{$connection}.database");
            $username = config("database.connections.{$connection}.username");
            $password = config("database.connections.{$connection}.password");
            $out = $dir.'/database.sql';

            $cmd = sprintf(
                'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
                escapeshellarg((string) $host),
                escapeshellarg((string) $port),
                escapeshellarg((string) $username),
                escapeshellarg((string) $password),
                escapeshellarg((string) $database),
                escapeshellarg($out)
            );

            exec($cmd, $output, $code);
            if ($code === 0 && File::exists($out)) {
                $this->line('MySQL dump created.');
            } else {
                $this->warn('mysqldump failed or is unavailable. See docs/BACKUP.md for manual steps.');
                File::put($dir.'/database-README.txt', "Run mysqldump manually for {$database}.");
            }
        }
    }

    private function backupStorage(string $dir): void
    {
        $source = storage_path('app/public');
        if (! File::isDirectory($source)) {
            $this->warn('No public storage directory to archive.');

            return;
        }

        $zipPath = $dir.'/storage-public.zip';
        if (! class_exists(ZipArchive::class)) {
            File::put($dir.'/storage-README.txt', 'ZipArchive unavailable; copy storage/app/public manually.');

            return;
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->warn('Could not create storage zip.');

            return;
        }

        foreach (File::allFiles($source) as $file) {
            $zip->addFile($file->getRealPath(), $file->getRelativePathname());
        }
        $zip->close();
        $this->line('Public storage archived.');
    }

    private function pruneOldBackups(): void
    {
        $keep = (int) config('luxsecure.backup.keep', 7);
        $root = storage_path('app/'.trim(config('luxsecure.backup.path', 'backups'), '/'));
        if (! File::isDirectory($root)) {
            return;
        }

        collect(File::directories($root))
            ->sort()
            ->values()
            ->slice(0, max(0, count(File::directories($root)) - $keep))
            ->each(fn ($dir) => File::deleteDirectory($dir));
    }
}
