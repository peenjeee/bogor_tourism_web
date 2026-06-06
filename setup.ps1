# BogorXplore local setup script for Windows PowerShell.
# Run from the repository root: .\setup.ps1

$ErrorActionPreference = "Stop"

$RootDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$LaravelDir = Join-Path $RootDir "web_recommendation"
$FlaskDir = Join-Path $RootDir "flask_api"
$DefaultDatabaseName = "bogorxplore"

function Write-Step {
    param([string] $Message)
    Write-Host ""
    Write-Host "==> $Message" -ForegroundColor Cyan
}

function Require-Command {
    param([string] $Name, [string] $InstallHint)

    $command = Get-Command $Name -ErrorAction SilentlyContinue
    if (-not $command) {
        throw "$Name is not available. $InstallHint"
    }

    return $command.Source
}

function Set-EnvValue {
    param(
        [string] $Path,
        [string] $Name,
        [string] $Value
    )

    $pattern = "^\s*#?\s*$([regex]::Escape($Name))="
    $lines = Get-Content -Path $Path
    $updated = $false

    $lines = $lines | ForEach-Object {
        if ($_ -match $pattern) {
            $updated = $true
            "$Name=$Value"
        } else {
            $_
        }
    }

    if (-not $updated) {
        $lines += "$Name=$Value"
    }

    Set-Content -Path $Path -Value $lines -Encoding UTF8
}

function Get-EnvValue {
    param(
        [string] $Path,
        [string] $Name,
        [string] $Default = ""
    )

    $pattern = "^\s*$([regex]::Escape($Name))=(.*)$"

    foreach ($line in Get-Content -Path $Path) {
        if ($line -match $pattern) {
            return $Matches[1].Trim()
        }
    }

    return $Default
}

if (-not (Test-Path $LaravelDir)) {
    throw "Laravel directory not found: $LaravelDir"
}

if (-not (Test-Path $FlaskDir)) {
    throw "Flask API directory not found: $FlaskDir"
}

$php = Require-Command "php" "Install PHP 8.2+ or enable it in Laragon."
$composer = Require-Command "composer" "Install Composer or enable it in Laragon."
$npm = Require-Command "npm" "Install Node.js 18+."
$pythonCommand = Get-Command "python" -ErrorAction SilentlyContinue
if (-not $pythonCommand) {
    $pythonCommand = Get-Command "py" -ErrorAction SilentlyContinue
}
if (-not $pythonCommand) {
    throw "Python is not available. Install Python 3.10+."
}
$python = $pythonCommand.Source

Write-Host "BogorXplore setup" -ForegroundColor Green
Write-Host "Repository: $RootDir"

Write-Step "Installing Flask API dependencies"
Push-Location $FlaskDir
try {
    & $python -m pip install -r requirements.txt
}
finally {
    Pop-Location
}

Write-Step "Installing Laravel dependencies"
Push-Location $LaravelDir
try {
    & $composer install
    & $npm install

    $envPath = Join-Path $LaravelDir ".env"
    $envExamplePath = Join-Path $LaravelDir ".env.example"

    if (-not (Test-Path $envPath)) {
        Copy-Item -Path $envExamplePath -Destination $envPath
        Write-Host "Created web_recommendation\.env from .env.example"
    }

    Set-EnvValue -Path $envPath -Name "FLASK_API_URL" -Value "http://localhost:5000"
    Set-EnvValue -Path $envPath -Name "FLASK_API_TIMEOUT" -Value "30"
    Set-EnvValue -Path $envPath -Name "DB_CONNECTION" -Value "mysql"
    Set-EnvValue -Path $envPath -Name "DB_HOST" -Value (Get-EnvValue -Path $envPath -Name "DB_HOST" -Default "127.0.0.1")
    Set-EnvValue -Path $envPath -Name "DB_PORT" -Value (Get-EnvValue -Path $envPath -Name "DB_PORT" -Default "3306")
    Set-EnvValue -Path $envPath -Name "DB_DATABASE" -Value (Get-EnvValue -Path $envPath -Name "DB_DATABASE" -Default $DefaultDatabaseName)
    Set-EnvValue -Path $envPath -Name "DB_USERNAME" -Value (Get-EnvValue -Path $envPath -Name "DB_USERNAME" -Default "root")
    Set-EnvValue -Path $envPath -Name "DB_PASSWORD" -Value (Get-EnvValue -Path $envPath -Name "DB_PASSWORD" -Default "")

    $dbHost = Get-EnvValue -Path $envPath -Name "DB_HOST" -Default "127.0.0.1"
    $dbPort = Get-EnvValue -Path $envPath -Name "DB_PORT" -Default "3306"
    $dbName = Get-EnvValue -Path $envPath -Name "DB_DATABASE" -Default $DefaultDatabaseName
    $dbUser = Get-EnvValue -Path $envPath -Name "DB_USERNAME" -Default "root"
    $dbPassword = Get-EnvValue -Path $envPath -Name "DB_PASSWORD" -Default ""

    Write-Host "Using MySQL database '$dbName' on ${dbHost}:${dbPort}"

    $mysql = Get-Command "mysql" -ErrorAction SilentlyContinue
    if ($mysql -and $dbName -match "^[A-Za-z0-9_]+$") {
        Write-Host "Ensuring MySQL database exists..."
        $previousMysqlPassword = $env:MYSQL_PWD

        try {
            if ($dbPassword -ne "") {
                $env:MYSQL_PWD = $dbPassword
            }

            & $mysql.Source -h $dbHost -P $dbPort -u $dbUser -e "CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

            if ($LASTEXITCODE -ne 0) {
                throw "Unable to create or access MySQL database '$dbName'. Check DB_* values in web_recommendation\.env."
            }
        }
        finally {
            $env:MYSQL_PWD = $previousMysqlPassword
        }
    } else {
        Write-Host "mysql CLI not found or database name is not simple. Make sure '$dbName' exists before migrations." -ForegroundColor Yellow
    }

    $envLines = Get-Content -Path $envPath
    if ($envLines -match "^APP_KEY=$") {
        & $php artisan key:generate
    } else {
        Write-Host "APP_KEY already exists; skipping key generation."
    }

    & $php artisan migrate
    & $php artisan db:seed --class=PlaceSeeder
    & $npm run build
    & $php artisan optimize:clear
}
finally {
    Pop-Location
}

Write-Host ""
Write-Host "Setup complete." -ForegroundColor Green
Write-Host ""
Write-Host "Start the app with three terminals:" -ForegroundColor Cyan
Write-Host "1. cd flask_api; python app.py"
Write-Host "2. cd web_recommendation; php artisan serve"
Write-Host "3. cd web_recommendation; npm run dev"
Write-Host ""
Write-Host "Web: http://localhost:8000"
Write-Host "API: http://localhost:5000"
