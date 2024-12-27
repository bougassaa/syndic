<pre>
<?php

$php = '/usr/local/php8.3/bin/php';
$composerHome = __DIR__ . '/composer';
$composerPhar = __DIR__ . '/composer.phar';

putenv("COMPOSER_HOME=$composerHome");
ini_set('max_execution_time', 300); // Temps en secondes (5 minutes ici)

if (!isset($_GET['simple'])) { // entrer si c'est pas une m-e-p simple (ex juste vider le cache)
// vérifier si composer est déjà installé
    if (!file_exists($composerPhar)) {
        // Assurez-vous que le dossier COMPOSER_HOME existe
        if (!is_dir($composerHome)) {
            mkdir($composerHome, 0777, true);
            echo "Répertoire COMPOSER_HOME créé à : $composerHome ✅\n";
        } else {
            echo "Répertoire COMPOSER_HOME déjà existant ✅\n";
        }

        $installerUrl = 'https://getcomposer.org/installer';
        $installerFile = 'composer-setup.php';

        if (file_put_contents($installerFile, file_get_contents($installerUrl))) {
            echo "Fichier '$installerFile' téléchargé avec succès ✅\n";
        } else {
            die("Échec du téléchargement du fichier '$installerFile' ❌\n");
        }

        // Vérifier le hash SHA-384
        $expectedHash = 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6';
        $actualHash = hash_file('sha384', $installerFile);

        if ($actualHash === $expectedHash) {
            echo "Installer vérifié ✅\n";
        } else {
            echo "Installer corrompu ❌\n";
            unlink($installerFile); // Supprimer le fichier si le hash ne correspond pas
            die("Fichier supprimé.\n");
        }

        // Exécuter le fichier d'installation
        exec("$php $installerFile", $output, $returnVar);

        if ($returnVar === 0) {
            echo "Composer installé avec succès ✅\n";
        } else {
            echo "Échec de l'installation de Composer ❌\n";
            print_r($output);
            exit(1);
        }

        // Supprimer le fichier d'installation
        if (unlink($installerFile)) {
            echo "Fichier '$installerFile' supprimé ✅\n";
        } else {
            echo "Impossible de supprimer le fichier '$installerFile' ⚠️\n";
        }
    } else {
        echo "Composer est déjà installé ✅\n";
    }

// Installer les dépendances en mode production
    exec("$php $composerPhar install --no-dev --optimize-autoloader", $output, $returnVar);

    if ($returnVar === 0) {
        echo "Les dépendances Symfony ont été installées avec succès ✅\n";
    } else {
        echo "Erreur lors de l'installation des dépendances Symfony ❌\n";
        print_r($output);
        exit(1);
    }
} else {
    echo "Start simple mode deployment ✅\n";
}
// Exécuter les migrations Doctrine
exec("$php bin/console doctrine:migrations:migrate --no-interaction", $output, $returnVar);

if ($returnVar === 0) {
    echo "Les migrations Doctrine ont été exécutées avec succès ✅\n";
} else {
    echo "Erreur lors de l'exécution des migrations Doctrine ❌\n";
    echo implode("\n", $output); // Affiche le retour d'exécution
    exit(1);
}
// vider le cache
exec("$php bin/console cache:clear", $output, $returnVar);

if ($returnVar === 0) {
    echo "Le cache a été vidé avec succès ✅\n";
} else {
    echo "Erreur lors du netoyage du cache ❌\n";
    echo implode("\n", $output); // Affiche le retour d'exécution
    exit(1);
}