<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetupDatabase extends Command
{
    protected $signature = 'db:setup';
    protected $description = 'Setup database with SSL configuration for Aiven';

    public function handle()
    {
        $this->info('📦 Vérification de la connexion à la base de données...');

        try {
            DB::connection()->getPdo();
            $this->info('✅ Connexion à la base de données établie avec succès!');
            
            // Vérifier la version de MySQL
            $version = DB::select('SELECT VERSION() as version')[0]->version;
            $this->info("📊 Version MySQL: {$version}");
            
            // Vérifier si les tables existent déjà
            $tables = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
            $this->info("📋 Tables existantes: " . count($tables));
            
            if (count($tables) === 0) {
                $this->info('🔄 Lancement des migrations...');
                $this->call('migrate', ['--force' => true]);
                $this->info('✅ Migrations exécutées avec succès!');
                
                $this->info('🌱 Lancement des seeders...');
                $this->call('db:seed', ['--force' => true]);
                $this->info('✅ Seeders exécutés avec succès!');
            } else {
                $this->info('🔄 Mise à jour des migrations...');
                $this->call('migrate', ['--force' => true]);
                $this->info('✅ Migrations mises à jour avec succès!');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Erreur de connexion à la base de données: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}