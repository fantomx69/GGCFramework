<?php
//namespace GGC_lib\core;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_Observer
 * 
 * Interfaccia base di implementazione schema "Obsever". Qualunque classe
 * (Target) che voglia usare tale pattern, deve implementare codesta interfaccia
 * o una sua versione specializzata, e quindi il/i metodo/i da esso definito.
 * Se se desidera una versione più specializzata di questa interfaccia, la si
 * crea derivandola da questa base e creandola o nel file della classe (Source)
 * o in un file separato con lo stesso nome della classe (Source) più una
 * qualche aggiunta al nome che indichi l'interfacia specializzata observer.
 *
 * @author Gianni Carafone
 */
interface GGC_IObserver {
    function notify($obj);
}

?>
