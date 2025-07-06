<?php
/* ------------------------------ 
   Scarico le eccezioni relative all' autoloader
   ------------------------------ */
include "exceptions.php";

/**
 * Autoloader per caricare automaticamente le classi e i trait in base alla loro posizione specificata nella mappa.
 * 
 * Utilizza una mappa caricata da un file per definire la posizione dell' oggetto nel corretto file.
 * Definendo la mappa come static, garantisco che sia caricata solo una volta.
 *
 * @param string $required_entity_name Il nome della classe o del trait da caricare.
 * 
 * @throws UndefinedDependencyException Se il file associato alla classe o al trait non è indicato.
 * @throws MissingDependencyException   Se il file associato alla classe o al trait è indicato,ma non esiste.
 */
spl_autoload_register(function ($required_entity_name) {
    //inizializza la mappa solo una volta
    static $LOADER_MAP = null;
    if ($LOADER_MAP === null) $LOADER_MAP = json_decode(file_get_contents(AUTOLOADER_MAP_PATH), true);
    
    //ricavo la posizione relativa dal JSON
    $relative_path = treeTraversal($required_entity_name, $LOADER_MAP);

    //se l' elemento non è definita nella mappa
    if (!$relative_path) 
        throw new UndefinedDependencyException($required_entity_name);

    //ricavo la posizione assoluta nel filesystem fisico
    $path = absolutePath($relative_path);

    if (!file_exists($path))
        throw new MissingDependencyException($required_entity_name, $path);

    //includo il file contenente la dependency
    require $path;
});

/**
 * Definisco un algoritmo di treeTraversal per navigare la mappa descrittiva delle posizioni nei file.
 * 
 * Dato il nome di un oggetto cercato, restituisce il path assoluto del file che lo contiene.
 * 
 * @param mixed $item         Oggetto da cercare.
 * @param mixed $subtree      Nodo in cui cercarlo.
 * @param mixed $current_path Path già visitato, utilizzato per comporlo in modo ricorsivo.
 * @return mixed              Path completo del file contenitore.
 */
function treeTraversal($item, $subtree, $current_path = '')
{
    //ciclo su tutti i figli
    foreach ($subtree as $name => $sub_element) {
        //compongo il nuovo path relativo
        $new_path = $current_path . DIRECTORY_SEPARATOR . $name;

        //se il child è un array, ricorsivamente continuo la ricerca
        if (is_array($sub_element)) {
            //se al suo internoho trovato l'elemento, ritorno il path completo dell'elemento
            if ($result = treeTraversal($item, $sub_element, $new_path))
                return $result;
        } else if ($sub_element === $item) //se il child è una foglia, verifico se è l'elemento cercato
            return $current_path; //ritorno il path completo dell'elemento
    }
    //se non ho trovato in nessun figlio l'oggetto
    return false; 
}



