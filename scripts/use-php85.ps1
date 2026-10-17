# Prefers PHP 8.5 for this PowerShell session (Laravel 13 / Composer platform_check).
$php85 = 'C:\php\php-8.5.11-nts-Win32-vs17-x64'
if (-not (Test-Path "$php85\php.exe")) {
    Write-Error "PHP 8.5 not found at $php85. Install PHP 8.4+ or 8.5 and update this path."
    exit 1
}
$env:Path = "$php85;" + (($env:Path -split ';' | Where-Object { $_ -and ($_ -notmatch 'php-8\.2') }) -join ';')
Write-Host "Using: " -NoNewline
php -v
