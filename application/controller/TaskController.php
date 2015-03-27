<?php 


// http://flyservice.butterfurz.de/image/getFlyImages/imageSize/small?imageUrl=http://idlelive.com/wp-content/uploads/2013/06/1dd45_celebrity_incredible-images-from-national-geographics-traveler-photo-contest.jpg


class TaskController extends AbstractZeitfadenController
{
            
  protected function declareDependencies()
  {
    return array_merge(array(
      'TaskService' => 'taskService',
    ), parent::declareDependencies());  
  }


  public function scheduleAction()
  {
    error_log('scheduleAction calkled.');
    $url = $this->_request->getParam('url','');
    $queueName = $this->_request->getParam('queueName','standard');
    $this->getTaskService()->scheduleSingleShot($queueName, $url);
  }


  public function resetAction()
  {
    $queueName = $this->_request->getParam('queueName','standard');
    $this->getTaskService()->resetQueue($queueName);
  }
  
  public function executeNextSingleShotAction()
  {
    
    $queueName = $this->_request->getParam('queueName','standard');
    $maxParallel = $this->_request->getParam('maxParallel',1);
    $force = $this->_request->getParam('force','false');
   
    error_log('cronjob execute me: '.$queueName);

    $this->getTaskService()->executeNextSingleShot($queueName, $maxParallel, $force);
     
    
  }
  
}



        
        
        
        
        
        
        
        
        
        