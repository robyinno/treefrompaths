<?php
class UploadException extends Exception
{
    public function __construct($code) {
        $message = $this->codeToMessage($code);
        parent::__construct($message, $code);
    }

    private function codeToMessage($code)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "Il file caricato supera la dimensione della direttiva upload_max_filesize in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "Il file caricato supera il valore MAX_FILE_SIZE della direttiva che è stata indicata nell' HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "Il file caricato è stato solo parzialmente caricato";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "Nessun file è stato caricato";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Persa la directory temporanea";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Ha fallito di creare il file su disco";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "Il caricamento del file è stato fermato dall'estensione";
                break;

            default:
                $message = "Errore sconosciuto nell'upload";
                break;
        }
        return $message;
    }
}
