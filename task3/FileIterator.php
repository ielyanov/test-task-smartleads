<?php
/**
 * Дан текстовый файл размером 2ГБ.
 * Напишите класс, реализующий интерфейс SeekableIterator, для чтения данного файла.
 *
 * Created by PhpStorm.
 * User: Igor
 * Date: 07.06.2018
 * Time: 21:45
 */
 
class FileIterator implements SeekableIterator {
	
    private $position = 0;
	/*Считываем сюда наш файл*/
    private $handle  = false; 
	/*ограничение кол-ва элементов сохраняемых в буфер*/
    const LIMIT = 40000;
    // array of {position->filepos}
    private $cache = [];
	
    private $top = 0;
	
    private $buffer = '';
	
    private $start = 0;
	
	private $finish = false;
	
	
    public function __construct($filename){
        /**
		  *проверяем наличие файла и его доступность для чтения
		  */
        if(file_exists($filename) && is_readable($filename)){
		   $this->handle = fopen($filename, 'r');
        }else{
		   //$this->handle = false;
           throw new OutOfBoundsException("Невозможно прочитать файл $filename");
        }
    }

    private function reader($pos,$found=-1)
    {
        $found = $found < 0 ? 0 : $found;
		
        $fpos = $this->cache[$found];
        fseek($this->handle, $fpos); 

        $strend = 0;
        while (!feof($this->handle) && $this->top <= $pos) {
            /*Читаем строки в пределах нашего лимита*/
            $this->buffer = fread($this->handle, self::LIMIT);
			
            $k = 0;
            if($cord = strpos($this->buffer,"\n")) {
                $found = $this->top;
                do {
                    if($cord + 1 + $strend > self::LIMIT){
                        $this->cache[$this->top] = $fpos + $k;
                        $strend = self::LIMIT - $k;
                    }
                    $k = $cord + 1;
                    $this->top++;
                }
                while($cord = strpos($this->buffer, "\n", $k));
            }
            $this->cache[$this->top] = $fpos + $k;
            $fpos += strlen($this->buffer);
        }
        if(feof($this->handle)) {
            $this->finish = true;
        }
        return $found;
    }
    /**
     * Ищем пару для чтения буфера
     * @param $pos
     * @param $found
     * @return bool|int|string
     */
    private function search($pos,&$found){
        $search = false;
        if(empty($this->cache)) return false;
        foreach($this->cache as $key => $fpos){
            if($pos >= $key){
                $found = $key;
            } else {
                $search = $key;
                break;
            }
        }
        return $search;
    }
    /**
     * Записывает строку с номеров $pos в буфер
     */
    private function findpos($pos){
        $found=-1;
        if(!$this->handle) return ;
        if(!$this->search($pos,$found)) { // нечитанное
            $found = $this->reader($pos,$found);
        }
        if($this->start != $found) {
            $fpos = $this->cache[$found];
            $this->start = $found;
            fseek($this->handle, $fpos);
            $this->buffer = fread($this->handle, self::LIMIT);
        }
    }
	/* Метод проверки правильности позиции*/
    private function isvalid($pos){
        if($pos < 0 || !$this->handle ){
            return false;
        }
        if($this->top < $pos && !$this->finish) {
            $this->findpos($pos);
        }
        return $pos <= $this->top;
    }
    /* Метод требуемый для интерфейса SeekableIterator */
    public function seek($position) {
        if(!$this->isvalid($position)){
            throw new OutOfBoundsException("Неверная позиция ($position)");
        }
        $this->position = $position;
    }
	
    public function rewind() {
        $this->position = 0;
    }
    public function current() {
        $this->findpos($this->position);
        $result = explode("\n",$this->buffer);
        return $result[$this->position-$this->start];
    }
    public function next() {
        ++$this->position;
    }
    public function key() {
        return $this->position;
    }
    public function valid() {
        return $this->isvalid($this->position);
    }
}