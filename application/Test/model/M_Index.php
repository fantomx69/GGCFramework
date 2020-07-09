<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of m_index
 *
 * @author Gianni Carafone
 */

class M_Index {
   /*
    * Funzione di prova per chiamata dal client/browser.
    */
   public function getServerName() {
       
       return $_SERVER['SERVER_NAME'];

   }
   
   public function getDateTime() {
        return '<br/>' . 'Salve risposta in formato HTML tramite function.' .
             ' Sono le ore : ' . date("H:i:s d m Y");

   }
   
}

?>
