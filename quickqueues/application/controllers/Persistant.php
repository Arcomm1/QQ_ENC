<?php

class Persistant
{
  private $file  = './persist.json';

  function setData($key, $data)
  {
    $entry = $this->getData();

    $entry[$key] = $data;
    
    file_put_contents($this->file, json_encode($entry), LOCK_EX);

    return $entry;
  }
  
  function getData()
  {
    if(file_exists($this->file))
    {
      $fileData = file_get_contents($this->file);

      return json_decode($fileData,true);
    }
    return [];
  }

  function hasData($key)
  {
    $entry = $this->getData();

    return $entry[$key];
  }

  function clearData()
  {
    if(file_exists($this->file))
    {
      unlink($this->file);
    }
  }
}
?>