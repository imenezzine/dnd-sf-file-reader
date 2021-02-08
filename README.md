## Description
Cette librairie devra permettre l'exécution d'une commande qui affichera une grille
d'information à partir d'un flux de données entrant de type fichier CSV.

## Les critères d'acceptation
La commande devra prendre en paramètre le chemin du fichier CSV à afficher.

Le format d'affichage des colonnes de la donnée du fichier CSV devra respecter certaines
règles de formatage listées ci-dessous:
-  created_at: format Friday, 13-Aug-2005 13:45:17 UTC
-  description: affichage de la valeur dans un format prenant en compte les potentielles
balises HTML.
-  price: affichage du prix sur une valeur décimale à deux chiffres après la virgule,
arrondi au dixième, avec une concaténation de la devise et en utilisant une virgule en
séparateur. (eg: 15,09€)
-  title: affichage d'une colonne supplémentaire "slug" dans le tableau. Correspond à la
colonne "title" en minuscule, sans espace (à remplacer par un _), sans caractères
spéciaux (à remplacer par un -)
-  is_enabled: en fonction de la valeur, afficher le libellé "Enable" ou "Disable"
-  Ajouter un paramètre à la commande pour afficher un JSON et non un tableau
formaté
-  La commande doit être exécuter tous les jours entre 7h00 et 19h00. La définition de
la fréquence de la tâche CRON sera suffisante.

## Résultat d'affichage
:point_right: format tableau :
![Alt text](table.png?raw=true "Table")
:point_right: format json
![Alt text](json.png?raw=true "Json")
## :nerd_face: Comment ça marche

### Technos utilisés : 
- `PHP7` : langage de programmation
- `Symfony4`: framework PHP
- `Composer` : outil de gestion de dépendences
- `Docker` : une technologie qui a pour but de faciliter les déploiements d'application, et la gestion du dimensionnement de l'infrastructure sous-jacente.
- `Docker Compose`: un outil qui permet de décrire (dans un fichier YAML) et gérer plusieurs conteneurs comme un ensemble de services inter-connectés
- `friendsofphp/php-cs-fixer`: outil qui analyse et corrige le code de l'application pour suivre les normes de codage PHP telles que définies dans le PSR-1, PSR-2, etc.

### Description du code :
- la classe DisplayDataFromCSVFileCommand répond à la problématique de l'exercice elle contient deux fonctions :
  1. `configure()` qui permet de configurer la commande à exécuter, pour notre cas l'argument ***path*** qui prend le chemin du fichier entrant de type CSV et une option ***format*** qui définit le format de l'affichage cli soit un tableau par défaut ou bien json;
  2. `execute()`qui englobe tout le traitement qui commence par lire le fichier CSV et se termine par afficher soit un tableau soit un json; 
- Le code contient une interface ***FileReader*** qui définit le contrat du projet : elle a pour rôle de lire un fichier et le formatter. Ce qui donne une liberté et une flexibilité sur l'implémentation.Dans notre cas, la classe ***CSVFileReader*** implémente le contrat ***FileReader*** mais si l'exercice évolue pour permettre de lire un fichier XML ou autre c'est juste implémenter une nouvelle classe ***XMLFileReader*** qui implémente notre contrat sans toucher au code du base. (***Design pattern Strategy***)
- le code contient aussi deux classes qui sont responsable du formattage de l'affichage suivant l'option ***format*** : ***JsonFormatter*** et ***TableFormatter*** (pareil ici si le besoin évolue pour supporter d'autre formats, on peut utiliser par exemple le design pattern **Factory**)

#### :point_right:  Extrait de code:
```php
class DisplayDataFromCSVFileCommand extends Command
{
    /**
     * @var FileReaderInterface
     */
    private $fileReader;

    /**
     * @var JsonFormatter
     */
    private $jsonFormatter;

    /**
     * @var TableFormatter
     */
    private $tableFormatter;

    public function __construct(FileReaderInterface $fileReader, JsonFormatter $jsonFormatter, TableFormatter $tableFormatter, string $name = null)
    {
        parent::__construct($name);

        $this->fileReader = $fileReader;
        $this->jsonFormatter = $jsonFormatter;
        $this->tableFormatter = $tableFormatter;
    }

    protected static $defaultName = 'app:display-data-csv-file';

    protected function configure()
    {
        $this
            ->setDescription('Display data form csv file with a specific format')
            ->addArgument('path', InputArgument::REQUIRED, 'path of file')
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'display format');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $path = $input->getArgument('path');
        $format = $input->getOption('format');
        $rows = $this->fileReader->getLines($path);

        if ('json' == $format) {
            $io->text($this->jsonFormatter->render($this->fileReader->format($rows)));
        } else {
            $this->tableFormatter->render($output, $this->fileReader->format($rows));
        }

        return 0;
    }
}
```
## :bulb: Comment lancer le projet:
Lancer les commandes suivantes :
- composer install
- php bin/console app:display-data-csv-file ****path csv file****: sans l'option **format**, l'affichage par défaut c'est un tableau 
- php bin/console app:display-data-csv-file ****path csv file**** --format=json: afficher les données sous format json (on peut utiliser juste le raccourci **-f json**)
- php bin/console app:display-data-csv-file --help: pour afficher plus de détails sur comment exécuter la commande
**N.B** : La commande doit être exécuter tous les jours entre 7h00 et 19h00. La définition de
la fréquence de la tâche CRON qui sera utilisée est sous ce format standard: `* 7-19 * * * <commande à éxecuter>`
##### Explication du format CRON
```php
# ┌───────────── minute (0 - 59)
# │ ┌───────────── hour (0 - 23)
# │ │ ┌───────────── day of the month (1 - 31)
# │ │ │ ┌───────────── month (1 - 12)
# │ │ │ │ ┌───────────── day of the week (0 - 6) (Sunday to Saturday;
# │ │ │ │ │                                   7 is also Sunday on some systems)
# │ │ │ │ │
# │ │ │ │ │
# * * * * * <command to execute>
```
