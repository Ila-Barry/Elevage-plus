@echo off
REM -----------------------------------------------------------------------------
REM run-local.bat
REM Démarre l'application Laravel en local et rend le serveur accessible sur le réseau
REM -----------------------------------------------------------------------------

SETLOCAL ENABLEEXTENSIONS ENABLEDELAYEDEXPANSION
cd /d %~dp0

necho ==================================================
echo Running Elevage-plus local server
echo Project root: %CD%
echo ==================================================

nREM 1) Préparer le fichier .env si nécessaire
if not exist .env (
    echo .env introuvable, copie de .env.example...
    copy /Y .env.example .env >nul
)

nREM 2) Générer une clé d'application si elle manque
for /f "tokens=1* delims==" %%A in ('findstr /B /C:"APP_KEY=" .env 2^>nul') do set APP_KEY_LINE=%%B
if "%APP_KEY_LINE%"=="" (
    echo Generation de APP_KEY...
    php artisan key:generate --force
) else (
    echo APP_KEY exists.
)

nREM 3) Afficher l'adresse réseau locale si disponible
set "LOCAL_IP=localhost"
for /f "tokens=2 delims=:" %%A in ('ipconfig ^| findstr /R /C:"IPv4" /C:"Adresse IPv4"') do (
    set "LOCAL_IP=%%A"
    goto :FoundIP
)
:FoundIP
set "LOCAL_IP=!LOCAL_IP: =!"
echo.
echo Serveur web: http://localhost:8000
echo Adresse reseau locale: http://!LOCAL_IP!:8000
echo.

nREM 4) Lancer Laravel en écoute sur 0.0.0.0:8000
start "Laravel Server" cmd /k "cd /d %~dp0 && php artisan serve --host=0.0.0.0 --port=8000"

nREM 5) Lancer Vite en mode development, si npm est installé
start "Vite Dev" cmd /k "cd /d %~dp0 && if exist package.json (npm run dev -- --host 0.0.0.0) else echo package.json introuvable, impossible de demarrer Vite."

necho.
echo Les serveurs sont demarres dans des fenetres distinctes.
echo Assurez-vous que le pare-feu Windows autorise le port 8000.
echo Appuyez sur une touche pour continuer...
pause >nul
ENDLOCAL