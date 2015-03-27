<?php


class TaskService 
{
  

  public function __construct()
  {
    $this->collectionName = 'url_tasks';
    $this->mongoConnection = new \MongoClient();
    $this->mongoDb = $this->mongoConnection->whenever_scheduler;
    
    $name = $this->collectionName;
    $this->collection = $this->mongoDb->$name;
    
    //$this->collection->ensureIndex(array('serialized_specification' => 1));
        
  }
  
  
  
  public function scheduleSingleShot($queueName, $url)
  {
    if (!$this->collection->findOne(array('url' => $url, 'queueName' => $queueName, '$or' => array(array('status'=>'not_started'),array('status'=>'running')))))
    {
      error_log('introducing new url task '.$url.' for '.$queueName);     

      $document = array(
        'url' => $url,
        'queueName' => $queueName,
        'type' => 'single_shot',
        'status' => 'not_started'
      );
      
      $this->collection->insert($document);
      
    }
    else
   {
      error_log('url task already exists, not reintroducing '.$url.' for '.$queueName);     
   }
    
  }
  

  protected function getNextSingleShot($queueName)
  {
    return $document = $this->collection->findOne(array('type'=>'single_shot', 'status'=>'not_started', 'queueName' => $queueName));
  }
  
  public function hasNextSingleShot($queueName)
  {
    if (!$this->getNextSingleShot($queueName))
    {
      return false;
    }    
    else
    {
      return true;
    }
  }

  
  public function nextSingleShot($queueName)
  {
    $document = $this->getNextSingleShot($queueName);
    if ($document)
    {
      $url = $document['url'];
      $document['status'] = 'running';
      $this->collection->save($document); 
      return $url;
    }
    else
    {
      throw new \NothingToDoException();
    }
  }
  
  public function completeSingleShot($queueName,$url)
  {
    $document = $this->collection->findOne(array('type'=>'single_shot', 'status'=>'running', 'queueName' => $queueName));
    $document['status'] = 'completed';
    $this->collection->save($document); 
    $this->collection->remove(array('url'=>$url, 'queueName' =>$queueName), array('justOne'=>true));
  }

  public function resetQueue($queueName)
  {
      $redis = new \Predis\Client();
      $redis->set('Scheduler_Queue_'.$queueName, 0);
  }

  public function executeNextSingleShot($queueName, $maxParallel, $force)
  {
    $redis = new \Predis\Client();
    
    $lastExecution = $redis->get('Scheduler_Queue_'.$queueName.'_Last_Execution');
    if (($lastExecution + 3600*4) < time())
    {
      $redis->set('Scheduler_Queue_'.$queueName, 0);
    }
    
    //if (exec('ps -A | grep avconv') == '')
    //{
    //  $redis->set('Zeitfaden_CronJob_Running', 'reset. did not find avconv process.');
    //}

    $currentParallel = $redis->get('Scheduler_Queue_'.$queueName);
    
        
    if (( $currentParallel < $maxParallel) || ( $force === 'true'))
    {
      $counter = $redis->get('Scheduler_Queue_'.$queueName);
      $counter++;
      $redis->set('Scheduler_Queue_'.$queueName, $counter);

      $redis->set('Scheduler_Queue_'.$queueName.'_Last_Execution',time());

      
      if ($this->hasNextSingleShot($queueName))
      {
        $url = $this->nextSingleShot($queueName);
        error_log('now lynxing '.$url);
        exec("lynx -dump ".$url);
        error_log('done lynxing.');
        $this->completeSingleShot($queueName,$url);
      }
      else 
      {
        error_log('nothing to do');  
      }


      $counter = $redis->get('Scheduler_Queue_'.$queueName);
      $counter--;
      $redis->set('Scheduler_Queue_'.$queueName, $counter);

    }
    else
    {
      error_log('scheduler busy.');
    }
    
  }

}


  
