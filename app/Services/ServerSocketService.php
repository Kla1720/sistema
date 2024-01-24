<?php
namespace App\Services;

use Exception;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
class ServerSocketService{

    private $production = null;
    private $port = null;
    private $cliente = null;
    private $pm2 = null;

    function __construct($cliente = null, $production = false,$port = 3000){
        $this->production = $production;
        $this->port = $port;
        $this->cliente = is_null($cliente) ? uniqid() : $cliente;
        $this->pm2 = env('RUTA_PM2', false);
    }

    private function getConfig($production = false, $port){

        $prod = $production ? 'true' : 'false';
        $cert =env('CERT_SSL', false);
        $key =env('CERT_KEY', false);

        return "module.exports = config = {
            production:{$prod},
            port: {$port},
            key: '{$key}',
            cert: '{$cert}'
        }";
    }


    private function getFolder() : string{

        $folder = base_path('socket'.DIRECTORY_SEPARATOR .$this->cliente);

        if(!is_dir($folder)) mkdir($folder);

        return $folder;

    }

    private function getConfigFile(){
        return $this->getFolder().DIRECTORY_SEPARATOR.'config.js';
    }

    private function getIndexFile(){
        return $this->getFolder().DIRECTORY_SEPARATOR.'index.js';
    }

    private function existFile(){
        return file_exists($this->getIndexFile());
    }

    private function create(){
        $indexFile = base_path('socket'.DIRECTORY_SEPARATOR.'index.js.base');
        $toIndex = $this->getIndexFile();
        if(copy($indexFile, $toIndex)){
            return true;
        }
        throw new Exception('No se pudo crear el archivo');
    }

    public function run(){

        $this->setConfig($this->port, $this->production);

        $this->create();

        $this->start();
    }

    public function setConfig($port, $production = false ){
        $to = $this->getConfigFile();
        $fp = fopen($to, 'w');
        fwrite($fp, $this->getConfig($production, $port));
        fclose($fp);
    }

    public function start(){
        $toIndex = $this->getIndexFile();
        $process = new Process("sudo {$this->pm2} start {$toIndex} --name={$this->cliente}");
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

    }

    public function stop(){
        $process = new Process("sudo {$this->pm2} stop {$this->cliente}");
        $process->run();
    }

    public function restart(){
        $this->stop();

        if(!$this->existFile()){
            $this->create();
        }

        $this->start();
    }

    public function destroy(){

        $stopProcess = new Process("sudo {$this->pm2} delete {$this->cliente}");
        $stopProcess->run();
    }




}
