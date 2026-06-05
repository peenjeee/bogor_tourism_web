# Bogor Tourism Website - Setup Script
# Run this script after Laravel installation

Write-Host "🚀 Setting up Bogor Tourism Website..." -ForegroundColor Cyan

# Add Flask API URL to .env
Write-Host "`n📝 Configuring .env..." -ForegroundColor Yellow
$envPath = "c:\Users\62857\.gemini\antigravity\scratch\bogor_tourism_web\web_recommendation\.env"
Add-Content -Path $envPath -Value "`n# Flask API"
Add-Content -Path $envPath -Value "FLASK_API_URL=http://localhost:5000"

# Configure database (adjust as needed)
Write-Host "💾 Configure your database in .env file:" -ForegroundColor Yellow
Write-Host "   DB_DATABASE=bogor_tourism" -ForegroundColor White
Write-Host "   DB_USERNAME=root" -ForegroundColor White
Write-Host "   DB_PASSWORD=your_password" -ForegroundColor White
Write-Host "`nPress any key after configuring database..." -ForegroundColor Yellow
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

# Run migrations
Write-Host "`n🗄️  Running migrations..." -ForegroundColor Yellow
Set-Location "c:\Users\62857\.gemini\antigravity\scratch\bogor_tourism_web\web_recommendation"
php artisan migrate

# Seed database
Write-Host "`n🌱 Seeding database with 296 tourism places..." -ForegroundColor Yellow
php artisan db:seed --class=PlaceSeeder

# Install npm dependencies (if not done)
Write-Host "`n📦 Installing NPM dependencies..." -ForegroundColor Yellow
npm install

# Build assets
Write-Host "`n🎨 Building Tailwind CSS..." -ForegroundColor Yellow
npm run build

Write-Host "`n✅ Setup complete!" -ForegroundColor Green
Write-Host "`nNext steps:" -ForegroundColor Cyan
Write-Host "1. Start Flask API:" -ForegroundColor White
Write-Host "   cd ..\flask_api" -ForegroundColor Gray
Write-Host "   python app.py" -ForegroundColor Gray
Write-Host "`n2. Start Laravel (new terminal):" -ForegroundColor White  
Write-Host "   php artisan serve" -ForegroundColor Gray
Write-Host "`n3. Visit http://localhost:8000" -ForegroundColor White
