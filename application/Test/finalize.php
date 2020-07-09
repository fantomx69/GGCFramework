<?php
/**
 * NOTA :
 * Questo file file deve essere utilizzato alla stregua di un distruttore di classe.
 * Le operazioni fatte in codesto file non possnon essere fatte dalla classe
 * 'GGC_Application...' perchè alcuni elementi vitali inizializzati nella init,
 * appunto nascono prima dell'istanza dell'applicazione e terminano dopo.
 */

/**
 * Disattivazione ambiente/funzionalità autoloader.
 */
GGC_Autoloader::setSmartAutoload(false);
GGC_Autoloader::setAutoloadCache(false);
GGC_Autoloader::end();

/**
 * NOTA :
 * Possimao agiungere anche la finalizzazione di tutte le instanze create
 * nella 'init'. Le altre instanze create dopo l'oggetto application, dovrebbero
 * essere distrutte da application, appunto.
 */
//...
    
?>
